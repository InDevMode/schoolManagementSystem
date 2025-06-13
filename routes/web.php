<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClassSubjectController;
use App\Http\Controllers\ClassTeacherController;
use App\Http\Controllers\ClassTimetableController;
use App\Http\Controllers\CommunicateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'login']);
Route::post('login', [AuthController::class, 'authenticate']);
Route::get('logout', [AuthController::class, 'logout']);
Route::get('forgot_password', [AuthController::class, 'forgotPassword']);
Route::post('forgot_password', [AuthController::class, 'changePassword']);
Route::get('reset/{token}', [AuthController::class, 'resetPassword']);
Route::post('reset/{token}', [AuthController::class, 'resetAndChangePassword']);
Route::get('signup', [AuthController::class, 'signup']);

Route::group(['middleware' => 'admin'], function () {

    //Admin url
    Route::get('admin/dashboard', [DashboardController::class, 'dashboard']);
    Route::get('admin/admin/list', [AdminController::class, 'list']);
    Route::get('admin/admin/add', [AdminController::class, 'add']);
    Route::post('admin/admin/add', [AdminController::class, 'create']);
    Route::get('admin/admin/edit/{id}', [AdminController::class, 'edit']);
    Route::post('admin/admin/edit/{id}', [AdminController::class, 'update']);
    Route::get('admin/admin/delete/{id}', [AdminController::class, 'delete']);

    //Class url
    Route::get('admin/class/list', [ClassController::class, 'list']);
    Route::get('admin/class/add', [ClassController::class, 'add']);
    Route::post('admin/class/add', [ClassController::class, 'create']);
    Route::get('admin/class/edit/{id}', [ClassController::class, 'edit']);
    Route::post('admin/class/edit/{id}', [ClassController::class, 'update']);
    Route::get('admin/class/delete/{id}', [ClassController::class, 'delete']);

    //Subject url
    Route::get('admin/subject/list', [SubjectController::class, 'list']);
    Route::get('admin/subject/add', [SubjectController::class, 'add']);
    Route::post('admin/subject/add', [SubjectController::class, 'create']);
    Route::get('admin/subject/edit/{id}', [SubjectController::class, 'edit']);
    Route::post('admin/subject/edit/{id}', [SubjectController::class, 'update']);
    Route::get('admin/subject/delete/{id}', [SubjectController::class, 'delete']);

    //Assign Class Subject url
    Route::get('admin/assign_subject/list', [ClassSubjectController::class, 'list']);
    Route::get('admin/assign_subject/add', [ClassSubjectController::class, 'add']);
    Route::post('admin/assign_subject/add', [ClassSubjectController::class, 'create']);
    Route::get('admin/assign_subject/edit/{id}', [ClassSubjectController::class, 'edit']);
    Route::post('admin/assign_subject/edit/{id}', [ClassSubjectController::class, 'update']);
    Route::get('admin/assign_subject/edit_single/{id}', [ClassSubjectController::class, 'editSingle']);
    Route::post('admin/assign_subject/edit_single/{id}', [ClassSubjectController::class, 'updateSingle']);
    Route::get('admin/assign_subject/delete/{id}', [ClassSubjectController::class, 'delete']);

    // Admin Change Password url
    Route::get('admin/change_password', [UserController::class, 'changePassword']);
    Route::post('admin/change_password', [UserController::class, 'updatePassword']);

    // Student url on Admin
    Route::get('admin/student/list', [StudentController::class, 'list']);
    Route::get('admin/student/add', [StudentController::class, 'add']);
    Route::post('admin/student/add', [StudentController::class, 'create']);
    Route::get('admin/student/edit/{id}', [StudentController::class, 'edit']);
    Route::post('admin/student/edit/{id}', [StudentController::class, 'update']);
    Route::get('admin/student/delete/{id}', [StudentController::class, 'delete']);

    // Teacher url on Admin
    Route::get('admin/teacher/list', [TeacherController::class, 'list']);
    Route::get('admin/teacher/add', [TeacherController::class, 'add']);
    Route::post('admin/teacher/add', [TeacherController::class, 'create']);
    Route::get('admin/teacher/edit/{id}', [TeacherController::class, 'edit']);
    Route::post('admin/teacher/edit/{id}', [TeacherController::class, 'update']);
    Route::get('admin/teacher/delete/{id}', [TeacherController::class, 'delete']);

    // Parent url on Admin
    Route::get('admin/parent/list', [ParentController::class, 'list']);
    Route::get('admin/parent/add', [ParentController::class, 'add']);
    Route::post('admin/parent/add', [ParentController::class, 'create']);
    Route::get('admin/parent/edit/{id}', [ParentController::class, 'edit']);
    Route::post('admin/parent/edit/{id}', [ParentController::class, 'update']);
    Route::get('admin/parent/student/{id}', [ParentController::class, 'student']);
    Route::get('admin/parent/{parent_id}/assign_student_parent/{student_id}', [ParentController::class, 'assignStudentParent']);
    Route::get('admin/parent/des_assign_student_parent/{student_id}', [ParentController::class, 'desAssignStudentParent']);
    Route::get('admin/parent/delete/{id}', [ParentController::class, 'delete']);

    // Admin account url
    Route::get('admin/account', [UserController::class, 'myAccount']);
    Route::post('admin/account', [UserController::class, 'updateAdminAccount']);

    // Assign class to teacher url
    Route::get('admin/assign_class/list', [ClassTeacherController::class, 'list']);
    Route::get('admin/assign_class/add', [ClassTeacherController::class, 'add']);
    Route::post('admin/assign_class/add', [ClassTeacherController::class, 'create']);
    Route::get('admin/assign_class/edit/{id}', [ClassTeacherController::class, 'edit']);
    Route::post('admin/assign_class/edit/{id}', [ClassTeacherController::class, 'update']);
    Route::get('admin/assign_class/edit_single/{id}', [ClassTeacherController::class, 'editSingle']);
    Route::post('admin/assign_class/edit_single/{id}', [ClassTeacherController::class, 'updateSingle']);
    Route::get('admin/assign_class/delete/{id}', [ClassTeacherController::class, 'delete']);

    // Class timetable url
    Route::get('admin/class_timetable/list', [ClassTimetableController::class, 'list']);
    Route::post('admin/class_timetable/subject', [ClassTimetableController::class, 'getSubject']);
    Route::post('admin/class_timetable/add', [ClassTimetableController::class, 'add']);

    // Examinations url
    Route::get('admin/examinations/exam/list', [ExaminationController::class, 'list']);
    Route::get('admin/examinations/exam/add', [ExaminationController::class, 'add']);
    Route::post('admin/examinations/exam/add', [ExaminationController::class, 'create']);
    Route::get('admin/examinations/exam/edit/{id}', [ExaminationController::class, 'edit']);
    Route::post('admin/examinations/exam/edit/{id}', [ExaminationController::class, 'update']);
    Route::get('admin/examinations/exam/delete/{id}', [ExaminationController::class, 'delete']);

    // Schedule url
    Route::get('admin/examinations/schedule/list', [ExaminationController::class, 'scheduleList']);
    Route::post('admin/examinations/schedule/add', [ExaminationController::class, 'scheduleCreate']);

    // Exams register marks url
    Route::get('admin/examinations/marks_register/list', [ExaminationController::class, 'marksRegister']);
    Route::post('admin/examinations/marks_register/add', [ExaminationController::class, 'addMarksRegister']);
    Route::post('admin/examinations/marks_register/addSingleSubject', [ExaminationController::class, 'addSingleMarksRegister']);

    // Marks grade url
    Route::get('admin/examinations/marks_grade/list', [ExaminationController::class, 'listMarksGrade']);
    Route::get('admin/examinations/marks_grade/add', [ExaminationController::class, 'addMarksGrade']);
    Route::post('admin/examinations/marks_grade/add', [ExaminationController::class, 'createMarksGrade']);
    Route::get('admin/examinations/marks_grade/edit/{id}', [ExaminationController::class, 'editMarksGrade']);
    Route::post('admin/examinations/marks_grade/edit/{id}', [ExaminationController::class, 'updateMarksGrade']);
    Route::get('admin/examinations/marks_grade/delete/{id}', [ExaminationController::class, 'deleteMarksGrade']);

    // Attendance student url
    Route::get('admin/attendance/student/list', [AttendanceController::class, 'attendanceStudent']);
    Route::post('admin/attendance/student/save', [AttendanceController::class, 'attendanceStudentSave']);

    // Attendance report admin url
    Route::get('admin/attendance/report', [AttendanceController::class, 'attendanceReport']);

    // Communicate url
    Route::get('admin/communicate/noticeboard/list', [CommunicateController::class, 'list']);
    Route::get('admin/communicate/noticeboard/add', [CommunicateController::class, 'add']);
    Route::post('admin/communicate/noticeboard/add', [CommunicateController::class, 'create']);
    Route::get('admin/communicate/noticeboard/edit/{id}', [CommunicateController::class, 'edit']);
    Route::post('admin/communicate/noticeboard/edit/{id}', [CommunicateController::class, 'update']);
    Route::post('admin/communicate/noticeboard/delete/{id}', [CommunicateController::class, 'delete']);

});

