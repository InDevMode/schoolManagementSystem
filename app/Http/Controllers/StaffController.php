<?php

namespace App\Http\Controllers;

use App\Models\DeletionLogModel;
use App\Models\LeaveTypeModel;
use App\Models\StaffEventModel;
use App\Models\StaffLeaveModel;
use App\Models\StaffModel;
use App\Models\User;
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
        return Inertia::render('Admin/Staff/Index', [
            'staff'      => StaffModel::getAll(15),
            'roleLabels' => StaffModel::$roles,
            'users'      => User::select('id', 'name', 'last_name', 'user_type')
                ->whereIn('user_type', [1, 2])   // admin + teacher uniquement
                ->where('is_delete', 0)
                ->where('status', 1)
                ->orderBy('last_name')
                ->get(),
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'role'       => 'required|string',
            'status'     => 'required|in:active,inactive,suspended',
        ]);

        try {
            $staff             = new StaffModel;
            $staff->first_name = trim($request->first_name);
            $staff->last_name  = trim($request->last_name);
            $staff->email      = trim($request->email ?? '');
            $staff->phone      = trim($request->phone ?? '');
            $staff->role       = $request->role;
            $staff->status     = $request->status;
            $staff->hire_date  = $request->hire_date ?? null;
            $staff->end_date   = $request->end_date ?? null;
            $staff->address    = $request->address ?? null;
            $staff->gender     = $request->gender ?? null;
            $staff->user_id    = $request->user_id ?? null;
            $staff->created_by = Auth::id();
            $staff->save();

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
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'role'       => 'required|string',
            'status'     => 'required|in:active,inactive,suspended',
        ]);

        try {
            $staff             = StaffModel::getSingle($id);
            if (!$staff) abort(404);

            $staff->first_name = trim($request->first_name);
            $staff->last_name  = trim($request->last_name);
            $staff->email      = trim($request->email ?? '');
            $staff->phone      = trim($request->phone ?? '');
            $staff->role       = $request->role;
            $staff->status     = $request->status;
            $staff->hire_date  = $request->hire_date ?? $staff->hire_date;
            $staff->end_date   = $request->end_date ?? null;
            $staff->address    = $request->address ?? $staff->address;
            $staff->gender     = $request->gender ?? $staff->gender;
            $staff->user_id    = $request->user_id ?? $staff->user_id;
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
        return Inertia::render('Admin/Staff/LeaveTypes', [
            'leaveTypes' => LeaveTypeModel::getAllPaginated(15),
        ]);
    }

    public function leaveTypeCreate(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);

        try {
            $existing = LeaveTypeModel::getNameSingle($request->name);
            if ($existing) {
                return redirect()->back()->with('error', 'Un type de congé avec ce nom existe déjà.');
            }

            $lt              = new LeaveTypeModel;
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
            $existing = LeaveTypeModel::checkNameSingle($request->name, $id);
            if ($existing) {
                return redirect()->back()->with('error', 'Un type de congé avec ce nom existe déjà.');
            }

            $lt              = LeaveTypeModel::getSingle($id);
            if (!$lt) abort(404);

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
        return Inertia::render('Admin/Staff/Leaves', [
            'leaves'     => StaffLeaveModel::getAll(15),
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
        return Inertia::render('Admin/Staff/Events', [
            'events'      => StaffEventModel::getAll(15),
            'typeLabels'  => StaffEventModel::$typeLabels,
            'typeColors'  => StaffEventModel::$typeColors,
            'calendarEvents' => StaffEventModel::getCalendarEvents(),
        ]);
    }

    public function eventCreate(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:200',
            'event_date' => 'required|date',
            'event_type' => 'required|in:academic,cultural,administrative,exam,ceremony,trip',
        ]);

        try {
            $event             = new StaffEventModel;
            $event->title      = trim($request->title);
            $event->description= trim($request->description ?? '');
            $event->event_date = $request->event_date;
            $event->start_time = $request->start_time ?? null;
            $event->end_time   = $request->end_time ?? null;
            $event->event_type = $request->event_type;
            $event->location   = trim($request->location ?? '');
            $event->created_by = Auth::id();
            $event->save();

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
            'event_type' => 'required|in:academic,cultural,administrative,exam,ceremony,trip',
        ]);

        try {
            $event             = StaffEventModel::getSingle($id);
            if (!$event) abort(404);

            $event->title      = trim($request->title);
            $event->description= trim($request->description ?? '');
            $event->event_date = $request->event_date;
            $event->start_time = $request->start_time ?? null;
            $event->end_time   = $request->end_time ?? null;
            $event->event_type = $request->event_type;
            $event->location   = trim($request->location ?? '');
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

        DeletionLogModel::log('staff_events', $event->id, $event->toArray());
        $event->is_delete = 1;
        $event->save();

        return redirect()->back()->with('success', 'Événement supprimé.');
    }
}
