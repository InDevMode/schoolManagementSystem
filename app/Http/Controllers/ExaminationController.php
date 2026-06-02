<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\ClassTeacherModel;
use App\Models\ExaminationModel;
use App\Models\MarkRegisterModel;
use App\Models\MarksGradeModel;
use App\Models\PeriodModel;
use App\Models\ScheduleModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ExaminationController extends Controller
{
    public function list()
    {
        return Inertia::render('Admin/Exams/Index', [
            'exams' => ExaminationModel::getExaminations(15),
            'periods' => PeriodModel::getAllPeriods(),
        ]);
    }

    public function create(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $existingClass = ExaminationModel::getNameSingle($request->name);

            if ($existingClass) {
                return redirect()->back()->with('error', 'Une évaluation avec ce nom existe déjà.');
            }

            $exam = new ExaminationModel;
            $exam->name = trim($request->name);
            $exam->start_date = trim($request->start_date);
            $exam->end_date = trim($request->end_date);
            $exam->period_id = intval($request->period_id);
            $exam->status = intval($request->status);
            $exam->created_by = auth()->user()->id;
            $exam->save();

            return redirect('admin/examinations/exam/list')->with('success', 'Cette évaluation a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'une évaluation : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function edit($id)
    {
        $exam = ExaminationModel::getSingle($id);
        if (!$exam) {
            abort(404);
        }
        return response()->json(['exam' => $exam, 'periods' => PeriodModel::getAllPeriods()]);
    }

    public function update(Request $request, $id): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $exam = ExaminationModel::getSingle($id);
            $existingExam = ExaminationModel::checkNameSingle($request->name, $id);

            if ($existingExam) {
                return redirect()->back()->with('error', 'Une évaluation avec ce nom existe déjà.');
            }

            if (!$exam) {
                return redirect()->back()->with('error', 'Cette évaluation est introuvable.');
            }

            $exam->name = trim($request->name);
            $exam->start_date = trim($request->start_date);
            $exam->end_date = trim($request->end_date);
            $exam->period_id = intval($request->period_id);
            $exam->status = intval($request->status);
            $exam->save();
            return redirect('admin/examinations/exam/list')->with('success', 'Cette évaluation a été modifiée avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification de cette évaluation : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function delete($id)
    {
        $exam = ExaminationModel::getSingle($id);
        if ($exam) {
            $exam->is_delete = 1;
            $exam->save();
            return redirect('admin/examinations/exam/list')->with('success', 'Cette évaluation a été supprimé avec succès.');
        } else {
            abort(404);
        }
    }

    public function scheduleList(Request $request)
    {
        $result = [];

        if (!empty($request->get('exam_id')) && !empty($request->get('class_id'))) {
            $getSubject = ClassSubjectModel::getSubject($request->get('class_id'));

            foreach ($getSubject as $subject) {
                $dataSchedule = [];
                $dataSchedule['subject_id'] = $subject->subject_id;
                $dataSchedule['class_id'] = $subject->class_id;
                $dataSchedule['subject_name'] = $subject->subject_name;
                $dataSchedule['subject_type'] = $subject->subject_type;

                $examSchedule = ScheduleModel::getExamSchedule($request->get('exam_id'), $request->get('class_id'), $subject->subject_id);
                if ($examSchedule) {
                    $dataSchedule['exam_date'] = $examSchedule->exam_date;
                    $dataSchedule['start_time'] = $examSchedule->start_time;
                    $dataSchedule['end_time'] = $examSchedule->end_time;
                    $dataSchedule['room_number'] = $examSchedule->room_number;
                    $dataSchedule['full_marks'] = $examSchedule->full_marks;
                    $dataSchedule['passing_marks'] = $examSchedule->passing_marks;
                } else {
                    $dataSchedule['exam_date'] = '';
                    $dataSchedule['start_time'] = '';
                    $dataSchedule['end_time'] = '';
                    $dataSchedule['room_number'] = '';
                    $dataSchedule['full_marks'] = '';
                    $dataSchedule['passing_marks'] = '';
                }

                $result[] = $dataSchedule;
            }
        }

        return Inertia::render('Admin/Exams/Schedule', [
            'examSchedule' => $result,
            'exams'        => ExaminationModel::getExams(),
            'classes'      => ClassModel::getClass(),
            'selectedExam'  => $request->exam_id,
            'selectedClass' => $request->class_id,
        ]);
    }

    public function scheduleCreate(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $existingSchedules = ScheduleModel::checkExamSchedule($request->exam_id, $request->class_id);
            ScheduleModel::deleteExamSchedule($request->exam_id, $request->class_id);
            $isUpdated = $existingSchedules;
            if (!empty($request->schedule)) {
                foreach ($request->schedule as $schedule) {
                    if (
                        !empty($schedule['subject_id']) &&
                        !empty($schedule['exam_date']) &&
                        !empty($schedule['start_time']) &&
                        !empty($schedule['end_time']) &&
                        !empty($schedule['room_number']) &&
                        !empty($schedule['full_marks']) &&
                        !empty($schedule['passing_marks'])
                    ) {
                        $examSchedule = new ScheduleModel;
                        $examSchedule->exam_id = $request->exam_id;
                        $examSchedule->class_id = $request->class_id;
                        $examSchedule->subject_id = $schedule['subject_id'];
                        $examSchedule->exam_date = $schedule['exam_date'];
                        $examSchedule->start_time = $schedule['start_time'];
                        $examSchedule->end_time = $schedule['end_time'];
                        $examSchedule->room_number = $schedule['room_number'];
                        $examSchedule->full_marks = $schedule['full_marks'];
                        $examSchedule->passing_marks = $schedule['passing_marks'];
                        $examSchedule->created_by = auth()->user()->id;
                        $examSchedule->save();
                    }
                }
            }
            $message = $isUpdated
                ? 'Ce programme d\'évaluation a été modifié avec succès.'
                : 'Ce programme d\'évaluation a été créé avec succès.';

            return redirect('admin/examinations/schedule/list')->with('success', $message);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de ce programme d'évaluation : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function myExamTimetableStudent()
    {
        $class_id = Auth::user()->class_id;
        $getExamSchedule = ScheduleModel::getExam($class_id);
        $result = [];
        foreach ($getExamSchedule as $examSchedule) {
            $dataExam = [];
            $dataExam['name'] = $examSchedule->exam_name;
            $getExamTimetable = ScheduleModel::getExamTimetable($examSchedule->exam_id, $class_id);
            $results = [];
            foreach ($getExamTimetable as $examTimetable) {
                $dataSchedule = [];
                $dataSchedule['subject_name'] = $examTimetable->subject_name;
                $dataSchedule['exam_date'] = $examTimetable->exam_date;
                $dataSchedule['start_time'] = $examTimetable->start_time;
                $dataSchedule['end_time'] = $examTimetable->end_time;
                $dataSchedule['room_number'] = $examTimetable->room_number;
                $dataSchedule['full_marks'] = $examTimetable->full_marks;
                $dataSchedule['passing_marks'] = $examTimetable->passing_marks;
                $results[] = $dataSchedule;
            }
            $dataExam['getExams'] = $results;
            $result[] = $dataExam;
        }

        return Inertia::render('Student/Exams/Timetable', [
            'examTimetable' => $result,
        ]);
    }

    public function myExamTimetableTeacher()
    {
        $getClass = ClassTeacherModel::getMyClassSubjectGroup(Auth::user()->id);
        $result = [];

        foreach ($getClass as $class) {
            $dataClass = [];
            $dataClass['class_name'] = $class->class_name;

            $getExamSchedule = ScheduleModel::getExam($class->class_id);
            $examArray = [];
            foreach ($getExamSchedule as $examSchedule) {
                $dataExam = [];
                $dataExam['exam_name'] = $examSchedule->exam_name;
                $getExamTimetable = ScheduleModel::getExamTimetable($examSchedule->exam_id, $class->class_id);
                $subjectArray = [];
                foreach ($getExamTimetable as $examTimetable) {
                    $dataSchedule = [];
                    $dataSchedule['subject_name'] = $examTimetable->subject_name;
                    $dataSchedule['exam_date'] = $examTimetable->exam_date;
                    $dataSchedule['start_time'] = $examTimetable->start_time;
                    $dataSchedule['end_time'] = $examTimetable->end_time;
                    $dataSchedule['room_number'] = $examTimetable->room_number;
                    $dataSchedule['full_marks'] = $examTimetable->full_marks;
                    $dataSchedule['passing_marks'] = $examTimetable->passing_marks;
                    $subjectArray[] = $dataSchedule;
                }
                $dataExam['subjectSchedule'] = $subjectArray;
                $examArray[] = $dataExam;
            }
            $dataClass['getExams'] = $examArray;
            $result[] = $dataClass;
        }

        return Inertia::render('Teacher/Exams/Timetable', [
            'examTimetable' => $result,
        ]);
    }

    public function parentStudentExamTimetable($student_id)
    {
        $getStudent = User::getSingle($student_id);
        $class_id = $getStudent->class_id;
        $getExamSchedule = ScheduleModel::getExam($class_id);
        $result = [];

        foreach ($getExamSchedule as $examSchedule) {
            $dataExam = [];
            $dataExam['name'] = $examSchedule->exam_name;
            $getExamTimetable = ScheduleModel::getExamTimetable($examSchedule->exam_id, $class_id);
            $results = [];

            foreach ($getExamTimetable as $examTimetable) {
                $dataSchedule = [];
                $dataSchedule['subject_name'] = $examTimetable->subject_name;
                $dataSchedule['exam_date'] = $examTimetable->exam_date;
                $dataSchedule['start_time'] = $examTimetable->start_time;
                $dataSchedule['end_time'] = $examTimetable->end_time;
                $dataSchedule['room_number'] = $examTimetable->room_number;
                $dataSchedule['full_marks'] = $examTimetable->full_marks;
                $dataSchedule['passing_marks'] = $examTimetable->passing_marks;
                $results[] = $dataSchedule;
            }
            $dataExam['getExams'] = $results;
            $result[] = $dataExam;
        }

        return Inertia::render('Parent/Exams/Timetable', [
            'examTimetable' => $result,
            'student' => $getStudent,
        ]);
    }

    public function marksRegister(Request $request)
    {
        $data = [
            'classes' => ClassModel::getClass(),
            'exams' => ExaminationModel::getExams(),
        ];

        if (!empty($request->exam_id) && !empty($request->class_id)) {
            $data['subjects'] = ScheduleModel::getSubject($request->exam_id, $request->class_id);
            $data['students'] = User::getStudent($request->class_id);
        }

        return Inertia::render('Admin/Exams/MarksRegister', [
            'data' => $data,
        ]);
    }

    public function addMarksRegister(Request $request)
    {
        try {
            $allSuccessful = true;

            if (!empty($request->marks)) {
                foreach ($request->marks as $mark) {
                    $getExamSchedule = ScheduleModel::getSingle($mark['id']);

                    // Définir les notes par défaut à null si elles n'existent pas
                    $class_work = $mark['class_work'] ?? null;
                    $home_work = $mark['home_work'] ?? null;
                    $test_work = $mark['test_work'] ?? null;
                    $exam_work = $mark['exam_work'] ?? null;
                    $quiz_1 = $mark['quiz_1'] ?? null;
                    $quiz_2 = $mark['quiz_2'] ?? null;
                    $quiz_3 = $mark['quiz_3'] ?? null;
                    $quiz_4 = $mark['quiz_4'] ?? null;
                    $quiz_5 = $mark['quiz_5'] ?? null;
                    $assignment_1 = $mark['assignment_1'] ?? null;
                    $assignment_2 = $mark['assignment_2'] ?? null;
                    $assignment_3 = $mark['assignment_3'] ?? null;
                    $passing_marks = $mark['passing_marks'] ?? null;
                    $full_marks = $mark['full_marks'] ?? null;

                    // Création d'un tableau de toutes les notes individuelles pour un calcul facile
                    $all_individual_marks = [
                        $class_work,
                        $home_work,
                        $test_work,
                        $exam_work,
                        $quiz_1,
                        $quiz_2,
                        $quiz_3,
                        $quiz_4,
                        $quiz_5,
                        $assignment_1,
                        $assignment_2,
                        $assignment_3
                    ];

                    // Calcul du total des notes en ignorant les valeurs null
                    $total_marks = collect($all_individual_marks)->filter(fn($value) => !is_null($value))->sum();

                    // Le total des notes reçues pour la validation (vous pouvez le garder ou le calculer)
                    $total_marks_received = $mark['total_marks'] ?? $total_marks;

                    if ($getExamSchedule && $getExamSchedule->full_marks >= $total_marks_received) {
                        $getMarks = MarkRegisterModel::checkAlreadyMarks(
                            $request->student_id,
                            $request->exam_id,
                            $request->class_id,
                            $mark['subject_id']
                        );

                        $marksRegister = $getMarks ?? new MarkRegisterModel;

                        // Assigner toutes les valeurs des notes individuelles et des totaux
                        $marksRegister->student_id = $request->student_id;
                        $marksRegister->class_id = $request->class_id;
                        $marksRegister->exam_id = $request->exam_id;
                        $marksRegister->subject_id = $mark['subject_id'];
                        $marksRegister->class_work = $class_work;
                        $marksRegister->home_work = $home_work;
                        $marksRegister->test_work = $test_work;
                        $marksRegister->exam_work = $exam_work;
                        $marksRegister->quiz_1 = $quiz_1;
                        $marksRegister->quiz_2 = $quiz_2;
                        $marksRegister->quiz_3 = $quiz_3;
                        $marksRegister->quiz_4 = $quiz_4;
                        $marksRegister->quiz_5 = $quiz_5;
                        $marksRegister->assignment_1 = $assignment_1;
                        $marksRegister->assignment_2 = $assignment_2;
                        $marksRegister->assignment_3 = $assignment_3;

                        // Calcul de la moyenne des quiz
                        $quiz_notes = array_filter([$quiz_1, $quiz_2, $quiz_3, $quiz_4, $quiz_5], fn($value) => !is_null($value));
                        $quiz_average = count($quiz_notes) > 0 ? array_sum($quiz_notes) / count($quiz_notes) : 0;

                        // Calcul de la moyenne des assignments
                        $assignment_notes = array_filter([$assignment_1, $assignment_2, $assignment_3], fn($value) => !is_null($value));
                        $assignment_average = count($assignment_notes) > 0 ? array_sum($assignment_notes) / count($assignment_notes) : 0;

                        // Récupération et assignation du coefficient
                        $classSubject = ClassSubjectModel::getClassSubject($request->class_id, $mark['subject_id']);
                        $coefficient = $classSubject ? $classSubject->coefficient : 1;

                        // Enregistrer les moyennes, le coefficient et les totaux dans le modèle
                        $marksRegister->quiz_average = round($quiz_average, 2);
                        $marksRegister->assignment_average = round($assignment_average, 2);
                        $marksRegister->coefficient = $coefficient;
                        $marksRegister->total_marks = $total_marks_received;
                        $marksRegister->passing_marks = $passing_marks;
                        $marksRegister->full_marks = $full_marks;
                        $marksRegister->created_by = Auth::user()->id;

                        $marksRegister->save();

                    } else {
                        $allSuccessful = false;
                        return response()->json([
                            'success' => false,
                            'message' => "Le total des notes pour la matière avec l'ID : {$mark['subject_id']} dépasse la note totale autorisée."
                        ]);
                    }
                }

                if ($allSuccessful) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Toutes les notes ont été ajoutées/modifiées avec succès.'
                    ]);
                }

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune donnée de notes envoyée.'
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Erreur lors de la création du registre de notes : " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite. Veuillez réessayer.'
            ]);
        }
    }

    public function addSingleMarksRegister(Request $request)
    {
        try {
            $allSuccessful = true;

            if (!empty($request->marks)) {
                foreach ($request->marks as $mark) {
                    $getExamSchedule = ScheduleModel::getSingle($mark['id']);

                    // Définir les notes par défaut à null si elles n'existent pas ou sont vides
                    $class_work = $mark['class_work'] ?? null;
                    $home_work = $mark['home_work'] ?? null;
                    $test_work = $mark['test_work'] ?? null;
                    $exam_work = $mark['exam_work'] ?? null;
                    $quiz_1 = $mark['quiz_1'] ?? null;
                    $quiz_2 = $mark['quiz_2'] ?? null;
                    $quiz_3 = $mark['quiz_3'] ?? null;
                    $quiz_4 = $mark['quiz_4'] ?? null;
                    $quiz_5 = $mark['quiz_5'] ?? null;
                    $assignment_1 = $mark['assignment_1'] ?? null;
                    $assignment_2 = $mark['assignment_2'] ?? null;
                    $assignment_3 = $mark['assignment_3'] ?? null;
                    $passing_marks = $mark['passing_marks'] ?? null;
                    $full_marks = $mark['full_marks'] ?? null;

                    // Création d'un tableau de toutes les notes individuelles
                    $all_individual_marks = [
                        $class_work,
                        $home_work,
                        $test_work,
                        $exam_work,
                        $quiz_1,
                        $quiz_2,
                        $quiz_3,
                        $quiz_4,
                        $quiz_5,
                        $assignment_1,
                        $assignment_2,
                        $assignment_3
                    ];

                    // Calcul du total des notes en ignorant les valeurs null
                    $total_marks_calculated = collect($all_individual_marks)->filter(fn($value) => !is_null($value))->sum();
                    $total_marks_received = $mark['total_marks'] ?? $total_marks_calculated;

                    if ($getExamSchedule && $getExamSchedule->full_marks >= $total_marks_received) {
                        $getMarks = MarkRegisterModel::checkAlreadyMarks(
                            $request->student_id,
                            $request->exam_id,
                            $request->class_id,
                            $mark['subject_id']
                        );

                        $marksRegister = $getMarks ?? new MarkRegisterModel;
                        $marksRegister->created_by = Auth::user()->id;

                        // Assigner toutes les valeurs des notes individuelles
                        $marksRegister->student_id = $request->student_id;
                        $marksRegister->class_id = $request->class_id;
                        $marksRegister->exam_id = $request->exam_id;
                        $marksRegister->subject_id = $mark['subject_id'];
                        $marksRegister->class_work = $class_work;
                        $marksRegister->home_work = $home_work;
                        $marksRegister->test_work = $test_work;
                        $marksRegister->exam_work = $exam_work;
                        $marksRegister->quiz_1 = $quiz_1;
                        $marksRegister->quiz_2 = $quiz_2;
                        $marksRegister->quiz_3 = $quiz_3;
                        $marksRegister->quiz_4 = $quiz_4;
                        $marksRegister->quiz_5 = $quiz_5;
                        $marksRegister->assignment_1 = $assignment_1;
                        $marksRegister->assignment_2 = $assignment_2;
                        $marksRegister->assignment_3 = $assignment_3;

                        // Calcul de la moyenne des quiz
                        $quiz_notes_collection = collect([$quiz_1, $quiz_2, $quiz_3, $quiz_4, $quiz_5]);
                        $quiz_average = $quiz_notes_collection->filter(fn($value) => !is_null($value))->avg();
                        $marksRegister->quiz_average = round($quiz_average, 2);

                        // Calcul de la moyenne des assignments
                        $assignment_notes_collection = collect([$assignment_1, $assignment_2, $assignment_3]);
                        $assignment_average = $assignment_notes_collection->filter(fn($value) => !is_null($value))->avg();
                        $marksRegister->assignment_average = round($assignment_average, 2);

                        // Récupération et assignation du coefficient
                        $classSubject = ClassSubjectModel::getClassSubject($request->class_id, $mark['subject_id']);
                        $coefficient = $classSubject ? $classSubject->coefficient : 1;
                        $marksRegister->coefficient = $coefficient;

                        // Assigner les totaux et autres informations
                        $marksRegister->total_marks = $total_marks_received;
                        $marksRegister->passing_marks = $passing_marks;
                        $marksRegister->full_marks = $full_marks;

                        $marksRegister->save();

                    } else {
                        $allSuccessful = false;
                        return response()->json([
                            'success' => false,
                            'message' => "Le total des notes pour la matière avec l'ID : {$mark['subject_id']} dépasse la note totale autorisée."
                        ]);
                    }
                }

                if ($allSuccessful) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Toutes les notes ont été ajoutées/modifiées avec succès.'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune donnée de notes envoyée.'
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création du registre de notes : " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite. Veuillez réessayer.'
            ]);
        }
    }

    public function teacherMarkRegister(Request $request)
    {
        $data = [
            'classes' => ClassTeacherModel::getMyClassSubjectGroup(Auth::user()->id),
            'exams' => ScheduleModel::getExamTeacher(Auth::user()->id),
        ];

        if (!empty($request->exam_id) && !empty($request->class_id)) {
            $data['subjects'] = ScheduleModel::getSubject($request->exam_id, $request->class_id);
            $data['students'] = User::getStudent($request->class_id);
        }

        return Inertia::render('Teacher/Exams/MarksRegister', [
            'data' => $data,
        ]);
    }

    public function addTeacherMarkRegister(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $responses = [];
            $allSuccessful = true;

            if (!empty($request->marks)) {
                foreach ($request->marks as $mark) {
                    $getExamSchedule = ScheduleModel::getSingle($mark['id']);
                    $schedule_full_marks = $getExamSchedule->full_marks;

                    $class_work = !empty($mark['class_work']) ? $mark['class_work'] : 0;
                    $home_work = !empty($mark['home_work']) ? $mark['home_work'] : 0;
                    $test_work = !empty($mark['test_work']) ? $mark['test_work'] : 0;
                    $exam_work = !empty($mark['exam_work']) ? $mark['exam_work'] : 0;
                    $passing_marks = !empty($mark['passing_marks']) ? $mark['passing_marks'] : 0;
                    $full_marks = !empty($mark['full_marks']) ? $mark['full_marks'] : 0;

                    $total_marks = $class_work + $home_work + $test_work + $exam_work;

                    if ($getExamSchedule && $schedule_full_marks >= $total_marks) {
                        $getMarks = MarkRegisterModel::checkAlreadyMarks(
                            $request->student_id,
                            $request->exam_id,
                            $request->class_id,
                            $mark['subject_id']
                        );

                        if (!empty($getMarks)) {
                            $marksRegister = $getMarks;
                            $marksRegister->created_by = Auth::user()->id;
                        } else {
                            $marksRegister = new MarkRegisterModel;
                            $marksRegister->created_by = Auth::user()->id;
                        }

                        $marksRegister->student_id = $request->student_id;
                        $marksRegister->class_id = $request->class_id;
                        $marksRegister->exam_id = $request->exam_id;
                        $marksRegister->subject_id = $mark['subject_id'];
                        $marksRegister->class_work = $class_work;
                        $marksRegister->home_work = $home_work;
                        $marksRegister->test_work = $test_work;
                        $marksRegister->exam_work = $exam_work;
                        $marksRegister->passing_marks = $passing_marks;
                        $marksRegister->full_marks = $full_marks;

                        $marksRegister->save();

                        $responses[] = [
                            'success' => true,
                            'message' => "Notes ajoutées/modifiées avec succès pour la matière avec l'ID : {$mark['subject_id']}."
                        ];
                    } else {
                        $responses[] = [
                            'success' => false,
                            'message' => "Le total des notes pour la matière avec l'ID : {$mark['subject_id']} dépasse la note totale autorisée."
                        ];
                        $allSuccessful = false;
                    }
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune donnée de notes envoyée.'
                ]);
            }

            if ($allSuccessful) {
                return response()->json([
                    'success' => true,
                    'message' => 'Toutes les notes ont été ajoutées/modifiées avec succès.'
                ]);
            } else {
                $firstError = collect($responses)->firstWhere('success', false);
                return response()->json([
                    'success' => false,
                    'message' => $firstError['message']
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Erreur lors de la création du registre de notes : " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite. Veuillez réessayer.'
            ]);
        }
    }

    public function addSingleTeacherMarkRegister(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $isUpdate = false;
            if (!empty($request->marks)) {
                foreach ($request->marks as $mark) {
                    $getExamSchedule = ScheduleModel::getSingle($mark['id']);
                    $schedule_full_marks = $getExamSchedule->full_marks;

                    $class_work = !empty($mark['class_work']) ? $mark['class_work'] : 0;
                    $home_work = !empty($mark['home_work']) ? $mark['home_work'] : 0;
                    $test_work = !empty($mark['test_work']) ? $mark['test_work'] : 0;
                    $exam_work = !empty($mark['exam_work']) ? $mark['exam_work'] : 0;

                    $total_marks = $class_work + $home_work + $test_work + $exam_work;

                    if ($getExamSchedule && $schedule_full_marks >= $total_marks) {
                        $getMarks = MarkRegisterModel::checkAlreadyMarks($request->student_id, $request->exam_id, $request->class_id, $mark['subject_id']);
                        if (!empty($getMarks)) {
                            $marksRegister = $getMarks;
                            $isUpdate = true;
                        } else {
                            $marksRegister = new MarkRegisterModel;
                            $marksRegister->created_by = Auth::user()->id;
                        }

                        $marksRegister->student_id = $request->student_id;
                        $marksRegister->class_id = $request->class_id;
                        $marksRegister->exam_id = $request->exam_id;
                        $marksRegister->subject_id = $mark['subject_id'];
                        $marksRegister->class_work = $class_work;
                        $marksRegister->home_work = $home_work;
                        $marksRegister->test_work = $test_work;
                        $marksRegister->exam_work = $exam_work;
                        $marksRegister->passing_marks = $getExamSchedule->passing_marks;
                        $marksRegister->full_marks = $getExamSchedule->full_marks;

                        $marksRegister->save();

                        $message = $isUpdate ? 'Notes modifiées avec succès' : 'Notes ajoutées avec succès';
                        return response()->json(['success' => true, 'message' => $message]);
                    } else {
                        return response()->json(['error' => false, 'message' => 'Le total des notes de l\'apprenant est plus grande que la note totale']);
                    }
                }
            } else {
                return response()->json(['error' => false, 'message' => '']);
            }
            return response()->json(['success' => true, 'message' => 'Opération effectuée avec succès']);

        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de ce registre de notes. " . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Vos informations ne sont pas correctes. Veuillez réessayer.']);
        }
    }

    public function myExamResultStudent()
    {
        $result = [];
        $getExam = MarkRegisterModel::getExam(Auth::user()->id);

        foreach ($getExam as $examValue) {
            $dataExam = [];
            $dataExam['exam_name'] = $examValue->exam_name;
            $dataExam['exam_id'] = $examValue->exam_id;

            $getExamSubject = MarkRegisterModel::getExamSubject($examValue->exam_id, Auth::user()->id);
            $dataSubject = [];

            // Initialiser les totaux pour cet examen
            $total_class_work = 0;
            $total_home_work = 0;
            $total_test_work = 0;
            $total_exam_work = 0;
            $total_score = 0;
            $passing_marks = 0;
            $full_marks = 0;

            foreach ($getExamSubject as $examSubject) {
                $total_score_subject = $examSubject['class_work'] + $examSubject['test_work'] + $examSubject['home_work'] + $examSubject['exam_work'];

                $dataSub = [];
                $dataSub['subject_name'] = $examSubject['subject_name'];
                $dataSub['class_work'] = $examSubject['class_work'];
                $dataSub['test_work'] = $examSubject['test_work'];
                $dataSub['home_work'] = $examSubject['home_work'];
                $dataSub['exam_work'] = $examSubject['exam_work'];
                $dataSub['score_marks'] = $total_score_subject;
                $dataSub['passing_marks'] = $examSubject['passing_marks'];
                $dataSub['full_marks'] = $examSubject['full_marks'];
                $dataSubject[] = $dataSub;

                // Calculer les totaux
                $total_class_work += $examSubject['class_work'];
                $total_home_work += $examSubject['home_work'];
                $total_test_work += $examSubject['test_work'];
                $total_exam_work += $examSubject['exam_work'];
                $total_score += $total_score_subject;
                $passing_marks += $examSubject['passing_marks'];
                $full_marks += $examSubject['full_marks'];
            }

            $dataExam['subject'] = $dataSubject;

            // Ajouter les totaux à l'examen
            $dataExam['total_class_work'] = $total_class_work;
            $dataExam['total_home_work'] = $total_home_work;
            $dataExam['total_test_work'] = $total_test_work;
            $dataExam['total_exam_work'] = $total_exam_work;
            $dataExam['total_score'] = $total_score;
            $dataExam['passing_marks'] = $passing_marks;
            $dataExam['full_marks'] = $full_marks;

            // Calculer le pourcentage et le grade
            $percentage = ($full_marks > 0) ? ($total_score * 100) / $full_marks : 0;
            $dataExam['percentage'] = round($percentage, 2);
            $dataExam['grade'] = MarksGradeModel::getGrade($percentage);

            $result[] = $dataExam;
        }

        $data['getExamResultStudent'] = $result;
        return Inertia::render('Student/Exams/Results', [
            'examResult' => $result,
        ]);
    }

    public function studentExamResultPrint(Request $request)
    {
        $data['header_title'] = "Imprimer mes résultats d'examens";
        $exam_id = $request->exam_id;
        $student_id = $request->student_id;

        $getExam = MarkRegisterModel::getExam($student_id);
        $data['getStudent'] = User::getStudentData($student_id);

        if ($getExam->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun examen trouvé pour cet apprenant.');
        }

        foreach ($getExam as $examValue) {
            $data['exam_name'] = $examValue->exam_name;
        }

        $getExamSubject = MarkRegisterModel::getExamSubject($exam_id, $student_id);

        // Initialiser les totaux
        $total_class_work = 0;
        $total_home_work = 0;
        $total_test_work = 0;
        $total_exam_work = 0;
        $total_score = 0;
        $passing_marks = 0;
        $full_marks = 0;

        $dataSubject = [];
        foreach ($getExamSubject as $examSubject) {
            $total_score_subject = $examSubject['class_work'] + $examSubject['test_work'] + $examSubject['home_work'] + $examSubject['exam_work'];

            $dataSub = [];
            $dataSub['subject_name'] = $examSubject['subject_name'];
            $dataSub['class_work'] = $examSubject['class_work'];
            $dataSub['test_work'] = $examSubject['test_work'];
            $dataSub['home_work'] = $examSubject['home_work'];
            $dataSub['exam_work'] = $examSubject['exam_work'];
            $dataSub['score_marks'] = $total_score_subject;
            $dataSub['passing_marks'] = $examSubject['passing_marks'];
            $dataSub['full_marks'] = $examSubject['full_marks'];
            $dataSubject[] = $dataSub;

            // Calculer les totaux
            $total_class_work += $examSubject['class_work'];
            $total_home_work += $examSubject['home_work'];
            $total_test_work += $examSubject['test_work'];
            $total_exam_work += $examSubject['exam_work'];
            $total_score += $total_score_subject;
            $passing_marks += $examSubject['passing_marks'];
            $full_marks += $examSubject['full_marks'];
        }

        $data['getExamResultStudent'] = $dataSubject;

        // Ajouter les totaux aux données
        $data['total_class_work'] = $total_class_work;
        $data['total_home_work'] = $total_home_work;
        $data['total_test_work'] = $total_test_work;
        $data['total_exam_work'] = $total_exam_work;
        $data['total_score'] = $total_score;
        $data['passing_marks'] = $passing_marks;
        $data['full_marks'] = $full_marks;

        // Calculer le pourcentage et le grade
        $percentage = ($full_marks > 0) ? ($total_score * 100) / $full_marks : 0;
        $data['percentage'] = round($percentage, 2);
        $data['getGrade'] = MarksGradeModel::getGrade($percentage);

        return view('exam_result_print', $data);
    }

    public function parentStudentExamResult($student_id)
    {
        $getStudent = User::getSingle($student_id);
        $result = [];
        $getExam = MarkRegisterModel::getExam($student_id);
        foreach ($getExam as $examValue) {
            $dataExam = [];
            $dataExam['exam_name'] = $examValue->exam_name;
            $dataExam['exam_id'] = $examValue->exam_id;
            $getExamSubject = MarkRegisterModel::getExamSubject($examValue->exam_id, $student_id);
            $dataSubject = [];
            foreach ($getExamSubject as $examSubject) {
                $totol_score = $examSubject['class_work'] + $examSubject['test_work'] + $examSubject['home_work'] + $examSubject['exam_work'];
                $dataSub = [];
                $dataSub['subject_name'] = $examSubject['subject_name'];
                $dataSub['class_work'] = $examSubject['class_work'];
                $dataSub['test_work'] = $examSubject['test_work'];
                $dataSub['home_work'] = $examSubject['home_work'];
                $dataSub['exam_work'] = $examSubject['exam_work'];
                $dataSub['score_marks'] = $totol_score;
                $dataSub['passing_marks'] = $examSubject['passing_marks'];
                $dataSub['full_marks'] = $examSubject['full_marks'];
                $dataSubject[] = $dataSub;
            }
            $dataExam['subject'] = $dataSubject;
            $result[] = $dataExam;
        }
        $data['getExamResultStudent'] = $result;
        return Inertia::render('Parent/Exams/Results', [
            'examResult' => $result,
            'student' => $getStudent,
        ]);
    }

    public function listMarksGrade()
    {
        return Inertia::render('Admin/Exams/MarksGrade', [
            'marksGrades' => MarksGradeModel::getMarksGrade(15),
        ]);
    }

    public function createMarksGrade(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $existingMarksGradeName = MarksGradeModel::getNameSingle($request->name);

            if ($existingMarksGradeName) {
                return redirect()->back()->with('error', 'Une note avec ce nom existe déjà.');
            }

            $marksGrade = new MarksGradeModel();
            $marksGrade->name = trim($request->name);
            $marksGrade->percent_from = trim($request->percent_from);
            $marksGrade->percent_to = trim($request->percent_to);
            $marksGrade->created_by = auth()->user()->id;
            $marksGrade->save();

            return redirect('admin/examinations/marks_grade/list')->with('success', 'Cette note a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'une note : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }

    }

    public function editMarksGrade($id)
    {
        $marksGrade = MarksGradeModel::getSingle($id);
        if (!$marksGrade) {
            abort(404);
        }
        return response()->json(['marksGrade' => $marksGrade]);
    }

    public function updateMarksGrade(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $existingMarksGradeName = MarksGradeModel::checkNameSingle($request->name, $id);

            if ($existingMarksGradeName) {
                return redirect()->back()->with('error', 'Une note avec ce nom existe déjà.');
            }

            $marksGrade = MarksGradeModel::getSingle($id);
            $marksGrade->name = trim($request->name);
            $marksGrade->percent_from = trim($request->percent_from);
            $marksGrade->percent_to = trim($request->percent_to);
            $marksGrade->save();

            return redirect('admin/examinations/marks_grade/list')->with('success', 'Cette note a été modifiée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification d'une note : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function deleteMarksGrade($id): \Illuminate\Http\RedirectResponse
    {
        try {
            $marksGrade = MarksGradeModel::getSingle($id);
            $marksGrade->delete();

            return redirect('admin/examinations/marks_grade/list')->with('success', 'Cette note a été supprimée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression d'une note : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

}