Route::group(['middleware' => 'teacher'], function () {
    Route::get('teacher/dashboard', [DashboardController::class, 'dashboard']);

    // Teacher Change Password url
    Route::get('teacher/change_password', [UserController::class, 'changePassword']);
    Route::post('teacher/change_password', [UserController::class, 'updatePassword']);

    // Teacher account url
    Route::get('teacher/account', [UserController::class, 'myAccount']);
    Route::post('teacher/account', [UserController::class, 'updateTeacherAccount']);

    // Teacher class subject url
    Route::get('teacher/class_subject', [ClassTeacherController::class, 'myClassSubject']);
    Route::get('teacher/class_subject/{class_id}/timetable/{subject_id}/', [ClassTimetableController::class, 'myClassSubjectTimetable']);

    // Teacher student url
    Route::get('teacher/my_student', [StudentController::class, 'myStudent']);

    // Teacher side exam timetable
    Route::get('teacher/my_exam_timetable', [ExaminationController::class, 'myExamTimetableTeacher']);

    // Student calendar url
    Route::get('teacher/my_calendar', [CalendarController::class, 'myTeacherCalendar']);

    // Teacher Marks register url
    Route::get('teacher/marks_register', [ExaminationController::class, 'teacherMarkRegister']);
    Route::post('teacher/add_marks_register', [ExaminationController::class, 'addTeacherMarkRegister']);
    Route::post('teacher/add_single_marks_register', [ExaminationController::class, 'addSingleTeacherMarkRegister']);

    // Student attendance url
    Route::get('teacher/attendance/student/list', [AttendanceController::class, 'attendanceStudentTeacher']);
    Route::post('teacher/attendance/student/save', [AttendanceController::class, 'attendanceStudentTeacherSave']);

      // Attendance report teacher url
    Route::get('teacher/attendance/report', [AttendanceController::class, 'attendanceReportTeacher']);

});

