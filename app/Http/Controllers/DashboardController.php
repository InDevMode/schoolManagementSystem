<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\ClassTeacherModel;
use App\Models\ClassTimetableModel;
use App\Models\CommunicateModel;
use App\Models\ExaminationModel;
use App\Models\FeesCollectionModel;
use App\Models\HomeworkModel;
use App\Models\NoticeBoardMessageModel;
use App\Models\ScheduleModel;
use App\Models\StudentAttendanceModel;
use App\Models\SubjectModel;
use App\Models\User;
use App\Models\WeekModel;
use App\Models\WorkModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $data['header_title'] = 'Tableau de bord';
        switch (Auth::user()->user_type) {
            case 1:

                $data['totalUser'] = User::getTotalUser();
                $data['totalAdmin'] = User::getTotalUserWithUserType(1);
                $data['totalTeacher'] = User::getTotalUserWithUserType(2);
                $data['totalStudent'] = User::getTotalUserWithUserType(3);
                $data['totalParent'] = User::getTotalUserWithUserType(4);
                $data['totalClass'] = ClassModel::getTotalClass();
                $data['totalSubject'] = SubjectModel::getTotalSubject();
                $data['totalFeesCollections'] = FeesCollectionModel::getTotalFeesCollections();
                $data['totalCommunicate'] = CommunicateModel::getTotalCommunicate();
                $data['totalExam'] = ExaminationModel::getTotalExam();
                $data['totalFeesCollectionsToday'] = FeesCollectionModel::getTotalFeesCollectionsToday();
                $data['totalWeek'] = WeekModel::getTotalWeek();
                $data['totalNoticeBoard'] = NoticeBoardMessageModel::getTotalNoticeBoardMessage();
                $data['totalNoticeBoardTeacher'] = NoticeBoardMessageModel::getTotalNoticeBoardMessageTeacher();
                $data['totalClassTimetable'] = ClassTimetableModel::getTotalClassTimetable();
                $data['totalClassTimetableTeacher'] = ClassTimetableModel::getTotalClassTimetableTeacher();
                $data['totalClassTimetableStudent'] = ClassTimetableModel::getTotalClassTimetableStudent();
                $data['totalHomework'] = HomeworkModel::getTotalHomework();
                $data['totalWork'] = WorkModel::getTotalWork();
                $data['totalAttendance'] = StudentAttendanceModel::getTotalAttendance();
                $data['totalAttendanceStudentPresent'] = StudentAttendanceModel::getTotalAttendanceTypeStudent(1);
                $data['totalAttendanceStudentLate'] = StudentAttendanceModel::getTotalAttendanceTypeStudent(2);
                $data['totalAttendanceStudentAbsent'] = StudentAttendanceModel::getTotalAttendanceTypeStudent(3);
                $data['totalAttendanceStudentHalfDay'] = StudentAttendanceModel::getTotalAttendanceTypeStudent(4);

                return Inertia::render('Dashboard/Admin', $data);

            case 2:

                $data['totalUser'] = User::getTotalUser();
                $data['totalStudent'] = User::getTotalUserWithUserType(3);
                $data['totalTeacherStudent'] = User::getTotalTeacherStudent();
                $data['totalClass'] = ClassModel::getTotalClass();
                $data['totalSubject'] = SubjectModel::getTotalSubject();
                $data['totalTeacherSubject'] = ClassTeacherModel::getTotalTeacherSubject();
                $data['totalClassAndSubject'] = ClassSubjectModel::getTotalClassAndSubject();
                $data['totalTeacherClass'] = ClassTeacherModel::getTotalTeacherClass();
                $data['totalCommunicate'] = CommunicateModel::getTotalCommunicate();
                $data['totalCommunicateTeacher'] = CommunicateModel::getTotalCommunicateCreatedByTeacher();
                $data['totalExamTeacher'] = ScheduleModel::getTotalExamClassTimebableTeacher();
                $data['totalExam'] = ExaminationModel::getTotalExam();
                $data['totalExamTeacherToday'] = ScheduleModel::getTotalExamClassTimebableTeacherToday();
                $data['totalNoticeBoard'] = NoticeBoardMessageModel::getTotalNoticeBoardMessage();
                $data['totalNoticeBoardTeacher'] = NoticeBoardMessageModel::getTotalNoticeBoardMessageTeacher();

                return Inertia::render('Dashboard/Teacher', $data);

            case 3:

                $data['totalUser'] = User::getTotalUser();
                $data['totalStudent'] = User::getTotalUserWithUserType(3);
                $data['totalSubject'] = SubjectModel::getTotalSubject();
                $data['totalStudentSubject'] = ClassSubjectModel::getTotalStudentSubject();
                $data['totalNoticeBoard'] = NoticeBoardMessageModel::getTotalNoticeBoardMessage();
                $data['totalNoticeBoardStudent'] = NoticeBoardMessageModel::getTotalNoticeBoardMessageStudent();
                $data['totalCommunicate'] = CommunicateModel::getTotalCommunicate();
                $data['totalFeesCollections'] = FeesCollectionModel::getFeesCollectionsByStudent();
                $data['totalFeesCollectionsAmoutPaidByStudent'] = FeesCollectionModel::getTotalFeesCollectionsAmountPaidByStudent();
                $data['totalFeesCollectionsAmountStudent'] = FeesCollectionModel::getTotalFeesCollectionsAmountStudent();
                $data['totalExamStudent'] = ScheduleModel::getTotalExamStudent();
                $data['totalClassTimetableStudent'] = ClassTimetableModel::getTotalClassTimetableStudent();
                $data['totalClassTimetable'] = ClassTimetableModel::getTotalClassTimetable();
                $data['totalExam'] = ExaminationModel::getTotalExam();
                $data['totalHomeworkStudent'] = HomeworkModel::getTotalHomeworkStudent();
                $data['totalHomework'] = HomeworkModel::getTotalHomework();
                $data['totalWorkStudent'] = WorkModel::getTotalWorkStudent();
                $data['totalWork'] = WorkModel::getTotalWork();
                $data['totalAttendanceStudent'] = StudentAttendanceModel::getTotalAttendanceStudent();
                $data['totalByAttendanceTypeStudentPresent'] = StudentAttendanceModel::getTotalAttendanceTypeByStudent(1, 3);
                $data['totalByAttendanceTypeStudentLate'] = StudentAttendanceModel::getTotalAttendanceTypeByStudent(2, 3);
                $data['totalByAttendanceTypeStudentAbsent'] = StudentAttendanceModel::getTotalAttendanceTypeByStudent(3, 3);
                $data['totalByAttendanceTypeStudentHalfDay'] = StudentAttendanceModel::getTotalAttendanceTypeByStudent(4, 3);
                $data['totalAttendance'] = StudentAttendanceModel::getTotalAttendance();

                return Inertia::render('Dashboard/Student', $data);

            case 4:

                $student_ids = User::getStudentIds();
                $class_ids = User::getClassIds();

                if (!empty($student_ids) && !empty($class_ids)) {
                    $data['totalFeesCollectionsAmoutPaidByStudents'] = FeesCollectionModel::getTotalFeesCollectionsAmountPaidByStudents($student_ids);
                    $data['totalFeesCollectionsAmountStudents'] = FeesCollectionModel::getTotalFeesCollectionsAmountStudents($student_ids);
                    $data['totalByAttendanceTypeStudentPresent'] = StudentAttendanceModel::getTotalByAttendanceTypeStudent(1, $student_ids);
                    $data['totalByAttendanceTypeStudentLate'] = StudentAttendanceModel::getTotalByAttendanceTypeStudent(2, $student_ids);
                    $data['totalByAttendanceTypeStudentAbsent'] = StudentAttendanceModel::getTotalByAttendanceTypeStudent(3, $student_ids);
                    $data['totalByAttendanceTypeStudentHalfDay'] = StudentAttendanceModel::getTotalByAttendanceTypeStudent(4, $student_ids);
                    $data['totalHomeworkStudent'] = HomeworkModel::getTotalHomeworkParentStudent($student_ids);
                    $data['totalWorkStudent'] = WorkModel::getTotalWorkParentStudent($class_ids, $student_ids);
                    $data['totalHomework'] = HomeworkModel::getTotalHomework();
                    $data['totalWork'] = WorkModel::getTotalWork();
                }

                $data['totalUser'] = User::getTotalUser();
                $data['totalParent'] = User::getTotalUserWithUserType(4);
                $data['totalStudent'] = User::getTotalUserWithUserType(3);
                $data['totalParentStudent'] = User::getTotalParentStudent();
                $data['totalNoticeBoard'] = NoticeBoardMessageModel::getTotalNoticeBoardMessage();
                $data['totalNoticeBoardParent'] = NoticeBoardMessageModel::getTotalNoticeBoardMessageParent();
                $data['totalAttendance'] = StudentAttendanceModel::getTotalAttendance();
                $data['totalCommunicate'] = CommunicateModel::getTotalCommunicate();

                return Inertia::render('Dashboard/Parent', $data);

        }
        return Inertia::render('Auth/Login');
    }
}
