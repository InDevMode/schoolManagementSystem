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
use Illuminate\Support\Facades\DB;
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
            $upcomingEvents = StaffEventModel::getUpcoming(10);
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
                $data['totalExam']    = $this->safeStat(fn() => PeriodModel::where('is_delete', 0)->count());
                $data['totalFeesCollections']      = FeesCollectionModel::getTotalFeesCollections();
                $data['totalFeesCollectionsToday'] = FeesCollectionModel::getTotalFeesCollectionsToday();
                $data['totalCommunicate']          = CommunicateModel::getTotalCommunicate();
                $data['totalNoticeBoard']          = NoticeBoardMessageModel::getTotalNoticeBoardMessage();
                $data['totalHomework']             = HomeworkModel::getTotalHomework();
                $data['totalWork']                 = WorkModel::getTotalWork();
                $data['totalAttendance']           = StudentAttendanceModel::getTotalAttendance();
                $data['totalAttendanceStudentPresent']  = StudentAttendanceModel::getTotalAttendanceTypeStudentBySchool(1);
                $data['totalAttendanceStudentLate']     = StudentAttendanceModel::getTotalAttendanceTypeStudentBySchool(2);
                $data['totalAttendanceStudentAbsent']   = StudentAttendanceModel::getTotalAttendanceTypeStudentBySchool(3);
                $data['totalAttendanceStudentHalfDay']  = StudentAttendanceModel::getTotalAttendanceTypeStudentBySchool(4);
                $data['attendanceBySchool']             = $this->safeStat(fn() => $this->getAttendanceBySchool(), []);
                $data['totalWeek']             = WeekModel::getTotalWeek();
                $data['totalClassTimetable']   = ClassTimetableModel::getTotalClassTimetable();
                $data['totalRoles']            = \Spatie\Permission\Models\Role::count();
                $data['totalPermissions']      = \Spatie\Permission\Models\Permission::count();
                // ── Sexe apprenants ──
                $data['totalStudentMale']   = $this->safeStat(fn() => DB::table('users')->where('user_type', 3)->where('is_delete', 0)->where('gender', 'male')->count());
                $data['totalStudentFemale'] = $this->safeStat(fn() => DB::table('users')->where('user_type', 3)->where('is_delete', 0)->where('gender', 'female')->count());
                // ── Sexe professeurs ──
                $data['totalTeacherMale']   = $this->safeStat(fn() => DB::table('users')->where('user_type', 2)->where('is_delete', 0)->where('gender', 'male')->count());
                $data['totalTeacherFemale'] = $this->safeStat(fn() => DB::table('users')->where('user_type', 2)->where('is_delete', 0)->where('gender', 'female')->count());
                // ── Sexe parents ──
                $data['totalParentMale']    = $this->safeStat(fn() => DB::table('users')->where('user_type', 4)->where('is_delete', 0)->where('gender', 'male')->count());
                $data['totalParentFemale']  = $this->safeStat(fn() => DB::table('users')->where('user_type', 4)->where('is_delete', 0)->where('gender', 'female')->count());
                // ── Sexe admins ──
                $data['totalAdminMale']     = $this->safeStat(fn() => DB::table('users')->where('user_type', 1)->where('is_delete', 0)->where('gender', 'male')->count());
                $data['totalAdminFemale']   = $this->safeStat(fn() => DB::table('users')->where('user_type', 1)->where('is_delete', 0)->where('gender', 'female')->count());
                // ── Super Admins ──
                $data['totalSuperAdmin']       = $this->safeStat(fn() => DB::table('users')->where('user_type', 0)->where('is_delete', 0)->count());
                $data['totalSuperAdminMale']   = $this->safeStat(fn() => DB::table('users')->where('user_type', 0)->where('is_delete', 0)->where('gender', 'male')->count());
                $data['totalSuperAdminFemale'] = $this->safeStat(fn() => DB::table('users')->where('user_type', 0)->where('is_delete', 0)->where('gender', 'female')->count());
                // ── Infos supplémentaires système ──
                $data['totalSchools']               = $this->safeStat(fn() => DB::table('schools')->where('is_delete', 0)->count());
                $data['totalDeletionLogs']          = $this->safeStat(fn() => DB::table('deletion_logs')->count());
                $data['totalPermissionAssignments'] = $this->safeStat(fn() => DB::table('model_has_permissions')->count());
                $data['totalClassSubject']          = $this->safeStat(fn() => DB::table('class_subjects')->where('is_delete', 0)->count());
                // ── Stats par école ──
                $data['schoolsStats']          = $this->safeStat(fn() => $this->getSchoolsStats(), []);
                // ── Présences par mois ──
                $data['attendanceByMonth']  = $this->safeStat(fn() => $this->getAttendanceByMonth(), []);
                // ── Bulletins ──
                $data['totalPublishedBulletins'] = $this->safeStat(fn() => BulletinModel::where('status', 'published')->where('is_delete', 0)->count());
                $data['totalDraftBulletins']     = $this->safeStat(fn() => BulletinModel::where('status', 'draft')->where('is_delete', 0)->count());
                // ── Moyennes remarquables ──
                $data['topAverage']    = $this->safeStat(fn() => BulletinModel::where('is_delete', 0)->where('status', 'published')->max('average'));
                $data['lowAverage']    = $this->safeStat(fn() => BulletinModel::where('is_delete', 0)->where('status', 'published')->whereNotNull('average')->min('average'));
                $data['successRate']   = $this->safeStat(fn() => $this->computeSuccessRate());
                // ── RH ──
                $data['totalStaff']          = $this->safeStat(fn() => StaffModel::getTotalActive());
                $data['totalPendingLeaves']  = $this->safeStat(fn() => StaffLeaveModel::getPendingCount());
                $data['totalApprovedLeaves'] = $this->safeStat(fn() => DB::table('staff_leaves')->where('status', 'approved')->where('is_delete', 0)->count());
                $data['totalPendingGrades']  = $this->safeStat(fn() => $this->countPendingGrades());
                $data['totalOpenEvals']      = $this->safeStat(fn() => EvaluationModel::where('status', 'open')->where('is_delete', 0)->count());
                $data['totalUpcomingEvents'] = $this->safeStat(fn() => StaffEventModel::where('is_delete', 0)->whereDate('event_date', '>=', today())->count());
                $data['staffRoleData']       = $this->safeStat(fn() => $this->getStaffRoleData(), []);
                $data['currentLeaves']       = $this->safeStat(fn() => StaffModel::getCurrentLeaves(), []);
                // ── Contributions détaillées ──
                $data['feesStats']           = $this->safeStat(fn() => $this->getFeesStats(), []);
                $data['upcomingEvents']      = $upcomingEvents;
                $data['calendarEvents']      = $calendarEvents;
                $data['currentPeriod']       = $currentPeriod;
                return Inertia::render('Dashboard/SuperAdmin', $data);

            // ── Admin ────────────────────────────────────────────────────────
            case 1:
                $schoolId = Auth::user()->school_id;
                $data['totalUser']    = User::getTotalUser($schoolId);
                $data['totalAdmin']   = User::getTotalUserWithUserType(1, $schoolId);
                $data['totalTeacher'] = User::getTotalUserWithUserType(2, $schoolId);
                $data['totalStudent'] = User::getTotalUserWithUserType(3, $schoolId);
                $data['totalParent']  = User::getTotalUserWithUserType(4, $schoolId);
                $data['totalClass']   = ClassModel::getTotalClass($schoolId);
                $data['totalSubject'] = SubjectModel::getTotalSubject();
                $data['totalExam']    = $this->safeStat(fn() => PeriodModel::where('is_delete', 0)->count());
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
                $data['totalAttendance']           = StudentAttendanceModel::getTotalAttendanceBySchool($schoolId);
                $data['totalAttendanceStudentPresent']  = StudentAttendanceModel::getTotalAttendanceTypeStudentBySchool(1, $schoolId);
                $data['totalAttendanceStudentLate']     = StudentAttendanceModel::getTotalAttendanceTypeStudentBySchool(2, $schoolId);
                $data['totalAttendanceStudentAbsent']   = StudentAttendanceModel::getTotalAttendanceTypeStudentBySchool(3, $schoolId);
                $data['totalAttendanceStudentHalfDay']  = StudentAttendanceModel::getTotalAttendanceTypeStudentBySchool(4, $schoolId);
                // ── Sexe apprenants ──
                $data['totalStudentMale']   = $this->safeStat(fn() => DB::table('users')->where('user_type', 3)->where('is_delete', 0)->where('school_id', $schoolId)->where('gender', 'male')->count());
                $data['totalStudentFemale'] = $this->safeStat(fn() => DB::table('users')->where('user_type', 3)->where('is_delete', 0)->where('school_id', $schoolId)->where('gender', 'female')->count());
                // ── Sexe professeurs ──
                $data['totalTeacherMale']   = $this->safeStat(fn() => DB::table('users')->where('user_type', 2)->where('is_delete', 0)->where('school_id', $schoolId)->where('gender', 'male')->count());
                $data['totalTeacherFemale'] = $this->safeStat(fn() => DB::table('users')->where('user_type', 2)->where('is_delete', 0)->where('school_id', $schoolId)->where('gender', 'female')->count());
                // ── Sexe parents ──
                $data['totalParentMale']    = $this->safeStat(fn() => DB::table('users')->where('user_type', 4)->where('is_delete', 0)->where('school_id', $schoolId)->where('gender', 'male')->count());
                $data['totalParentFemale']  = $this->safeStat(fn() => DB::table('users')->where('user_type', 4)->where('is_delete', 0)->where('school_id', $schoolId)->where('gender', 'female')->count());
                // ── Sexe admins ──
                $data['totalAdminMale']     = $this->safeStat(fn() => DB::table('users')->where('user_type', 1)->where('is_delete', 0)->where('school_id', $schoolId)->where('gender', 'male')->count());
                $data['totalAdminFemale']   = $this->safeStat(fn() => DB::table('users')->where('user_type', 1)->where('is_delete', 0)->where('school_id', $schoolId)->where('gender', 'female')->count());
                // ── Présences par mois ──
                $data['attendanceByMonth']  = $this->safeStat(fn() => $this->getAttendanceByMonth($schoolId), []);
                // ── Bulletins ──
                $data['totalPublishedBulletins'] = $this->safeStat(fn() => BulletinModel::where('status', 'published')->where('is_delete', 0)->where('school_id', $schoolId)->count());
                $data['totalDraftBulletins']     = $this->safeStat(fn() => BulletinModel::where('status', 'draft')->where('is_delete', 0)->where('school_id', $schoolId)->count());
                // ── RH ──
                $data['totalStaff']          = $this->safeStat(fn() => StaffModel::getTotalActive());
                $data['totalPendingLeaves']  = $this->safeStat(fn() => StaffLeaveModel::getPendingCount());
                $data['totalPendingGrades']  = $this->safeStat(fn() => $this->countPendingGrades());
                $data['totalOpenEvals']      = $this->safeStat(fn() => EvaluationModel::where('status', 'open')->where('is_delete', 0)->count());
                $data['totalUpcomingEvents'] = $this->safeStat(fn() => StaffEventModel::where('is_delete', 0)->whereDate('event_date', '>=', today())->count());
                $data['currentLeaves']       = $this->safeStat(fn() => StaffModel::getCurrentLeaves(), []);
                // ── Contributions détaillées ──
                $data['feesStats']           = $this->safeStat(fn() => $this->getFeesStats($schoolId), []);
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
                $data['totalNoticeBoard']        = NoticeBoardMessageModel::getTotalNoticeBoardMessage();
                $data['totalNoticeBoardTeacher'] = NoticeBoardMessageModel::getTotalNoticeBoardMessageTeacher();
                // ── Examens ──
                $data['totalExamTeacher']      = $this->safeStat(fn() => EvaluationModel::where('teacher_id', $teacher_id)->where('is_delete', 0)->count());
                $data['totalExamTeacherToday'] = $this->safeStat(fn() => EvaluationModel::where('teacher_id', $teacher_id)->where('is_delete', 0)->whereDate('eval_date', today())->count());
                $data['totalTeacherHomework']  = $this->safeStat(fn() => DB::table('homework')->where('teacher_id', $teacher_id)->where('is_delete', 0)->count());
                $data['totalPendingEvals']     = $this->safeStat(fn() => $this->countPendingGrades($teacher_id));
                // ── Présences de mes classes ──
                $myClassIds = $this->safeStat(fn() => DB::table('class_teacher')->where('teacher_id', $teacher_id)->pluck('class_id')->toArray(), []);
                if (!empty($myClassIds)) {
                    $myStudentIds = DB::table('users')->whereIn('class_id', $myClassIds)->where('user_type', 3)->where('is_delete', 0)->pluck('id')->toArray();
                    if (!empty($myStudentIds)) {
                        $data['totalAttPresent']  = $this->safeStat(fn() => DB::table('student_attendance')->whereIn('student_id', $myStudentIds)->where('type', 1)->where('is_delete', 0)->count());
                        $data['totalAttLate']     = $this->safeStat(fn() => DB::table('student_attendance')->whereIn('student_id', $myStudentIds)->where('type', 2)->where('is_delete', 0)->count());
                        $data['totalAttAbsent']   = $this->safeStat(fn() => DB::table('student_attendance')->whereIn('student_id', $myStudentIds)->where('type', 3)->where('is_delete', 0)->count());
                        $data['totalAttHalfDay']  = $this->safeStat(fn() => DB::table('student_attendance')->whereIn('student_id', $myStudentIds)->where('type', 4)->where('is_delete', 0)->count());
                    }
                }
                $data['upcomingEvents']      = $upcomingEvents;
                $data['calendarEvents']      = $calendarEvents;
                $data['currentPeriod']       = $currentPeriod;
                $data['myRecentEvaluations'] = $this->safeStat(
                    fn() => EvaluationModel::getByTeacher($teacher_id)->take(8)->values(), []
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
                $data['totalExamStudent']     = $this->safeStat(fn() => DB::table('class_timetable')->join('users', 'users.class_id', '=', 'class_timetable.class_id')->where('users.id', $student_id)->where('class_timetable.is_delete', 0)->count());
                $data['totalWorkStudent']     = WorkModel::getTotalWorkStudent();
                $data['totalAttendanceStudent'] = StudentAttendanceModel::getTotalAttendanceStudent();
                $data['totalByAttendanceTypeStudentPresent']  = StudentAttendanceModel::getTotalAttendanceTypeByStudent(1, 3);
                $data['totalByAttendanceTypeStudentLate']     = StudentAttendanceModel::getTotalAttendanceTypeByStudent(2, 3);
                $data['totalByAttendanceTypeStudentAbsent']   = StudentAttendanceModel::getTotalAttendanceTypeByStudent(3, 3);
                $data['totalByAttendanceTypeStudentHalfDay']  = StudentAttendanceModel::getTotalAttendanceTypeByStudent(4, 3);
                $data['upcomingEvents'] = $upcomingEvents;
                $data['calendarEvents'] = $calendarEvents;
                $data['currentPeriod']  = $currentPeriod;
                $data['myBulletins']    = $this->safeStat(
                    fn() => BulletinModel::getByStudent($student_id)->values(), []
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
                $data['upcomingEvents'] = $upcomingEvents;
                $data['calendarEvents'] = $calendarEvents;
                $data['currentPeriod']  = $currentPeriod;
                $childrenBulletins = [];
                foreach ($student_ids as $sid) {
                    $student = User::find($sid);
                    if ($student) {
                        // Enrichir avec class_name
                        $className = DB::table('class')->where('id', $student->class_id)->value('name');
                        $student->class_name = $className;
                        $childrenBulletins[] = [
                            'student'   => $student,
                            'bulletins' => $this->safeStat(fn() => BulletinModel::getByStudent($sid)->take(3)->values(), []),
                        ];
                    }
                }
                $data['childrenBulletins'] = $childrenBulletins;
                return Inertia::render('Dashboard/Parent', $data);

            // ── Rôle personnalisé (user_type >= 5) ──────────────────────────
            default:
                $user        = Auth::user();
                $permissions = $user->getAllPermissions()->pluck('name')->toArray();

                $data['userPermissions'] = $permissions;
                $data['upcomingEvents']  = $upcomingEvents;
                $data['currentPeriod']   = $currentPeriod;

                // Données conditionnelles selon permissions
                if (in_array('view.users.students', $permissions)) {
                    $data['totalStudent'] = User::getTotalUserWithUserType(3);
                }
                if (in_array('view.users.teachers', $permissions)) {
                    $data['totalTeacher'] = User::getTotalUserWithUserType(2);
                }
                if (in_array('view.users.parents', $permissions)) {
                    $data['totalParent'] = User::getTotalUserWithUserType(4);
                }
                if (in_array('view.academics.classes', $permissions)) {
                    $data['totalClass'] = ClassModel::getTotalClass();
                }
                if (in_array('view.academics.subjects', $permissions)) {
                    $data['totalSubject'] = SubjectModel::getTotalSubject();
                }
                if (in_array('view.exams.periods', $permissions)) {
                    $data['totalExam'] = $this->safeStat(fn() => PeriodModel::where('is_delete', 0)->count());
                }
                if (in_array('view.attendance.manage', $permissions) || in_array('view.attendance.report', $permissions)) {
                    $data['totalAttendanceStudentPresent']  = StudentAttendanceModel::getTotalAttendanceTypeStudent(1);
                    $data['totalAttendanceStudentLate']     = StudentAttendanceModel::getTotalAttendanceTypeStudent(2);
                    $data['totalAttendanceStudentAbsent']   = StudentAttendanceModel::getTotalAttendanceTypeStudent(3);
                    $data['totalAttendanceStudentHalfDay']  = StudentAttendanceModel::getTotalAttendanceTypeStudent(4);
                }
                if (in_array('view.staff.list', $permissions)) {
                    $data['totalStaff'] = $this->safeStat(fn() => StaffModel::getTotalActive());
                }
                if (in_array('view.staff.leaves', $permissions)) {
                    $data['totalPendingLeaves'] = $this->safeStat(fn() => StaffLeaveModel::getPendingCount());
                }

                return Inertia::render('Dashboard/Default', $data);
        }
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Retourne les statistiques d'utilisateurs pour chaque école (Super Admin).
     */
    private function getSchoolsStats(): array
    {
        $schools = DB::table('schools')
            ->where('is_delete', 0)
            ->where('status', 1)
            ->select('id', 'school_name')
            ->orderBy('school_name')
            ->get();

        $result = [];
        foreach ($schools as $school) {
            $base = DB::table('users')
                ->where('is_delete', 0)
                ->where('school_id', $school->id);

            $result[] = [
                'school_id'      => $school->id,
                'school_name'    => $school->school_name,
                'total_users'    => (clone $base)->whereIn('user_type', [1,2,3,4])->count(),
                'total_students' => (clone $base)->where('user_type', 3)->count(),
                'total_teachers' => (clone $base)->where('user_type', 2)->count(),
                'total_parents'  => (clone $base)->where('user_type', 4)->count(),
                'total_admins'   => (clone $base)->where('user_type', 1)->count(),
                'total_staff'    => $this->safeStat(fn() => DB::table('staff')->where('is_delete', 0)->where('status', 'active')->where('school_id', $school->id)->count()),
            ];
        }

        return $result;
    }

    /**
     * Retourne les présences totales par école (pour le Super Admin).
     * Retourne un tableau [{school_name, present, late, absent, halfday}].
     * Note: attendance_type est stocké en string ('present','late','absent','half_day').
     */
    private function getAttendanceBySchool(): array
    {
        $rows = DB::table('attendances')
            ->join('users', 'users.id', '=', 'attendances.student_id')
            ->join('schools', 'schools.id', '=', 'users.school_id')
            ->where('attendances.is_delete', 0)
            ->where('users.is_delete', 0)
            ->where('users.user_type', 3)
            ->where('schools.is_delete', 0)
            ->selectRaw('schools.id as school_id, schools.school_name, attendances.attendance_type as type, COUNT(*) as total')
            ->groupBy('schools.id', 'schools.school_name', 'attendances.attendance_type')
            ->get();

        // Regrouper par école
        $schools = [];
        foreach ($rows as $row) {
            $id = $row->school_id;
            if (!isset($schools[$id])) {
                $schools[$id] = [
                    'school_id'   => $id,
                    'school_name' => $row->school_name,
                    'present'     => 0,
                    'late'        => 0,
                    'absent'      => 0,
                    'halfday'     => 0,
                ];
            }
            // attendance_type peut être string ('present','late','absent','half_day')
            // ou entier (1,2,3,4) selon la version des données
            $typeMap = [
                'present'  => 'present',  1 => 'present',
                'late'     => 'late',     2 => 'late',
                'absent'   => 'absent',   3 => 'absent',
                'half_day' => 'halfday',  4 => 'halfday',
                'halfday'  => 'halfday',
            ];
            $key = $typeMap[$row->type] ?? null;
            if ($key) {
                $schools[$id][$key] = (int) $row->total;
            }
        }

        // Inclure aussi les écoles sans présences (pour afficher 0)
        $allSchools = DB::table('schools')
            ->where('is_delete', 0)
            ->where('status', 1)
            ->select('id', 'school_name')
            ->orderBy('school_name')
            ->get();

        foreach ($allSchools as $s) {
            if (!isset($schools[$s->id])) {
                $schools[$s->id] = [
                    'school_id'   => $s->id,
                    'school_name' => $s->school_name,
                    'present'     => 0,
                    'late'        => 0,
                    'absent'      => 0,
                    'halfday'     => 0,
                ];
            }
        }

        return array_values($schools);
    }

    /**
     * Notes avec score saisi, non validées, évaluations non annulées.
     *
     * @param int|null $teacherId  Si fourni, filtre sur les évaluations de ce prof uniquement.
     */
    private function countPendingGrades(?int $teacherId = null): int
    {
        $q = DB::table('grades')
            ->join('evaluations', 'evaluations.id', '=', 'grades.evaluation_id')
            ->where('grades.validated', false)
            ->whereNotNull('grades.score')
            ->where('grades.is_delete', 0)
            ->where('evaluations.is_delete', 0)
            ->where('evaluations.status', '!=', 'cancelled');

        if ($teacherId !== null) {
            $q->where('evaluations.teacher_id', $teacherId);
        }

        return $q->count();
    }

    /**
     * Calcule le taux de réussite global (moyenne >= 10) sur la période courante.
     */
    private function computeSuccessRate(): ?float
    {
        $total = BulletinModel::where('is_delete', 0)->where('status', 'published')->count();
        if ($total === 0) return null;
        $pass = BulletinModel::where('is_delete', 0)->where('status', 'published')->where('average', '>=', 10)->count();
        return round(($pass / $total) * 100, 1);
    }

    /**
     * Retourne la répartition du personnel par rôle RH.
     */
    private function getStaffRoleData(): array
    {
        $rows = DB::table('staff')
            ->where('is_delete', 0)
            ->where('status', 'active')
            ->groupBy('role')
            ->selectRaw('role, count(*) as total')
            ->get();

        $labels = [
            'teacher'    => 'Professeurs',
            'director'   => 'Directeurs',
            'accountant' => 'Comptables',
            'supervisor' => 'Surveillants',
            'secretary'  => 'Secrétaires',
            'librarian'  => 'Bibliothécaires',
            'other'      => 'Autres',
        ];

        $result = [];
        foreach ($rows as $row) {
            $key = $labels[$row->role] ?? ucfirst($row->role);
            $result[$key] = (int) $row->total;
        }

        return $result;
    }

    /**
     * Retourne les présences par mois pour les 12 derniers mois.
     * La table `attendances` stocke attendance_type en string ('present','late','absent','half_day').
     */
    private function getAttendanceByMonth(?int $schoolId = null): array
    {
        $year = date('Y');
        $q = DB::table('attendances')
            ->where('attendances.is_delete', 0)
            ->whereYear('attendances.attendance_date', $year);

        if ($schoolId !== null) {
            $q->join('users', 'users.id', '=', 'attendances.student_id')
              ->where('users.school_id', $schoolId)
              ->where('users.is_delete', 0);
        }

        $rows = $q->selectRaw('attendances.attendance_type as type, MONTH(attendances.attendance_date) as month, COUNT(*) as total')
                  ->groupBy('attendances.attendance_type', DB::raw('MONTH(attendances.attendance_date)'))
                  ->get();

        $result = [
            'present'  => array_fill(1, 12, 0),
            'late'     => array_fill(1, 12, 0),
            'absent'   => array_fill(1, 12, 0),
            'halfday'  => array_fill(1, 12, 0),
        ];

        // Support string ET entier
        $typeMap = [
            'present'  => 'present',  1 => 'present',
            'late'     => 'late',     2 => 'late',
            'absent'   => 'absent',   3 => 'absent',
            'half_day' => 'halfday',  4 => 'halfday',
            'halfday'  => 'halfday',
        ];

        foreach ($rows as $row) {
            $key = $typeMap[$row->type] ?? null;
            if ($key && isset($result[$key][$row->month])) {
                $result[$key][$row->month] = (int) $row->total;
            }
        }

        return [
            'present' => array_values($result['present']),
            'late'    => array_values($result['late']),
            'absent'  => array_values($result['absent']),
            'halfday' => array_values($result['halfday']),
        ];
    }

    /**
     * Retourne les statistiques détaillées des contributions scolaires.
     *
     * @param int|null $schoolId  Si fourni, filtre sur l'école (Admin). Sinon global (SuperAdmin).
     */
    private function getFeesStats(?int $schoolId = null): array
    {
        $base = DB::table('feescollections')
            ->where('feescollections.is_delete', 0);

        if ($schoolId !== null) {
            $base = $base->join('users', 'users.id', '=', 'feescollections.student_id')
                         ->where('users.school_id', $schoolId)
                         ->where('users.is_delete', 0);
        }

        // ── Montants globaux ────────────────────────────────────────────────
        $amountRow = (clone $base)
            ->selectRaw('SUM(total_amount) as total, SUM(paid_amount) as paid, SUM(remaning_amount) as remaining')
            ->first();

        $totalAmount     = (int) ($amountRow->total     ?? 0);
        $paidAmount      = (int) ($amountRow->paid      ?? 0);
        $remainingAmount = (int) ($amountRow->remaining ?? 0);

        // ── Dossiers par statut (is_payment + payment_status) ───────────────
        $statusRows = (clone $base)
            ->selectRaw('payment_status, is_payment, COUNT(*) as total')
            ->groupBy('payment_status', 'is_payment')
            ->get();

        $countPaid    = 0;
        $countPending = 0;
        $countUnpaid  = 0;

        foreach ($statusRows as $row) {
            $status = strtolower((string) ($row->payment_status ?? ''));
            $isPaid = (int) ($row->is_payment ?? 0);
            $cnt    = (int) $row->total;

            if ($isPaid === 1 || $status === 'paid' || $status === 'completed') {
                $countPaid += $cnt;
            } elseif ($status === 'pending' || $status === '') {
                $countPending += $cnt;
            } else {
                $countUnpaid += $cnt;
            }
        }

        // ── Montants payés par statut ────────────────────────────────────────
        $paidAmountRow = (clone $base)
            ->where(function ($q) {
                $q->where('is_payment', 1)
                  ->orWhereIn('payment_status', ['Paid', 'Completed']);
            })
            ->selectRaw('SUM(paid_amount) as paid')
            ->first();

        $pendingAmountRow = (clone $base)
            ->where('is_payment', 0)
            ->where(function ($q) {
                $q->where('payment_status', 'Pending')
                  ->orWhereNull('payment_status');
            })
            ->selectRaw('SUM(total_amount) as amount')
            ->first();

        // ── Répartition par mode de paiement ───────────────────────────────
        $paymentTypeRows = (clone $base)
            ->whereNotNull('payment_type')
            ->where('payment_type', '!=', '')
            ->selectRaw('payment_type, COUNT(*) as count, SUM(paid_amount) as amount')
            ->groupBy('payment_type')
            ->orderByDesc('count')
            ->get();

        $paymentTypes = [];
        foreach ($paymentTypeRows as $row) {
            $paymentTypes[] = [
                'type'   => $row->payment_type,
                'count'  => (int) $row->count,
                'amount' => (int) ($row->amount ?? 0),
            ];
        }

        // ── Évolution mensuelle (année en cours) ────────────────────────────
        $year = date('Y');
        $monthlyRows = (clone $base)
            ->whereYear('feescollections.created_at', $year)
            ->selectRaw('MONTH(feescollections.created_at) as month, COUNT(*) as count, SUM(paid_amount) as paid')
            ->groupBy(DB::raw('MONTH(feescollections.created_at)'))
            ->get()
            ->keyBy('month');

        $monthlyCount  = [];
        $monthlyPaid   = [];
        $monthlyByType = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthlyCount[] = (int) ($monthlyRows->get($m)?->count ?? 0);
            $monthlyPaid[]  = (int) ($monthlyRows->get($m)?->paid  ?? 0);
        }

        // Évolution mensuelle par mode de paiement (top 4 modes)
        $topTypes = array_slice(array_column($paymentTypes, 'type'), 0, 4);
        foreach ($topTypes as $type) {
            $rows = DB::table('feescollections')
                ->where('feescollections.is_delete', 0)
                ->where('feescollections.payment_type', $type)
                ->whereYear('feescollections.created_at', $year)
                ->selectRaw('MONTH(feescollections.created_at) as month, COUNT(*) as count')
                ->groupBy(DB::raw('MONTH(feescollections.created_at)'))
                ->get()
                ->keyBy('month');

            $series = [];
            for ($m = 1; $m <= 12; $m++) {
                $series[] = (int) ($rows->get($m)?->count ?? 0);
            }
            $monthlyByType[] = ['name' => $type, 'data' => $series];
        }

        // Taux de collecte réel (sur les montants)
        $collectionRate = $totalAmount > 0
            ? round(($paidAmount / $totalAmount) * 100, 1)
            : 0;

        return [
            // Montants globaux
            'totalAmount'     => $totalAmount,
            'paidAmount'      => $paidAmount,
            'remainingAmount' => $remainingAmount,
            // Dossiers par statut
            'countPaid'       => $countPaid,
            'countPending'    => $countPending,
            'countUnpaid'     => $countUnpaid,
            // Taux de collecte réel
            'collectionRate'  => $collectionRate,
            // Répartition par mode de paiement
            'paymentTypes'    => $paymentTypes,
            // Évolution mensuelle
            'monthlyCount'    => $monthlyCount,
            'monthlyPaid'     => $monthlyPaid,
            'monthlyByType'   => $monthlyByType,
        ];
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
