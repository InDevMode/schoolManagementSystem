<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\ClassTeacherModel;
use App\Models\ExaminationModel;
use App\Models\MarkRegisterModel;
use App\Models\MarksGradeModel;
use App\Models\ScheduleModel;
use App\Models\SettingModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExaminationController extends Controller
{
    public function list(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['getExams'] = ExaminationModel::getExaminations(5);
        $data['header_title'] = "Liste des évaluations";
        return view('admin.examinations.exam.list', $data);
    }

    public function add(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Créer une évaluation";
        return view('admin.examinations.exam.add', $data);
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
            $exam->note = trim($request->note);
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
        $data['getExams'] = ExaminationModel::getSingle($id);
        if (!empty($data['getExams'])) {
            $data['header_title'] = "Modifier une évaluation";
            return view('admin.examinations.exam.edit', $data);
        } else {
            abort(404);
        }
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
            $exam->note = trim($request->note);
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

    public function scheduleList(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Programmation des examens";
        $data['getClass'] = ClassModel::getClass();
        $data['getExams'] = ExaminationModel::getExams();
        $result = array();

        if (!empty($request->get('exam_id')) && !empty($request->get('class_id'))) {
            $getSubject = ClassSubjectModel::getSubject($request->get('class_id'));

            foreach ($getSubject as $subject) {
                $dataSchedule = array();
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
        $data['getExamSchedule'] = $result;
        return view('admin.examinations.schedule.list', $data);
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

    public function myExamTimetableStudent(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Mon calendrier d'examen";
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
        $data['getExamTimetable'] = $result;
        return view('student.exam_timetable', $data);
    }

    public function myExamTimetableTeacher(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Mon calendrier d'examens";
        $getClass = ClassTeacherModel::getMyClassSubjectGroup(Auth::user()->id);
        $result = array();

        foreach ($getClass as $class) {
            $dataClass = array();
            $dataClass['class_name'] = $class->class_name;

            $getExamSchedule = ScheduleModel::getExam($class->class_id);
            $examArray = array();
            foreach ($getExamSchedule as $examSchedule) {
                $dataExam = array();
                $dataExam['exam_name'] = $examSchedule->exam_name;
                $getExamTimetable = ScheduleModel::getExamTimetable($examSchedule->exam_id, $class->class_id);
                $subjectArray = array();
                foreach ($getExamTimetable as $examTimetable) {
                    $dataSchedule = array();
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
        $data['getExamTimetable'] = $result;
        return view('teacher.exam_timetable', $data);
    }

    public function parentStudentExamTimetable($student_id): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Son calendrier d'examens";
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
        $data['getExamTimetable'] = $result;
        $data['getStudent'] = $getStudent;
        return view('parent.exam_timetable', $data);
    }

    public function marksRegister(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Registre des notes";
        $data['getClass'] = ClassModel::getClass();
        $data['getExams'] = ExaminationModel::getExams();

        if (!empty($request->exam_id) && !empty($request->class_id)) {
            $data['getSubject'] = ScheduleModel::getSubject($request->exam_id, $request->class_id);
            $data['getStudent'] = User::getStudent($request->class_id);
        }

        return view('admin.examinations.marks_register.list', $data);
    }

    public function addMarksRegister(Request $request): \Illuminate\Http\JsonResponse
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

    public function addSingleMarksRegister(Request $request): \Illuminate\Http\JsonResponse
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

    public function teacherMarkRegister(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Registre des notes";
        $data['getClass'] = ClassTeacherModel::getMyClassSubjectGroup(Auth::user()->id);
        $data['getExams'] = ScheduleModel::getExamTeacher(Auth::user()->id);
        if (!empty($request->exam_id) && !empty($request->class_id)) {
            $data['getSubject'] = ScheduleModel::getSubject($request->exam_id, $request->class_id);
            $data['getStudent'] = User::getStudent($request->class_id);
        }

        return view('teacher.marks_register', $data);
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

    public function myExamResultStudent(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $result = array();
        $getExam = MarkRegisterModel::getExam(Auth::user()->id);

        foreach ($getExam as $examValue) {
            $dataExam = array();
            $dataExam['exam_name'] = $examValue->exam_name;
            $dataExam['exam_id'] = $examValue->exam_id;

            $getExamSubject = MarkRegisterModel::getExamSubject($examValue->exam_id, Auth::user()->id);
            $dataSubject = array();

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

                $dataSub = array();
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
        $data['header_title'] = "Mes résultats d'examens";
        return view('student.exam_result', $data);
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

    public function parentStudentExamResult($student_id): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Ses résultats d'examens";

        $data['getStudent'] = User::getSingle($student_id);
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
        return view('parent.exam_result', $data);
    }

    public function listMarksGrade(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Liste des notes";
        $data['getMarksGrade'] = MarksGradeModel::getMarksGrade(5);
        return view('admin.examinations.marks_grade.list', $data);
    }

    public function addMarksGrade(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Créer une note";
        return view('admin.examinations.marks_grade.add', $data);
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

    public function editMarksGrade($id): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Modifier une note";
        $data['getMarksGrade'] = MarksGradeModel::getSingle($id);
        return view('admin.examinations.marks_grade.edit', $data);
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