Route::group(['middleware' => 'student'], function () {
    Route::get('student/dashboard', [DashboardController::class, 'dashboard']);

    // Student Change Password url
    Route::get('student/change_password', [UserController::class, 'changePassword']);
    Route::post('student/change_password', [UserController::class, 'updatePassword']);

    // Student calendar url
    Route::get('student/my_calendar', [CalendarController::class, 'myCalendar']);

    // Student account url
    Route::get('student/account', [UserController::class, 'myAccount']);
    Route::post('student/account', [UserController::class, 'updateStudentAccount']);

    // Student route side show subject
    Route::get('student/my_subject', [SubjectController::class, 'studentSubject']);
    Route::get('student/my_timetable', [ClassTimetableController::class, 'studentTimetable']);

    // Student side exam timetable
    Route::get('student/my_exam_timetable', [ExaminationController::class, 'myExamTimetableStudent']);
    Route::get('student/my_exam_result', [ExaminationController::class, 'myExamResultStudent']);

    // Student attendance url
    Route::get('student/my_attendance', [AttendanceController::class, 'myAttendance']);
});

Route::group(['middleware' => 'parent'], function () {
    Route::get('parent/dashboard', [DashboardController::class, 'dashboard']);

    // Parent Change Password url
    Route::get('parent/change_password', [UserController::class, 'changePassword']);
    Route::post('parent/change_password', [UserController::class, 'updatePassword']);

    // Parent account url
    Route::get('parent/account', [UserController::class, 'myAccount']);
    Route::post('parent/account', [UserController::class, 'updateParentAccount']);

    // Parent route side show student
    Route::get('parent/my_student', [ParentController::class, 'parentStudent']);
    Route::get('parent/my_student/{student_id}/subject', [SubjectController::class, 'parentStudentSubject']);
    Route::get('parent/my_student/exam_timetable/{student_id}/subject', [ExaminationController::class, 'parentStudentExamTimetable']);
    // Parent calendar url
    Route::get('parent/my_student/calendar/{student_id}/subject', [CalendarController::class, 'parentStudentExamCalendar']);

    // Parent student class timetable
    Route::get('parent/my_student/{class_id}/subject/{subject_id}/timetable/student/{student_id}', [ClassTimetableController::class, 'parentStudentSubjectTimetable']);

    // Parent student exam result url
    Route::get('parent/my_student/exam_result/{student_id}/result', [ExaminationController::class, 'parentStudentExamResult']);

    // Parent student attendance url
    Route::get('parent/my_student/attendance/{student_id}', [AttendanceController::class, 'parentStudentAttendance']);
});
