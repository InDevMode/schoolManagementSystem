<?php

namespace App\Http\Controllers;

use App\Models\DeletionLogModel;
use App\Models\EventTypeCustomModel;
use App\Models\LeaveTypeModel;
use App\Models\StaffEventModel;
use App\Models\StaffLeaveModel;
use App\Models\StaffModel;
use App\Models\User;
use App\Notifications\LeaveStatusChangedNotification;
use App\Notifications\NewEventNotification;
use App\Notifications\StaffAddedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class StaffController extends Controller
{
    // ══════════════════════════════════════════════════════════════════════════
    // PERSONNEL (STAFF)
    // ══════════════════════════════════════════════════════════════════════════

    public function list()
    {
        $perPage      = min((int) request('per_page', 15), 100);
        $currentUser  = Auth::user();
        $isSuperAdmin = $currentUser && (int) $currentUser->user_type === 0;

        // Requête utilisateurs disponibles pour le select du modal
        $usersQuery = User::select('id', 'name', 'last_name', 'user_type', 'school_id')
            ->whereIn('user_type', [1, 2])   // admin + teacher uniquement
            ->where('is_delete', 0)
            ->where('status', 1)
            ->orderBy('last_name');

        // Un admin ne voit que les utilisateurs de son école
        if (! $isSuperAdmin && $currentUser) {
            $usersQuery->where('school_id', $currentUser->school_id);
        }

        return Inertia::render('Admin/Staff/Index', [
            'staff'        => StaffModel::getAll($perPage),
            'roleLabels'   => StaffModel::$roles,
            'users'        => $usersQuery->get(),
            'isSuperAdmin' => $isSuperAdmin,
            'schools'      => $isSuperAdmin
                ? \App\Models\School::where('is_delete', 0)->select('id', 'school_name')->orderBy('school_name')->get()
                : [],
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role'    => 'required|string',
            'status'  => 'required|in:active,inactive,suspended',
        ]);

        try {
            // Récupérer les infos depuis le user lié
            $linkedUser = User::findOrFail((int) $request->user_id);

            $staff             = new StaffModel;
            $staff->role            = $request->role;
            $staff->status          = $request->status;
            $staff->hire_date       = $request->hire_date ?? null;
            $staff->end_date        = $request->end_date ?? null;
            $staff->employee_number = $request->employee_number ?? null;
            $staff->department      = $request->department ?? null;
            $staff->bio             = $request->bio ?? null;
            $staff->user_id         = $linkedUser->id;
            // school_id : toujours déduit de l'utilisateur lié (jamais envoyé manuellement)
            $staff->school_id       = $linkedUser->school_id ?? Auth::user()?->school_id;
            $staff->created_by      = Auth::id();
            $staff->save();

            // Notification in-app à l'utilisateur lié s'il existe
            try {
                if ($staff->user_id) {
                    $user = User::find($staff->user_id);
                    if ($user) {
                        $roleLabel = StaffModel::$roles[$staff->role] ?? $staff->role;
                        $hireDate  = $staff->hire_date
                            ? \Carbon\Carbon::parse($staff->hire_date)->format('d-m-Y')
                            : '';
                        $user->notify(new StaffAddedNotification($roleLabel, $hireDate));
                    }
                }
            } catch (\Exception $notifEx) {
                Log::warning("Staff create notification failed: " . $notifEx->getMessage());
            }

            return redirect()->back()->with('success', 'Membre du personnel ajouté avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur création staff : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    public function edit(int $id)
    {
        $staff = StaffModel::getSingle($id);
        if (!$staff) abort(404);
        return response()->json(['staff' => $staff]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role'    => 'required|string',
            'status'  => 'required|in:active,inactive,suspended',
        ]);

        try {
            $staff = StaffModel::getSingle($id);
            if (!$staff) abort(404);

            // Resynchroniser depuis le user lié si le user_id change
            $linkedUser = User::findOrFail((int) $request->user_id);

            $staff->role            = $request->role;
            $staff->status          = $request->status;
            $staff->hire_date       = $request->hire_date ?? $staff->hire_date;
            $staff->end_date        = $request->end_date ?? null;
            $staff->employee_number = $request->employee_number ?? $staff->employee_number;
            $staff->department      = $request->department ?? $staff->department;
            $staff->bio             = $request->bio ?? $staff->bio;
            $staff->user_id         = $linkedUser->id;
            $staff->school_id       = $linkedUser->school_id ?? $staff->school_id;
            $staff->save();

            return redirect()->back()->with('success', 'Personnel mis à jour avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur mise à jour staff : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    public function delete(int $id)
    {
        $staff = StaffModel::getSingle($id);
        if (!$staff) abort(404);

        DeletionLogModel::log('staff', $staff->id, $staff->toArray());
        $staff->is_delete = 1;
        $staff->save();

        return redirect()->back()->with('success', 'Personnel supprimé avec succès.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // TYPES DE CONGÉS
    // ══════════════════════════════════════════════════════════════════════════

    public function leaveTypeList()
    {
        $perPage = min((int) request('per_page', 15), 100);
        return Inertia::render('Admin/Staff/LeaveTypes', [
            'leaveTypes' => LeaveTypeModel::getAllPaginated($perPage),
        ]);
    }

    public function leaveTypeCreate(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);

        try {
            $user     = Auth::user();
            $schoolId = (int) $user->user_type === 0
                ? ($request->school_id ?? null)
                : $user->school_id;

            $existing = LeaveTypeModel::getNameSingle($request->name, $schoolId);
            if ($existing) {
                return redirect()->back()->with('error', 'Un type de congé avec ce nom existe déjà dans cette école.');
            }

            $lt              = new LeaveTypeModel;
            $lt->school_id   = $schoolId;
            $lt->name        = trim($request->name);
            $lt->description = trim($request->description ?? '');
            $lt->color       = $request->color ?? '#6366f1';
            $lt->save();

            return redirect()->back()->with('success', 'Type de congé créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur création type congé : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    public function leaveTypeEdit(int $id)
    {
        $lt = LeaveTypeModel::getSingle($id);
        if (!$lt) abort(404);
        return response()->json(['leaveType' => $lt]);
    }

    public function leaveTypeUpdate(Request $request, int $id)
    {
        $request->validate(['name' => 'required|string|max:100']);

        try {
            $lt = LeaveTypeModel::getSingle($id);
            if (!$lt) abort(404);

            // Vérifier que l'admin ne modifie que les types de son école
            $user = Auth::user();
            if ((int) $user->user_type !== 0 && (int) $lt->school_id !== (int) $user->school_id) {
                return redirect()->back()->with('error', 'Accès refusé à ce type de congé.');
            }

            $existing = LeaveTypeModel::checkNameSingle($request->name, $id, $lt->school_id);
            if ($existing) {
                return redirect()->back()->with('error', 'Un type de congé avec ce nom existe déjà dans cette école.');
            }

            $lt->name        = trim($request->name);
            $lt->description = trim($request->description ?? '');
            $lt->color       = $request->color ?? $lt->color;
            $lt->save();

            return redirect()->back()->with('success', 'Type de congé mis à jour.');
        } catch (\Exception $e) {
            Log::error("Erreur mise à jour type congé : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    public function leaveTypeDelete(int $id)
    {
        $lt = LeaveTypeModel::getSingle($id);
        if (!$lt) abort(404);

        $lt->is_delete = 1;
        $lt->save();

        return redirect()->back()->with('success', 'Type de congé supprimé.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // CONGÉS DU PERSONNEL
    // ══════════════════════════════════════════════════════════════════════════

    public function leaveList()
    {
        $perPage = min((int) request('per_page', 15), 100);
        return Inertia::render('Admin/Staff/Leaves', [
            'leaves'     => StaffLeaveModel::getAll($perPage),
            'leaveTypes' => LeaveTypeModel::getAll(),
            'staff'      => StaffModel::getAllActive(),
            'pendingCount' => StaffLeaveModel::getPendingCount(),
        ]);
    }

    public function leaveCreate(Request $request)
    {
        $request->validate([
            'staff_id'      => 'required|integer',
            'leave_type_id' => 'required|integer',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $leave                = new StaffLeaveModel;
            $leave->staff_id      = $request->staff_id;
            $leave->leave_type_id = $request->leave_type_id;
            $leave->start_date    = $request->start_date;
            $leave->end_date      = $request->end_date ?? null;
            $leave->reason        = trim($request->reason ?? '');
            $leave->status        = 'pending';
            $leave->save();

            return redirect()->back()->with('success', 'Demande de congé enregistrée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur création congé : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    public function leaveApprove(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);

        try {
            $leave = StaffLeaveModel::getSingle($id);
            if (!$leave) abort(404);

            $leave->status      = $request->status;
            $leave->approved_by = Auth::id();
            $leave->approved_at = now();
            $leave->admin_note  = trim($request->admin_note ?? '');
            $leave->save();

            // Notifier le membre du personnel concerné (via son compte User si lié)
            try {
                $staff = StaffModel::find($leave->staff_id);
                if ($staff && $staff->user_id) {
                    $user = User::find($staff->user_id);
                    if ($user) {
                        $leaveType = LeaveTypeModel::find($leave->leave_type_id);
                        $leaveTypeName = $leaveType?->name ?? 'Congé';
                        $startDate = \Carbon\Carbon::parse($leave->start_date)->format('d-m-Y');

                        $user->notify(new LeaveStatusChangedNotification(
                            $request->status,
                            $startDate,
                            $leaveTypeName,
                            $leave->admin_note
                        ));
                    }
                }
            } catch (\Exception $notifEx) {
                Log::warning("Leave approve notification failed for leave #{$id}: " . $notifEx->getMessage());
            }

            $msg = $request->status === 'approved' ? 'Congé approuvé.' : 'Congé rejeté.';
            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            Log::error("Erreur approbation congé : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    public function leaveDelete(int $id)
    {
        $leave = StaffLeaveModel::getSingle($id);
        if (!$leave) abort(404);

        DeletionLogModel::log('staff_leaves', $leave->id, $leave->toArray());
        $leave->is_delete = 1;
        $leave->save();

        return redirect()->back()->with('success', 'Congé supprimé.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ÉVÉNEMENTS DE L'ÉCOLE
    // ══════════════════════════════════════════════════════════════════════════

    public function eventList()
    {
        $perPage = min((int) request('per_page', 15), 100);
        return Inertia::render('Admin/Staff/Events', [
            'events'          => StaffEventModel::getAll($perPage),
            'typeLabels'      => StaffEventModel::$typeLabels,
            'typeColors'      => StaffEventModel::$typeColors,
            'calendarEvents'  => StaffEventModel::getCalendarEvents(),
            // Types personnalisés de l'école pour les selects
            'customEventTypes' => EventTypeCustomModel::getForCurrentSchool(),
        ]);
    }

    public function eventCreate(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:200',
            'event_date' => 'required|date',
            'event_type' => 'required|in:academic,cultural,administrative,exam,ceremony,trip,custom',
        ]);

        try {
            $currentUser = Auth::user();
            $schoolId    = (int) $currentUser->user_type === 0
                ? ($request->school_id ?? null)
                : $currentUser->school_id;

            $event                      = new StaffEventModel;
            $event->school_id           = $schoolId;
            $event->title               = trim($request->title);
            $event->description         = trim($request->description ?? '');
            $event->event_date          = $request->event_date;
            $event->start_time          = $request->start_time ?? null;
            $event->end_time            = $request->end_time ?? null;
            $event->event_type          = $request->event_type;
            $event->custom_event_type_id = $request->custom_event_type_id ?? null;
            $event->location            = trim($request->location ?? '');
            $event->created_by          = $currentUser->id;
            $event->save();

            // Notifier uniquement les utilisateurs actifs de la même école
            try {
                $eventDate = \Carbon\Carbon::parse($event->event_date)->format('d-m-Y');
                $usersQuery = User::where('is_delete', 0)->where('status', 1);

                // Scoping : notifier seulement les utilisateurs de cette école
                if ($schoolId) {
                    $usersQuery->where('school_id', $schoolId);
                }

                $users = $usersQuery->get();
                foreach ($users as $u) {
                    $u->notify(new NewEventNotification($event->title, $eventDate, $event->event_type));
                }
            } catch (\Exception $notifEx) {
                Log::warning("Event create notification failed: " . $notifEx->getMessage());
            }

            return redirect()->back()->with('success', 'Événement créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur création événement : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    public function eventEdit(int $id)
    {
        $event = StaffEventModel::getSingle($id);
        if (!$event) abort(404);
        return response()->json(['event' => $event]);
    }

    public function eventUpdate(Request $request, int $id)
    {
        $request->validate([
            'title'      => 'required|string|max:200',
            'event_date' => 'required|date',
            'event_type' => 'required|in:academic,cultural,administrative,exam,ceremony,trip,custom',
        ]);

        try {
            $event = StaffEventModel::getSingle($id);
            if (!$event) abort(404);

            // Vérifier que l'admin ne modifie que les événements de son école
            $user = Auth::user();
            if ((int) $user->user_type !== 0 && (int) $event->school_id !== (int) $user->school_id) {
                return redirect()->back()->with('error', 'Accès refusé à cet événement.');
            }

            $event->title               = trim($request->title);
            $event->description         = trim($request->description ?? '');
            $event->event_date          = $request->event_date;
            $event->start_time          = $request->start_time ?? null;
            $event->end_time            = $request->end_time ?? null;
            $event->event_type          = $request->event_type;
            $event->custom_event_type_id = $request->custom_event_type_id ?? null;
            $event->location            = trim($request->location ?? '');
            $event->save();

            return redirect()->back()->with('success', 'Événement mis à jour.');
        } catch (\Exception $e) {
            Log::error("Erreur mise à jour événement : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    public function eventDelete(int $id)
    {
        $event = StaffEventModel::getSingle($id);
        if (!$event) abort(404);

        // Vérifier que l'admin ne supprime que les événements de son école
        $user = Auth::user();
        if ((int) $user->user_type !== 0 && (int) $event->school_id !== (int) $user->school_id) {
            return redirect()->back()->with('error', 'Accès refusé à cet événement.');
        }

        DeletionLogModel::log('staff_events', $event->id, $event->toArray());
        $event->is_delete = 1;
        $event->save();

        return redirect()->back()->with('success', 'Événement supprimé.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // TYPES D'ÉVÉNEMENTS PERSONNALISÉS (par école)
    // ══════════════════════════════════════════════════════════════════════════

    public function customEventTypeList()
    {
        $perPage = min((int) request('per_page', 15), 100);
        return Inertia::render('Admin/Staff/CustomEventTypes', [
            'customEventTypes' => EventTypeCustomModel::getAllPaginated($perPage),
        ]);
    }

    public function customEventTypeCreate(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'color' => 'nullable|string|max:7',
        ]);

        try {
            $user     = Auth::user();
            $schoolId = (int) $user->user_type === 0
                ? ($request->school_id ?? null)
                : $user->school_id;

            if (! $schoolId) {
                return redirect()->back()->with('error', 'Impossible de déterminer l\'école.');
            }

            // Unicité du nom dans l'école
            $existing = EventTypeCustomModel::getByNameAndSchool($request->name, $schoolId);
            if ($existing) {
                return redirect()->back()->with('error', 'Un type d\'événement avec ce nom existe déjà dans cette école.');
            }

            $type              = new EventTypeCustomModel;
            $type->school_id   = $schoolId;
            $type->name        = trim($request->name);
            $type->color       = $request->color ?? '#6366f1';
            $type->description = trim($request->description ?? '');
            $type->created_by  = $user->id;
            $type->save();

            return redirect()->back()->with('success', 'Type d\'événement créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur création type événement : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    public function customEventTypeEdit(int $id)
    {
        $type = EventTypeCustomModel::getSingle($id);
        if (!$type) abort(404);

        // Vérifier que l'admin ne voit que les types de son école
        $user = Auth::user();
        if ((int) $user->user_type !== 0 && (int) $type->school_id !== (int) $user->school_id) {
            abort(403);
        }

        return response()->json(['customEventType' => $type]);
    }

    public function customEventTypeUpdate(Request $request, int $id)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'color' => 'nullable|string|max:7',
        ]);

        try {
            $type = EventTypeCustomModel::getSingle($id);
            if (!$type) abort(404);

            $user = Auth::user();
            if ((int) $user->user_type !== 0 && (int) $type->school_id !== (int) $user->school_id) {
                return redirect()->back()->with('error', 'Accès refusé à ce type d\'événement.');
            }

            // Unicité du nom dans l'école (hors enregistrement courant)
            $existing = EventTypeCustomModel::getByNameAndSchool($request->name, $type->school_id, $id);
            if ($existing) {
                return redirect()->back()->with('error', 'Un type d\'événement avec ce nom existe déjà dans cette école.');
            }

            $type->name        = trim($request->name);
            $type->color       = $request->color ?? $type->color;
            $type->description = trim($request->description ?? '');
            $type->save();

            return redirect()->back()->with('success', 'Type d\'événement mis à jour.');
        } catch (\Exception $e) {
            Log::error("Erreur mise à jour type événement : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    public function customEventTypeDelete(int $id)
    {
        $type = EventTypeCustomModel::getSingle($id);
        if (!$type) abort(404);

        $user = Auth::user();
        if ((int) $user->user_type !== 0 && (int) $type->school_id !== (int) $user->school_id) {
            return redirect()->back()->with('error', 'Accès refusé à ce type d\'événement.');
        }

        $type->is_delete = 1;
        $type->save();

        return redirect()->back()->with('success', 'Type d\'événement supprimé.');
    }
}
