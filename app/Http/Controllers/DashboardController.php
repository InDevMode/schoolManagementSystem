<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\ClassTeacherModel;
use App\Models\ClassTimetableModel;
use App\Models\CommunicateModel;
use App\Models\ExaminationModel;
use App\Models\FeesCollectionModel;
use App\Models\NoticeBoardMessageModel;
use App\Models\ScheduleModel;
use App\Models\SubjectModel;
use App\Models\User;
use App\Models\WeekModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

                return view('admin.dashboard', $data);

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

                return view('teacher.dashboard', $data);

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
                // $data['totalExamStudent'] = ExaminationModel::getTotalExamStudent();
                $data['totalExamStudent'] = ScheduleModel::getTotalExamStudent();
                $data['totalClassTimetableStudent'] = ClassTimetableModel::getTotalClassTimetableStudent();
                $data['totalClassTimetable'] = ClassTimetableModel::getTotalClassTimetable();
                $data['totalExam'] = ExaminationModel::getTotalExam();


                return view('student.dashboard', $data);

            case 4:

                $data['totalUser'] = User::getTotalUser();
                $data['totalStudent'] = User::getTotalUserWithUserType(3);
                $data['totalNoticeBoard'] = NoticeBoardMessageModel::getTotalNoticeBoardMessage();
                $data['totalNoticeBoardParent'] = NoticeBoardMessageModel::getTotalNoticeBoardMessageParent();
                $data['totalParent'] = User::getTotalUserWithUserType(4);

                return view('parent.dashboard', $data);

        }
        return view('auth.login');
    }
}
