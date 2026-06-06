<?php

namespace App\Http\Controllers;

use App\Models\BulletinModel;
use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\ClassTeacherModel;
use App\Models\ClassTimetableModel;
use App\Models\CommunicateModel;
use App\Models\EvaluationModel;
use App\Models\FeesCollectionModel;
use App\Models\HomeworkModel;
use App\Models\NoticeBoardMessageModel;
use App\Models\PeriodModel;
use App\Models\StaffEventModel;
use App\Models\StaffLeaveModel;
use App\Models\StaffModel;
use App\Models\StudentAttendanceModel;
use App\Models\SubjectModel;
use App\Models\User;
use App\Models\WeekModel;
use App\Models\WorkModel;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $data = [];
        $data['header_title'] = 'Tableau de bord';

        // Événements et congés visibles sur tous les dashboards
        $upcomingEvents = [];
        $calendarEvents = [];
        try {
            $upcomingEvents = StaffEventModel::getUpcoming(5);
            $calendarEvents = StaffEventModel::getCalendarEvents();
        } catch (\Exception $e) {
            // Tables pas encore migrées — on ignore silencieusement
        }

        $currentPeriod = null;
        try {
            $currentPeriod = PeriodModel::getAllPeriods()->first();
        } catch (\Exception $e) {}

        switch ((int) Auth::user()->user_type) {

            // ── Super Admin ─────────────────────────────────────────────────
            case 0:
                $data['totalUser']    = User::getTotalUser();
                $data['totalAdmin']   = User::getTotalUserWithUserType(1);
                $data['totalTeacher'] = User::getTotalUserWithUserType(2);
                $data['totalStudent'] = User::getTotalUserWithUserType(3);
                $data['totalParent']  = User::getTotalUserWithUserType(4);
                $data['totalClass']   = ClassModel::getTotalClass();
                $data['totalSubject'] = SubjectModel::getTotalSubject();
                $data['totalFeesCollections']      = FeesCollectionModel::getTotalFeesCollections();
                $data['totalFeesCollectionsToday'] = FeesCollectionModel::getTotalFeesCollectionsToday();
                $data['totalCommunicate']          = CommunicateModel::getTotalCommunicate();
                $data['totalNoticeBoard']          = NoticeBoardMessageModel::getTotalNoticeBoardMessage();
                $data['totalHomework']             = HomeworkModel::getTotalHomework();
                $data['totalWork']                 = WorkModel::getTotalWork();
                $data['totalAttendance']           = StudentAttendanceModel::getTotalAttendance();
                $data['totalAttendanceStudentPresent']  = StudentAttendanceModel::getTotalAttendanceTypeStudent(1);
                $data['totalAttendanceStudentLate']     = StudentAttendanceModel::getTotalAttendanceTypeStudent(2);
                $data['totalAttendanceStudentAbsent']   = StudentAttendanceModel::getTotalAttendanceTypeStudent(3);
                $data['totalAttendanceStudentHalfDay']  = StudentAttendanceModel::getTotalAttendanceTypeStudent(4);
                $data['totalWeek']             = WeekModel::getTotalWeek();
                $data['totalClassTimetable']   = ClassTimetableModel::getTotalClassTimetable();
                $data['totalRoles']            = \Spatie\Permission\Models\Role::count();
                $data['totalPermissions']      = \Spatie\Permission\Models\Permission::count();
                // RH + nouveaux
                $data['totalStaff']          = $this->safeStat(fn() => StaffModel::getTotalActive());
                $data['totalPendingLeaves']  = $this->safeStat(fn() => StaffLeaveModel::getPendingCount());
                $data['totalPendingGrades']  = $this->safeStat(fn() => \App\Models\GradeModel::where('validated', false)->where('is_delete', 0)->count());
                $data['totalUpcomingEvents'] = $this->safeStat(fn() => StaffEventModel::where('is_delete', 0)->whereDate('event_date', '>=', today())->count());
                $data['currentLeaves']       = $this->safeStat(fn() => StaffModel::getCurrentLeaves(), []);
                $data['upcomingEvents']      = $upcomingEvents;
                $data['calendarEvents']      = $calendarEvents;
                $data['currentPeriod']       = $currentPeriod;
                return Inertia::render('Dashboard/SuperAdmin', $data);

            // ── Admin ────────────────────────────────────────────────────────
            case 1:
                $data['totalUser']    = User::getTotalUser();
                $data['totalAdmin']   = User::getTotalUserWithUserType(1);
                $data['totalTeacher'] = User::getTotalUserWithUserType(2);
                $data['totalStudent'] = User::getTotalUserWithUserType(3);
                $data['totalParent']  = User::getTotalUserWithUserType(4);
                $data['totalClass']   = ClassModel::getTotalClass();
                $data['totalSubject'] = SubjectModel::getTotalSubject();
                $data['totalFeesCollections']      = FeesCollectionModel::getTotalFeesCollections();
                $data['totalFeesCollectionsToday'] = FeesCollectionModel::getTotalFeesCollectionsToday();
                $data['totalCommunicate']          = CommunicateModel::getTotalCommunicate();
                $data['totalNoticeBoard']          = NoticeBoardMessageModel::getTotalNoticeBoardMessage();
                $data['totalNoticeBoardTeacher']   = NoticeBoardMessageModel::getTotalNoticeBoardMessageTeacher();
                $data['totalClassTimetable']       = ClassTimetableModel::getTotalClassTimetable();
                $data['totalClassTimetableTeacher']= ClassTimetableModel::getTotalClassTimetableTeacher();
                $data['totalClassTimetableStudent']= ClassTimetableModel::getTotalClassTimetableStudent();
                $data['totalHomework']             = HomeworkModel::getTotalHomework();
                $data['totalWork']                 = WorkModel::getTotalWork();
                $data['totalAttendance']           = StudentAttendanceModel::getTotalAttendance();
                $data['totalAttendanceStudentPresent']  = StudentAttendanceModel::getTotalAttendanceTypeStudent(1);
                $data['totalAttendanceStudentLate']     = StudentAttendanceModel::getTotalAttendanceTypeStudent(2);
                $data['totalAttendanceStudentAbsent']   = StudentAttendanceModel::getTotalAttendanceTypeStudent(3);
                $data['totalAttendanceStudentHalfDay']  = StudentAttendanceModel::getTotalAttendanceTypeStudent(4);
                // RH + nouveaux
                $data['totalStaff']          = $this->safeStat(fn() => StaffModel::getTotalActive());
                $data['totalPendingLeaves']  = $this->safeStat(fn() => StaffLeaveModel::getPendingCount());
                $data['totalPendingGrades']  = $this->safeStat(fn() => \App\Models\GradeModel::where('validated', false)->where('is_delete', 0)->count());
                $data['totalOpenEvals']      = $this->safeStat(fn() => \App\Models\EvaluationModel::where('status', 'open')->where('is_delete', 0)->count());
                $data['totalDraftBulletins'] = $this->safeStat(fn() => \App\Models\BulletinModel::where('status', 'draft')->where('is_delete', 0)->count());
                $data['currentLeaves']       = $this->safeStat(fn() => StaffModel::getCurrentLeaves(), []);
                $data['upcomingEvents']      = $upcomingEvents;
                $data['calendarEvents']      = $calendarEvents;
                $data['currentPeriod']       = $currentPeriod;
                return Inertia::render('Dashboard/Admin', $data);

            // ── Professeur ───────────────────────────────────────────────────
            case 2:
                $teacher_id = Auth::user()->id;
                $data['totalTeacherStudent']  = User::getTotalTeacherStudent();
                $data['totalTeacherClass']    = ClassTeacherModel::getTotalTeacherClass();
                $data['totalTeacherSubject']  = ClassTeacherModel::getTotalTeacherSubject();
                $data['totalNoticeBoard']     = NoticeBoardMessageModel::getTotalNoticeBoardMessage();
                $data['totalNoticeBoardTeacher'] = NoticeBoardMessageModel::getTotalNoticeBoardMessageTeacher();
                // Nouveaux
                $data['upcomingEvents']      = $upcomingEvents;
                $data['calendarEvents']      = $calendarEvents;
                $data['currentPeriod']       = $currentPeriod;
                $data['myRecentEvaluations'] = $this->safeStat(
                    fn() => EvaluationModel::getByTeacher($teacher_id)->take(5)->values(), []
                );
                return Inertia::render('Dashboard/Teacher', $data);

            // ── Élève ────────────────────────────────────────────────────────
            case 3:
                $student_id = Auth::user()->id;
                $data['totalStudentSubject']  = ClassSubjectModel::getTotalStudentSubject();
                $data['totalNoticeBoard']     = NoticeBoardMessageModel::getTotalNoticeBoardMessage();
                $data['totalNoticeBoardStudent'] = NoticeBoardMessageModel::getTotalNoticeBoardMessageStudent();
                $data['totalFeesCollections'] = FeesCollectionModel::getFeesCollectionsByStudent();
                $data['totalFeesCollectionsAmoutPaidByStudent'] = FeesCollectionModel::getTotalFeesCollectionsAmountPaidByStudent();
                $data['totalFeesCollectionsAmountStudent']      = FeesCollectionModel::getTotalFeesCollectionsAmountStudent();
                $data['totalClassTimetableStudent'] = ClassTimetableModel::getTotalClassTimetableStudent();
                $data['totalHomeworkStudent'] = HomeworkModel::getTotalHomeworkStudent();
                $data['totalWorkStudent']     = WorkModel::getTotalWorkStudent();
                $data['totalAttendanceStudent'] = StudentAttendanceModel::getTotalAttendanceStudent();
                $data['totalByAttendanceTypeStudentPresent']  = StudentAttendanceModel::getTotalAttendanceTypeByStudent(1, 3);
                $data['totalByAttendanceTypeStudentLate']     = StudentAttendanceModel::getTotalAttendanceTypeByStudent(2, 3);
                $data['totalByAttendanceTypeStudentAbsent']   = StudentAttendanceModel::getTotalAttendanceTypeByStudent(3, 3);
                $data['totalByAttendanceTypeStudentHalfDay']  = StudentAttendanceModel::getTotalAttendanceTypeByStudent(4, 3);
                // Nouveaux
                $data['upcomingEvents'] = $upcomingEvents;
                $data['calendarEvents'] = $calendarEvents;
                $data['currentPeriod']  = $currentPeriod;
                $data['myBulletins']    = $this->safeStat(
                    fn() => BulletinModel::getByStudent($student_id)->take(3)->values(), []
                );
                return Inertia::render('Dashboard/Student', $data);

            // ── Parent ───────────────────────────────────────────────────────
            case 4:
                $student_ids = User::getStudentIds();
                $class_ids   = User::getClassIds();
                if (!empty($student_ids) && !empty($class_ids)) {
                    $data['totalFeesCollectionsAmoutPaidByStudents'] = FeesCollectionModel::getTotalFeesCollectionsAmountPaidByStudents($student_ids);
                    $data['totalFeesCollectionsAmountStudents']      = FeesCollectionModel::getTotalFeesCollectionsAmountStudents($student_ids);
                    $data['totalByAttendanceTypeStudentPresent']  = StudentAttendanceModel::getTotalByAttendanceTypeStudent(1, $student_ids);
                    $data['totalByAttendanceTypeStudentLate']     = StudentAttendanceModel::getTotalByAttendanceTypeStudent(2, $student_ids);
                    $data['totalByAttendanceTypeStudentAbsent']   = StudentAttendanceModel::getTotalByAttendanceTypeStudent(3, $student_ids);
                    $data['totalByAttendanceTypeStudentHalfDay']  = StudentAttendanceModel::getTotalByAttendanceTypeStudent(4, $student_ids);
                    $data['totalHomeworkStudent'] = HomeworkModel::getTotalHomeworkParentStudent($student_ids);
                    $data['totalWorkStudent']     = WorkModel::getTotalWorkParentStudent($class_ids, $student_ids);
                }
                $data['totalParentStudent']     = User::getTotalParentStudent();
                $data['totalNoticeBoard']       = NoticeBoardMessageModel::getTotalNoticeBoardMessage();
                $data['totalNoticeBoardParent'] = NoticeBoardMessageModel::getTotalNoticeBoardMessageParent();
                // Nouveaux
                $data['upcomingEvents'] = $upcomingEvents;
                $data['calendarEvents'] = $calendarEvents;
                $data['currentPeriod']  = $currentPeriod;
                $childrenBulletins = [];
                foreach ($student_ids as $sid) {
                    $student = User::find($sid);
                    if ($student) {
                        $childrenBulletins[] = [
                            'student'   => $student,
                            'bulletins' => $this->safeStat(fn() => BulletinModel::getByStudent($sid)->take(2)->values(), []),
                        ];
                    }
                }
                $data['childrenBulletins'] = $childrenBulletins;
                return Inertia::render('Dashboard/Parent', $data);
        }

        return Inertia::render('Auth/Login');
    }

    /**
     * Exécute un callback de façon sécurisée (tables pas encore migrées, etc.)
     */
    private function safeStat(callable $fn, mixed $default = 0): mixed
    {
        try {
            return $fn();
        } catch (\Exception $e) {
            return $default;
        }
    }
}
