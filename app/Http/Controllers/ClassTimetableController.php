<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\ClassTimetableModel;
use App\Models\SubjectModel;
use App\Models\User;
use App\Models\WeekModel;
use App\Notifications\TimetableCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ClassTimetableController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────
    // ADMIN — liste / formulaire de gestion
    // ─────────────────────────────────────────────────────────────────────

    public function list(Request $request)
    {
        // Matières de la classe sélectionnée (si fournie)
        $subjects = [];
        if (!empty($request->class_id)) {
            $subjectRows = ClassTimetableModel::getSubject($request->class_id);
            foreach ($subjectRows as $s) {
                $subjects[] = ['id' => $s->subject_id, 'name' => $s->subject_name];
            }
        }

        // Grille semaine
        $weeks   = WeekModel::getAllWeek();
        $week    = [];

        foreach ($weeks as $weekValue) {
            $entry = [
                'week_id'     => $weekValue->id,
                'week_name'   => $weekValue->name,
                'start_time'  => '',
                'end_time'    => '',
                'room_number' => '',
            ];

            if (!empty($request->class_id) && !empty($request->subject_id)) {
                $slot = ClassTimetableModel::getClassTimetable(
                    $request->class_id,
                    $request->subject_id,
                    $weekValue->id
                );
                if ($slot) {
                    $entry['start_time']  = $slot->start_time;
                    $entry['end_time']    = $slot->end_time;
                    $entry['room_number'] = $slot->room_number;
                }
            }

            $week[] = $entry;
        }

        return Inertia::render('Admin/Timetable/Index', [
            'classes'         => ClassModel::getClass(),
            'subjects'        => $subjects,
            'week'            => $week,
            'selectedClass'   => $request->class_id,
            'selectedSubject' => $request->subject_id,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // AJAX — matières actives d'une classe (pour le select dynamique)
    // ─────────────────────────────────────────────────────────────────────

    public function getSubject(Request $request): \Illuminate\Http\JsonResponse
    {
        if (empty($request->class_id)) {
            return response()->json(['subjects' => []]);
        }

        $rows = ClassTimetableModel::getSubject($request->class_id);

        $subjects = $rows->map(fn($s) => [
            'id'   => $s->subject_id,
            'name' => $s->subject_name,
        ])->values();

        return response()->json(['subjects' => $subjects]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // ADMIN — enregistrer l'emploi du temps
    // ─────────────────────────────────────────────────────────────────────

    public function add(Request $request)
    {
        try {
            // Supprimer les anciens créneaux (soft-delete via is_delete)
            ClassTimetableModel::where('class_id', $request->class_id)
                ->where('subject_id', $request->subject_id)
                ->update(['is_delete' => 1]);

            foreach ($request->timetable as $slot) {
                if (
                    !empty($slot['week_id']) &&
                    !empty($slot['start_time']) &&
                    !empty($slot['end_time'])
                ) {
                    ClassTimetableModel::create([
                        'class_id'    => intval($request->class_id),
                        'subject_id'  => intval($request->subject_id),
                        'week_id'     => intval($slot['week_id']),
                        'start_time'  => $slot['start_time'],
                        'end_time'    => $slot['end_time'],
                        'room_number' => $slot['room_number'] ?? '',
                    ]);
                }
            }

            // Notifier les élèves de la classe et leurs parents
            try {
                $class   = ClassModel::getSingle($request->class_id);
                $subject = SubjectModel::getSingle($request->subject_id);

                if ($class && $subject) {
                    $className   = $class->name;
                    $subjectName = $subject->name;

                    // Tous les élèves actifs de la classe
                    $students = User::where('class_id', $request->class_id)
                        ->where('user_type', 3)
                        ->where('is_delete', 0)
                        ->where('status', 1)
                        ->get();

                    $notifiedParents = [];

                    foreach ($students as $student) {
                        $student->notify(new TimetableCreatedNotification($className, $subjectName));

                        // Notifier le parent une seule fois (même parent peut avoir plusieurs enfants dans la classe)
                        if ($student->parent_id && !in_array($student->parent_id, $notifiedParents)) {
                            $parent = User::find($student->parent_id);
                            $parent?->notify(new TimetableCreatedNotification($className, $subjectName));
                            $notifiedParents[] = $student->parent_id;
                        }
                    }

                    // Notifier les professeurs assignés à cette classe
                    $teachers = User::join('class_teacher', 'class_teacher.teacher_id', '=', 'users.id')
                        ->where('class_teacher.class_id', $request->class_id)
                        ->where('class_teacher.is_delete', 0)
                        ->where('users.is_delete', 0)
                        ->where('users.status', 1)
                        ->select('users.*')
                        ->get();

                    foreach ($teachers as $teacher) {
                        $teacher->notify(new TimetableCreatedNotification($className, $subjectName));
                    }
                }
            } catch (\Exception $notifEx) {
                Log::warning("Timetable notification failed: " . $notifEx->getMessage());
            }

            return redirect('admin/class_timetable/list')
                ->with('success', 'Emploi du temps enregistré avec succès.');

        } catch (\Exception $e) {
            Log::error('ClassTimetable add: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // ÉTUDIANT — emploi du temps (grille matricielle)
    // ─────────────────────────────────────────────────────────────────────

    public function studentTimetable()
    {
        $class_id = Auth::user()->class_id;
        $timetable = $this->buildStudentTimetableMatrix($class_id);

        return Inertia::render('Student/Timetable/Index', [
            'timetable' => $timetable,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PROFESSEUR — emploi du temps d'une matière pour une classe
    // ─────────────────────────────────────────────────────────────────────

    public function myClassSubjectTimetable($class_id, $subject_id)
    {
        $weeks    = WeekModel::getAllWeek();
        $weekData = [];

        foreach ($weeks as $w) {
            $slot = ClassTimetableModel::getClassTimetable($class_id, $subject_id, $w->id);
            $weekData[] = [
                'week_id'     => $w->id,
                'week_name'   => $w->name,
                'start_time'  => $slot?->start_time  ?? '',
                'end_time'    => $slot?->end_time    ?? '',
                'room_number' => $slot?->room_number ?? '',
            ];
        }

        return Inertia::render('Teacher/Timetable/Index', [
            'classInfo' => ClassModel::getSingle($class_id),
            'subject'   => SubjectModel::getSingle($subject_id),
            'timetable' => [['week' => $weekData]],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PARENT — emploi du temps complet d'une classe (JSON pour le modal)
    // ─────────────────────────────────────────────────────────────────────

    public function parentClassFullTimetable($class_id): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'timetable' => $this->buildStudentTimetableMatrix((int) $class_id),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PARENT — emploi du temps d'une matière pour l'enfant
    // ─────────────────────────────────────────────────────────────────────

    public function parentStudentSubjectTimetable($class_id, $subject_id, $student_id)
    {
        $weeks    = WeekModel::getAllWeek();
        $weekData = [];

        foreach ($weeks as $w) {
            $slot = ClassTimetableModel::getClassTimetable($class_id, $subject_id, $w->id);
            $weekData[] = [
                'week_id'     => $w->id,
                'week_name'   => $w->name,
                'start_time'  => $slot?->start_time  ?? '',
                'end_time'    => $slot?->end_time    ?? '',
                'room_number' => $slot?->room_number ?? '',
            ];
        }

        return Inertia::render('Parent/Timetable/Index', [
            'class'     => ClassModel::getSingle($class_id),
            'subject'   => SubjectModel::getSingle($subject_id),
            'student'   => User::getSingle($student_id),
            'timetable' => [['week' => $weekData]],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // HELPER — grille matricielle pour un élève
    // Structure : [ { name, week: [ { week_id, week_name, day, start_time, end_time, room_number } ] } ]
    // ─────────────────────────────────────────────────────────────────────

    private function buildStudentTimetableMatrix(int $class_id): array
    {
        $subjects  = ClassTimetableModel::getSubject($class_id);
        $weeks     = WeekModel::getAllWeek();
        $result    = [];

        foreach ($subjects as $subject) {
            $weekRows = [];

            foreach ($weeks as $w) {
                $slot = ClassTimetableModel::getClassTimetable(
                    $subject->class_id,
                    $subject->subject_id,
                    $w->id
                );

                $weekRows[] = [
                    'week_id'     => $w->id,
                    'week_name'   => $w->name,
                    'day'         => $w->day,
                    'start_time'  => $slot?->start_time  ?? '',
                    'end_time'    => $slot?->end_time    ?? '',
                    'room_number' => $slot?->room_number ?? '',
                ];
            }

            $result[] = [
                'name' => $subject->subject_name,
                'week' => $weekRows,
            ];
        }

        return $result;
    }
}
