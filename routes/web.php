<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClassSubjectController;
use App\Http\Controllers\ClassTeacherController;
use App\Http\Controllers\ClassTimetableController;
use App\Http\Controllers\CommunicateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeesCollectionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\RbacController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DeletionLogController;
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
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'authenticate']);
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('forgot_password', [AuthController::class, 'forgotPassword'])->name('password.request');
Route::post('forgot_password', [AuthController::class, 'changePassword'])->name('password.email');
Route::get('reset/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::post('reset/{token}', [AuthController::class, 'resetAndChangePassword'])->name('password.update');

// Changement de mot de passe forcé (après réinitialisation admin)
Route::get('force-change-password',  [AuthController::class, 'forceChangePasswordForm'])->name('force-change-password')->middleware('auth');
Route::post('force-change-password', [AuthController::class, 'forceChangePasswordUpdate'])->name('force-change-password.update')->middleware('auth');

// OAuth Social Login (Google & Facebook)
Route::get('auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');


Route::group(['middleware' => 'common'], function () {
    Route::get('chat', [ChatController::class, 'chat']);

    // ── Notifications in-app ──────────────────────────────────────────────────
    Route::get('notifications/poll',         [NotificationController::class, 'index'])->name('notifications.poll');
    Route::post('notifications/{id}/read',   [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/read-all',    [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::delete('notifications/{id}',      [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('notifications-clear-read',[NotificationController::class, 'clearRead'])->name('notifications.clear-read');
    Route::post('chat', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::put('/chat/{id}', [ChatController::class, 'updateMessage'])->name('chat.update');

    // ── API JSON pour le polling temps réel — AVANT les routes wildcard ──────
    Route::get('chat/messages/poll',       [ChatController::class, 'pollMessages'])->name('chat.poll');
    Route::get('chat/contacts/poll',       [ChatController::class, 'pollContacts'])->name('chat.contacts.poll');
    Route::post('chat/send-ajax',          [ChatController::class, 'sendMessageAjax'])->name('chat.send.ajax');
    Route::post('chat/update-ajax/{id}',   [ChatController::class, 'updateMessageAjax'])->name('chat.update.ajax');
    Route::post('chat/delete-ajax/{id}',   [ChatController::class, 'deleteMessageAjax'])->name('chat.delete.ajax');
    Route::post('chat/typing',             [ChatController::class, 'setTyping'])->name('chat.typing');
    Route::get('chat/typing/check',        [ChatController::class, 'checkTyping'])->name('chat.typing.check');

    // Wildcard en DERNIER
    Route::get('/chat/{id}', [ChatController::class, 'deleteMessage'])->name('chat.delete');
});

// ══════════════════════════════════════════════════════════════════════════════
// SUPER ADMIN — Configuration RBAC (user_type = 0)
// ══════════════════════════════════════════════════════════════════════════════
Route::group(['middleware' => 'super_admin', 'prefix' => 'superadmin'], function () {

    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('superadmin.dashboard');

    // ── Rôles ──────────────────────────────────────────────────────────────
    Route::get('config/roles',              [RbacController::class, 'roleList'])->name('superadmin.roles');
    Route::post('config/roles/add',         [RbacController::class, 'roleCreate'])->name('superadmin.roles.create');
    Route::post('config/roles/edit/{id}',   [RbacController::class, 'roleUpdate'])->name('superadmin.roles.update');
    Route::get('config/roles/delete/{id}',  [RbacController::class, 'roleDelete'])->name('superadmin.roles.delete');
    Route::get('config/roles/restore/{id}', [RbacController::class, 'roleRestore'])->name('superadmin.roles.restore');

    // ── Permissions ────────────────────────────────────────────────────────
    Route::get('config/permissions',               [RbacController::class, 'permissionList'])->name('superadmin.permissions');
    Route::post('config/permissions/add',          [RbacController::class, 'permissionCreate'])->name('superadmin.permissions.create');
    Route::post('config/permissions/edit/{id}',    [RbacController::class, 'permissionUpdate'])->name('superadmin.permissions.update');
    Route::get('config/permissions/delete/{id}',   [RbacController::class, 'permissionDelete'])->name('superadmin.permissions.delete');
    Route::get('config/permissions/restore/{id}',  [RbacController::class, 'permissionRestore'])->name('superadmin.permissions.restore');

    // ── Attribution permissions ────────────────────────────────────────────
    Route::get('config/assign',                        [RbacController::class, 'assignList'])->name('superadmin.assign');
    Route::post('config/assign/role/{roleId}/sync',    [RbacController::class, 'assignSync'])->name('superadmin.assign.sync');
    Route::post('config/assign/user/{userId}/sync',    [RbacController::class, 'assignUserSync'])->name('superadmin.assign.user.sync');

    // ── Paramètres système ─────────────────────────────────────────────────
    Route::get('config/settings',                [UserController::class, 'settings'])->name('superadmin.settings');
    Route::post('config/settings/save',          [UserController::class, 'updateSettingInfo'])->name('superadmin.settings.save');

    // ── Compte super admin ─────────────────────────────────────────────────
    Route::get('account',         [UserController::class, 'myAccount'])->name('superadmin.account');
    Route::post('account',        [UserController::class, 'updateSuperAdminAccount'])->name('superadmin.account.update');
    Route::get('change_password', [UserController::class, 'changePassword'])->name('superadmin.change_password');
    Route::post('change_password',[UserController::class, 'updatePassword'])->name('superadmin.change_password.update');

    // ── Édition inline cellule (super admin only) ──────────────────────────
    Route::post('users/inline-edit', [UserController::class, 'inlineCellEdit'])->name('superadmin.inline-edit');

    // ── Journaux de suppression (super admin only) ─────────────────────────
    Route::get('deletion-logs',       [DeletionLogController::class, 'list'])->name('superadmin.deletion-logs');
    Route::get('deletion-logs/{id}',  [DeletionLogController::class, 'show'])->name('superadmin.deletion-logs.show');

    // ── Liste de tous les utilisateurs (super admin only) ──────────────────
    Route::get('users',                           [UserController::class, 'allUsersList'])->name('superadmin.users');
    Route::post('users/reset-password',           [UserController::class, 'resetUsersPassword'])->name('superadmin.users.reset-password');
    Route::post('users/update/{id}',              [UserController::class, 'updateUserFromPanel'])->name('superadmin.users.update');
    Route::post('users/delete/{id}',              [UserController::class, 'deleteUser'])->name('superadmin.users.delete');
    Route::post('users/export',                   [UserController::class, 'exportAllUsers'])->name('superadmin.users.export');
});

Route::group(['middleware' => 'admin'], function () {

    // Dashboard (admin + rôles custom >= 5)
    Route::get('admin/dashboard',  [DashboardController::class, 'dashboard']);
    Route::get('dashboard',        [DashboardController::class, 'dashboard'])->name('dashboard.default');

    // Admin url
    Route::get('admin/admin/list',         [AdminController::class, 'list'])->middleware('check_perm:view.users.admins');
    Route::post('admin/admin/add',         [AdminController::class, 'create'])->middleware('check_perm:action.admins.create');
    Route::post('admin/admin/edit/{id}',   [AdminController::class, 'update'])->middleware('check_perm:action.admins.edit');
    Route::get('admin/admin/delete/{id}',  [AdminController::class, 'delete'])->middleware('check_perm:action.admins.delete');
    Route::post('admin/admin/export',      [AdminController::class, 'exportAdmin'])->middleware('check_perm:action.admins.export');

    // Class url
    Route::get('admin/class/list',         [ClassController::class, 'list'])->middleware('check_perm:view.academics.classes');
    Route::post('admin/class/add',         [ClassController::class, 'create'])->middleware('check_perm:action.classes.create');
    Route::post('admin/class/edit/{id}',   [ClassController::class, 'update'])->middleware('check_perm:action.classes.edit');
    Route::get('admin/class/delete/{id}',  [ClassController::class, 'delete'])->middleware('check_perm:action.classes.delete');

    // Subject url
    Route::get('admin/subject/list',         [SubjectController::class, 'list'])->middleware('check_perm:view.academics.subjects');
    Route::post('admin/subject/add',         [SubjectController::class, 'create'])->middleware('check_perm:action.subjects.create');
    Route::post('admin/subject/edit/{id}',   [SubjectController::class, 'update'])->middleware('check_perm:action.subjects.edit');
    Route::get('admin/subject/delete/{id}',  [SubjectController::class, 'delete'])->middleware('check_perm:action.subjects.delete');

    // Assign Class Subject url
    Route::get('admin/assign_subject/list',              [ClassSubjectController::class, 'list'])->middleware('check_perm:view.academics.assign_subjects');
    Route::post('admin/assign_subject/add',              [ClassSubjectController::class, 'create'])->middleware('check_perm:view.academics.assign_subjects');
    Route::post('admin/assign_subject/edit/{id}',        [ClassSubjectController::class, 'update'])->middleware('check_perm:view.academics.assign_subjects');
    Route::post('admin/assign_subject/edit_single/{id}', [ClassSubjectController::class, 'updateSingle'])->middleware('check_perm:view.academics.assign_subjects');
    Route::get('admin/assign_subject/delete/{id}',       [ClassSubjectController::class, 'delete'])->middleware('check_perm:view.academics.assign_subjects');

    // Student url
    Route::get('admin/student/list',         [StudentController::class, 'list'])->middleware('check_perm:view.users.students');
    Route::post('admin/student/add',         [StudentController::class, 'create'])->middleware('check_perm:action.students.create');
    Route::post('admin/student/edit/{id}',   [StudentController::class, 'update'])->middleware('check_perm:action.students.edit');
    Route::get('admin/student/delete/{id}',  [StudentController::class, 'delete'])->middleware('check_perm:action.students.delete');
    Route::post('admin/student/export',      [StudentController::class, 'exportStudent'])->middleware('check_perm:action.students.export');

    // Teacher url
    Route::get('admin/teacher/list',         [TeacherController::class, 'list'])->middleware('check_perm:view.users.teachers');
    Route::post('admin/teacher/add',         [TeacherController::class, 'create'])->middleware('check_perm:action.teachers.create');
    Route::post('admin/teacher/edit/{id}',   [TeacherController::class, 'update'])->middleware('check_perm:action.teachers.edit');
    Route::get('admin/teacher/delete/{id}',  [TeacherController::class, 'delete'])->middleware('check_perm:action.teachers.delete');
    Route::post('admin/teacher/export',      [TeacherController::class, 'exportTeacher'])->middleware('check_perm:action.teachers.export');

    // Parent url
    Route::get('admin/parent/list',          [ParentController::class, 'list'])->middleware('check_perm:view.users.parents');
    Route::post('admin/parent/add',          [ParentController::class, 'create'])->middleware('check_perm:action.parents.create');
    Route::post('admin/parent/edit/{id}',    [ParentController::class, 'update'])->middleware('check_perm:action.parents.edit');
    Route::get('admin/parent/student/{id}',  [ParentController::class, 'student'])->middleware('check_perm:view.users.parents');
    Route::get('admin/parent/{parent_id}/assign_student_parent/{student_id}',  [ParentController::class, 'assignStudentParent'])->middleware('check_perm:view.users.parents');
    Route::get('admin/parent/des_assign_student_parent/{student_id}',          [ParentController::class, 'desAssignStudentParent'])->middleware('check_perm:view.users.parents');
    Route::get('admin/parent/delete/{id}',   [ParentController::class, 'delete'])->middleware('check_perm:action.parents.delete');
    Route::post('admin/parent/export',       [ParentController::class, 'exportParent'])->middleware('check_perm:action.parents.export');

    // Admin account url
    Route::get('admin/account',  [UserController::class, 'myAccount']);
    Route::post('admin/account', [UserController::class, 'updateAdminAccount']);
    Route::post('admin/users/reset-password', [UserController::class, 'resetUsersPassword']);

    // Change password
    Route::get('admin/change_password',  [UserController::class, 'changePassword']);
    Route::post('admin/change_password', [UserController::class, 'updatePassword']);

    // Assign class to teacher url
    Route::get('admin/assign_class/list',              [ClassTeacherController::class, 'list'])->middleware('check_perm:view.academics.assign_classes');
    Route::post('admin/assign_class/add',              [ClassTeacherController::class, 'create'])->middleware('check_perm:view.academics.assign_classes');
    Route::post('admin/assign_class/edit/{id}',        [ClassTeacherController::class, 'update'])->middleware('check_perm:view.academics.assign_classes');
    Route::post('admin/assign_class/edit_single/{id}', [ClassTeacherController::class, 'updateSingle'])->middleware('check_perm:view.academics.assign_classes');
    Route::get('admin/assign_class/delete/{id}',       [ClassTeacherController::class, 'delete'])->middleware('check_perm:view.academics.assign_classes');

    // Class timetable url
    Route::get('admin/class_timetable/list',    [ClassTimetableController::class, 'list'])->middleware('check_perm:view.academics.timetable');
    Route::post('admin/class_timetable/subject',[ClassTimetableController::class, 'getSubject'])->middleware('check_perm:view.academics.timetable');
    Route::post('admin/class_timetable/add',    [ClassTimetableController::class, 'add'])->middleware('check_perm:view.academics.timetable');

    // Calendar admin url
    Route::get('admin/calendar', [CalendarController::class, 'adminCalendar']);

    // Period url
    Route::get('admin/examinations/period/list',              [PeriodController::class, 'list'])->middleware('check_perm:view.exams.periods');
    Route::post('admin/examinations/period/add',              [PeriodController::class, 'create'])->middleware('check_perm:view.exams.periods');
    Route::post('admin/examinations/period/edit/{id}',        [PeriodController::class, 'update'])->middleware('check_perm:view.exams.periods');
    Route::get('admin/examinations/period/delete/{id}',       [PeriodController::class, 'delete'])->middleware('check_perm:view.exams.periods');
    Route::post('admin/examinations/period/set-current/{id}', [PeriodController::class, 'setCurrent'])->middleware('check_perm:view.exams.periods');

    // Attendance url
    Route::get('admin/attendance/students/list',        [AttendanceController::class, 'attendanceStudent'])->middleware('check_perm:view.attendance.manage');
    Route::post('admin/attendance/student/save',        [AttendanceController::class, 'attendanceStudentSave'])->middleware('check_perm:action.attendance.save');
    Route::get('admin/attendance/report',               [AttendanceController::class, 'attendanceReport'])->middleware('check_perm:view.attendance.report');
    Route::post('admin/attendance/report/export',       [AttendanceController::class, 'attendanceReportExport'])->middleware('check_perm:view.attendance.report');
    Route::delete('admin/attendance/delete/{id}',       [AttendanceController::class, 'attendanceDelete'])->middleware('check_perm:action.attendance.save');

    // Communicate url
    Route::get('admin/communicate/noticeboard/list',           [CommunicateController::class, 'list'])->middleware('check_perm:view.communicate.noticeboard');
    Route::post('admin/communicate/noticeboard/add',           [CommunicateController::class, 'create'])->middleware('check_perm:action.noticeboard.manage');
    Route::get('admin/communicate/noticeboard/edit/{id}',      [CommunicateController::class, 'edit'])->middleware('check_perm:action.noticeboard.manage');
    Route::post('admin/communicate/noticeboard/edit/{id}',     [CommunicateController::class, 'update'])->middleware('check_perm:action.noticeboard.manage');
    Route::post('admin/communicate/noticeboard/delete/{id}',   [CommunicateController::class, 'delete'])->middleware('check_perm:action.noticeboard.manage');
    Route::post('admin/communicate/noticeboard/toggle/{id}',   [CommunicateController::class, 'toggle'])->middleware('check_perm:action.noticeboard.manage');
    Route::get('admin/communicate/noticeboard/history',        [CommunicateController::class, 'history'])->middleware('check_perm:action.noticeboard.manage');
    Route::post('admin/communicate/noticeboard/restore/{id}',  [CommunicateController::class, 'restore'])->middleware('check_perm:action.noticeboard.manage');
    Route::get('admin/communicate/send_mail',                  [CommunicateController::class, 'sendMail'])->middleware('check_perm:view.communicate.mail');
    Route::post('admin/communicate/send_mail',                 [CommunicateController::class, 'sendMailCreate'])->middleware('check_perm:action.mail.send');

    // Practical works url
    Route::get('admin/practicalworks/homework/list',                      [WorkController::class, 'practicalWorksList'])->middleware('check_perm:view.homework.list');
    Route::get('admin/practicalworks/homework/details/{id}',              [WorkController::class, 'practicalWorksDetails'])->middleware('check_perm:view.homework.list');
    Route::get('admin/practicalworks/homework/details-json/{id}',         [WorkController::class, 'practicalWorksDetailsJson'])->middleware('check_perm:view.homework.list');
    Route::get('admin/practicalworks/homework/edit-json/{id}',            [WorkController::class, 'practicalWorksEditJson'])->middleware('check_perm:action.homework.edit');
    Route::get('admin/practicalworks/homework/getSubjectByClassId/{id}',  [WorkController::class, 'getSubjectByClassId'])->middleware('check_perm:view.homework.list');
    Route::post('admin/practicalworks/homework/create',                   [WorkController::class, 'practicalWorksCreate'])->middleware('check_perm:action.homework.create');
    Route::post('admin/practicalworks/homework/edit/{id}',                [WorkController::class, 'practicalWorksUpdate'])->middleware('check_perm:action.homework.edit');
    Route::get('admin/practicalworks/homework/delete/{id}',               [WorkController::class, 'practicalWorksDelete'])->middleware('check_perm:action.homework.delete');
    Route::get('admin/practicalworks/homework/restore/{id}',              [WorkController::class, 'practicalWorksRestore'])->middleware('check_perm:action.homework.delete');
    Route::get('admin/practicalworks/homework/trash',                     [WorkController::class, 'practicalWorksTrashed'])->middleware('check_perm:view.homework.list');
    Route::get('admin/practicalworks/homework/submission/{id}',           [WorkController::class, 'homeworkSubmission'])->middleware('check_perm:view.homework.list');
    Route::get('admin/practicalworks/reports',                            [WorkController::class, 'homeworkReportList'])->middleware('check_perm:view.homework.reports');
    Route::get('admin/practicalworks/homework/reports/details/{id}',      [WorkController::class, 'homeworkReportDetails'])->middleware('check_perm:view.homework.reports');

    // Fees collections url
    Route::get('admin/feescollections/collections/list',                  [FeesCollectionController::class, 'list'])->middleware('check_perm:view.fees.collect');
    Route::get('admin/feescollections/feescollects/feesList',             [FeesCollectionController::class, 'feesList'])->middleware('check_perm:view.fees.reports');
    Route::post('admin/feescollections/collections/addFees/{id}',         [FeesCollectionController::class, 'createFees'])->name('createFeesCollects')->middleware('check_perm:action.fees.collect');
    Route::get('admin/feescollections/collections/delete/{id}',           [FeesCollectionController::class, 'deleteFees'])->middleware('check_perm:action.fees.delete');
    Route::post('admin/feescollections/feescollects/export',              [FeesCollectionController::class, 'exportFeesCollects'])->middleware('check_perm:view.fees.reports');

    // Settings url
    Route::get('admin/settings',                [UserController::class, 'settings'])->middleware('check_perm:view.settings');
    Route::post('admin/settings/setting_data',  [UserController::class, 'updateSettingInfo'])->middleware('check_perm:action.settings.manage');

    // Payment callbacks (pas de check_perm — callbacks externes)
    Route::get('admin/feescollections_paypal/payment_success',  [FeesCollectionController::class, 'paypalAdminSuccess']);
    Route::get('admin/feescollections_paypal/payment_error',    [FeesCollectionController::class, 'paypalAdminError']);
    Route::get('admin/feescollections_fedapay/payment_success', [FeesCollectionController::class, 'fedapayAdminSuccess']);
    Route::get('admin/feescollections_stripe/payment_success',  [FeesCollectionController::class, 'stripeAdminSuccess']);
    Route::get('admin/feescollections_stripe/payment_error',    [FeesCollectionController::class, 'stripeAdminError']);
    Route::post('/paypal/ipn/admin',                            [FeesCollectionController::class, 'paypalIPN']);

    Route::get('admin/test', [AdminController::class, 'test']);

    // ── Évaluations béninoises (nouveaux types) ──────────────────────────────
    Route::get('admin/evaluations/list',                           [EvaluationController::class, 'list'])->middleware('check_perm:view.exams.list');
    Route::post('admin/evaluations/add',                          [EvaluationController::class, 'create'])->middleware('check_perm:action.exams.create');
    Route::post('admin/evaluations/edit/{id}',                    [EvaluationController::class, 'update'])->middleware('check_perm:action.exams.edit');
    Route::get('admin/evaluations/delete/{id}',                   [EvaluationController::class, 'delete'])->middleware('check_perm:action.exams.delete');
    Route::post('admin/evaluations/status/{id}',                  [EvaluationController::class, 'changeStatus'])->middleware('check_perm:action.exams.edit');
    Route::get('admin/evaluations/grade-entry',                   [EvaluationController::class, 'gradeEntry'])->middleware('check_perm:view.exams.marks');
    Route::post('admin/evaluations/grades/save',                  [EvaluationController::class, 'saveGrades'])->middleware('check_perm:action.marks.manage');
    Route::post('admin/evaluations/grades/validate',              [EvaluationController::class, 'validateGrades'])->middleware('check_perm:action.marks.manage');
    Route::post('admin/evaluations/grades/reject',                [EvaluationController::class, 'rejectGrades'])->middleware('check_perm:action.marks.manage');
    Route::post('admin/evaluations/grades/cancel-validation',     [EvaluationController::class, 'cancelValidation'])->middleware('check_perm:action.marks.manage');
    Route::post('admin/evaluations/cancel',                        [EvaluationController::class, 'cancelEvaluation'])->middleware('check_perm:action.exams.edit');
    Route::get('admin/evaluations/grades/pending',                [EvaluationController::class, 'pendingValidation'])->middleware('check_perm:view.exams.marks');
    Route::get('admin/evaluations/subjects-by-class/{class_id}',  [EvaluationController::class, 'getSubjectsByClass'])->middleware('check_perm:view.exams.list');
    Route::get('admin/evaluations/by-class-period',               [EvaluationController::class, 'getEvaluationsByClassPeriod'])->middleware('check_perm:view.exams.list');

    // ── Bulletins ─────────────────────────────────────────────────────────────
    Route::get('admin/bulletins/list',                            [BulletinController::class, 'list'])->middleware('check_perm:view.bulletins.list');
    Route::post('admin/bulletins/generate',                       [BulletinController::class, 'generate'])->middleware('check_perm:action.bulletins.generate');
    Route::post('admin/bulletins/generate-class',                 [BulletinController::class, 'generateForClass'])->middleware('check_perm:action.bulletins.generate');
    Route::post('admin/bulletins/publish/{id}',                   [BulletinController::class, 'publish'])->middleware('check_perm:action.bulletins.publish');
    Route::post('admin/bulletins/publish-all',                    [BulletinController::class, 'publishAll'])->middleware('check_perm:action.bulletins.publish');
    Route::post('admin/bulletins/comment/{id}',                   [BulletinController::class, 'updateComment'])->middleware('check_perm:action.bulletins.generate');
    Route::get('admin/bulletins/delete/{id}',                     [BulletinController::class, 'delete'])->middleware('check_perm:action.bulletins.generate');
    Route::get('admin/bulletins/show/{id}',                       [BulletinController::class, 'show'])->middleware('check_perm:view.bulletins.list');
    Route::get('admin/bulletins/print/{id}',                      [BulletinController::class, 'print'])->middleware('check_perm:view.bulletins.list');
    Route::get('admin/bulletins/preview-averages',                [BulletinController::class, 'previewClassAverages'])->middleware('check_perm:view.bulletins.list');

    // ── Personnel (Staff) ─────────────────────────────────────────────────────
    Route::get('admin/staff/list',                                [StaffController::class, 'list'])->middleware('check_perm:view.staff.list');
    Route::post('admin/staff/add',                                [StaffController::class, 'create'])->middleware('check_perm:action.staff.create');
    Route::get('admin/staff/edit/{id}',                           [StaffController::class, 'edit'])->middleware('check_perm:action.staff.edit');
    Route::post('admin/staff/edit/{id}',                          [StaffController::class, 'update'])->middleware('check_perm:action.staff.edit');
    Route::get('admin/staff/delete/{id}',                         [StaffController::class, 'delete'])->middleware('check_perm:action.staff.delete');

    // ── Types de congés ───────────────────────────────────────────────────────
    Route::get('admin/staff/leave-types/list',                    [StaffController::class, 'leaveTypeList'])->middleware('check_perm:view.staff.leaves');
    Route::post('admin/staff/leave-types/add',                    [StaffController::class, 'leaveTypeCreate'])->middleware('check_perm:action.staff.leaves');
    Route::get('admin/staff/leave-types/edit/{id}',               [StaffController::class, 'leaveTypeEdit'])->middleware('check_perm:action.staff.leaves');
    Route::post('admin/staff/leave-types/edit/{id}',              [StaffController::class, 'leaveTypeUpdate'])->middleware('check_perm:action.staff.leaves');
    Route::get('admin/staff/leave-types/delete/{id}',             [StaffController::class, 'leaveTypeDelete'])->middleware('check_perm:action.staff.leaves');

    // ── Congés du personnel ───────────────────────────────────────────────────
    Route::get('admin/staff/leaves/list',                         [StaffController::class, 'leaveList'])->middleware('check_perm:view.staff.leaves');
    Route::post('admin/staff/leaves/add',                         [StaffController::class, 'leaveCreate'])->middleware('check_perm:action.staff.leaves');
    Route::post('admin/staff/leaves/approve/{id}',                [StaffController::class, 'leaveApprove'])->middleware('check_perm:action.staff.leaves');
    Route::get('admin/staff/leaves/delete/{id}',                  [StaffController::class, 'leaveDelete'])->middleware('check_perm:action.staff.leaves');

    // ── Événements ────────────────────────────────────────────────────────────
    Route::get('admin/staff/events/list',                         [StaffController::class, 'eventList'])->middleware('check_perm:view.staff.events');
    Route::post('admin/staff/events/add',                         [StaffController::class, 'eventCreate'])->middleware('check_perm:action.staff.events');
    Route::get('admin/staff/events/edit/{id}',                    [StaffController::class, 'eventEdit'])->middleware('check_perm:action.staff.events');
    Route::post('admin/staff/events/edit/{id}',                   [StaffController::class, 'eventUpdate'])->middleware('check_perm:action.staff.events');
    Route::get('admin/staff/events/delete/{id}',                  [StaffController::class, 'eventDelete'])->middleware('check_perm:action.staff.events');

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

    // Teacher calendar url
    Route::get('teacher/my_calendar', [CalendarController::class, 'myTeacherCalendar']);

    // ── Évaluations béninoises — prof ─────────────────────────────────────
    Route::get('teacher/evaluations',                  [EvaluationController::class, 'teacherList']);
    Route::post('teacher/evaluations/add',             [EvaluationController::class, 'teacherCreate']);
    Route::get('teacher/evaluations/grade-entry',      [EvaluationController::class, 'teacherGradeEntry']);
    Route::post('teacher/evaluations/grades/save',     [EvaluationController::class, 'teacherSaveGrades']);

    // Student attendance url
    Route::get('teacher/attendance/student/list', [AttendanceController::class, 'attendanceStudentTeacher']);
    Route::post('teacher/attendance/student/save', [AttendanceController::class, 'attendanceStudentTeacherSave']);

    // Attendance report teacher url
    Route::get('teacher/attendance/report', [AttendanceController::class, 'attendanceReportTeacher']);

    // Teacher noticeboard url
    Route::get('teacher/my_noticeboard', [CommunicateController::class, 'teacherNoticeBoard']);

    // Practical works url
    Route::get('teacher/practicalworks/homework/list', [WorkController::class, 'teacherPracticalWorksList']);
    Route::get('teacher/practicalworks/homework/add', [WorkController::class, 'teacherPracticalWorksAdd']);
    Route::get('teacher/practicalworks/homework/getSubjectByClassId/{id}', [WorkController::class, 'getSubjectByClassId']);
    Route::post('teacher/practicalworks/homework/add', [WorkController::class, 'teacherPracticalWorksCreate']);
    Route::get('teacher/practicalworks/homework/edit/{id}', [WorkController::class, 'teacherPracticalWorksEdit']);
    Route::get('teacher/practicalworks/homework/edit-json/{id}', [WorkController::class, 'teacherPracticalWorksEditJson']);
    Route::post('teacher/practicalworks/homework/edit/{id}', [WorkController::class, 'teacherPracticalWorksUpdate']);
    Route::get('teacher/practicalworks/homework/delete/{id}', [WorkController::class, 'teacherPracticalWorksDelete']);
    Route::get('teacher/practicalworks/homework/restore/{id}', [WorkController::class, 'teacherPracticalWorksRestore']);
    Route::get('teacher/practicalworks/homework/trash', [WorkController::class, 'teacherPracticalWorksTrashed']);

    // Practical works submitted url
    Route::get('teacher/practicalworks/homework/submission/{id}', [WorkController::class, 'teacherHomeworkSubmission']);


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

    // ── Notes et bulletins — élève ────────────────────────────────────────
    Route::get('student/my_grades',                          [EvaluationController::class, 'studentGrades']);
    Route::get('student/my_bulletins',                       [BulletinController::class, 'studentBulletins']);
    Route::get('student/my_bulletins/{id}',                  [BulletinController::class, 'studentBulletinShow']);
    Route::get('student/my_bulletins/{id}/print',            [BulletinController::class, 'studentBulletinPrint']);

    // Student attendance url
    Route::get('student/my_attendance', [AttendanceController::class, 'myAttendance']);

    //  Student notice board url
    Route::get('student/my_noticeboard', [CommunicateController::class, 'myNoticeBoard']);

    // Student Homework
    Route::get('student/my_homework', [WorkController::class, 'myHomework']);
    Route::get('student/my_homework/submission/{id}', [WorkController::class, 'myHomeworkSubmission']);
    Route::post('student/my_homework/submission/{id}', [WorkController::class, 'myHomeworkSubmissionCreate']);

    // Student Fees collections
    Route::get('student/my_fees', [FeesCollectionController::class, 'myFees']);
    Route::post('student/my_fees', [FeesCollectionController::class, 'myFeesCreate'])->name('studentFeesCreate');

    // Paypal payment url of student
    Route::get('student/my_fees_paypal/payment_success', [FeesCollectionController::class, 'paypalStudentSuccess']);
    Route::get('student/my_fees_paypal/payment_error', [FeesCollectionController::class, 'paypalStudentError']);

    // Stripe payment url of student
    Route::get('student/my_fees_stripe/payment_success', [FeesCollectionController::class, 'stripeStudentSuccess']);
    Route::get('student/my_fees_stripe/payment_error', [FeesCollectionController::class, 'stripeStudentError']);

    Route::post('/paypal/ipn/student', [FeesCollectionController::class, 'paypalIPN']);

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
    // Parent calendar url
    Route::get('parent/my_student/calendar/{student_id}/subject', [CalendarController::class, 'parentStudentExamCalendar']);

    // Parent student class timetable
    Route::get('parent/my_student/{class_id}/subject/{subject_id}/timetable/student/{student_id}', [ClassTimetableController::class, 'parentStudentSubjectTimetable']);

    // ── Notes et bulletins — parent ───────────────────────────────────────
    Route::get('parent/my_student/{student_id}/grades',              [EvaluationController::class, 'parentStudentGrades']);
    Route::get('parent/my_student/{student_id}/bulletins',           [BulletinController::class, 'parentStudentBulletins']);
    Route::get('parent/my_student/{student_id}/bulletins/{id}',      [BulletinController::class, 'parentStudentBulletinShow']);
    Route::get('parent/my_student/{student_id}/bulletins/{id}/print',[BulletinController::class, 'parentStudentBulletinPrint']);

    // Parent student attendance url
    Route::get('parent/my_student/attendance/{student_id}', [AttendanceController::class, 'parentStudentAttendance']);

    // Parent notice board url
    Route::get('parent/my_noticeboard', [CommunicateController::class, 'parentNoticeBoard']);

    // Practical works submitted url
    Route::get('parent/my_student/submission/{id}', [WorkController::class, 'parentHomeworkSubmission']);

    //  Fees collections
    Route::get('parent/my_student/feescollections/{student_id}', [FeesCollectionController::class, 'parentStudentFees']);
    Route::post('parent/my_student/feescollections/{student_id}', [FeesCollectionController::class, 'parentStudentFeesCreate'])->name('parentStudentFeesCreate');

    // Paypal payment url of parent
    Route::get('parent/my_fees_paypal/payment_success', [FeesCollectionController::class, 'paypalParentSuccess']);
    Route::get('parent/my_fees_paypal/payment_error', [FeesCollectionController::class, 'paypalParentError']);

    // Stripe payment url of parent
    Route::get('parent/my_fees_stripe/payment_success', [FeesCollectionController::class, 'stripeParentSuccess']);
    Route::get('parent/my_fees_stripe/payment_error', [FeesCollectionController::class, 'stripeParentError']);

    Route::post('/paypal/ipn/parent', [FeesCollectionController::class, 'paypalIPN']);
});
