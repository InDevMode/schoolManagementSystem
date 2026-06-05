import type { NavItem } from '@/types';

// ─── Admin nav ────────────────────────────────────────────────────────────────
export const adminNav: NavItem[] = [
    {
        id: 'dashboard', label: 'Dashboard', icon: 'home',
        href: '/admin/dashboard', permission: 'view.dashboard.admin',
    },
    {
        id: 'users', label: 'Utilisateurs', icon: 'users',
        children: [
            { id: 'admins',   label: 'Administrateurs', icon: 'shield',        href: '/admin/admin/list',   permission: 'view.users.admins'   },
            { id: 'teachers', label: 'Professeurs',      icon: 'academic-cap', href: '/admin/teacher/list', permission: 'view.users.teachers' },
            { id: 'students', label: 'Apprenants',       icon: 'user-group',   href: '/admin/student/list', permission: 'view.users.students' },
            { id: 'parents',  label: 'Parents',          icon: 'users',        href: '/admin/parent/list',  permission: 'view.users.parents'  },
        ],
    },
    {
        id: 'academics', label: 'Académique', icon: 'book-open',
        children: [
            { id: 'classes',         label: 'Classes',          icon: 'building-library', href: '/admin/class/list',            permission: 'view.academics.classes'  },
            { id: 'subjects',        label: 'Matières',         icon: 'book-open',        href: '/admin/subject/list',          permission: 'view.academics.subjects' },
            { id: 'assign-subjects', label: 'Assign. Matières', icon: 'arrows-right-left',href: '/admin/assign_subject/list',   permission: 'view.academics.assign_subjects' },
            { id: 'assign-classes',  label: 'Assign. Classes',  icon: 'arrows-right-left',href: '/admin/assign_class/list',     permission: 'view.academics.assign_classes'  },
            { id: 'timetable',       label: 'Emploi du temps',  icon: 'calendar',         href: '/admin/class_timetable/list',  permission: 'view.academics.timetable' },
        ],
    },
    {
        id: 'examinations', label: 'Évaluations', icon: 'clipboard-document-list',
        children: [
            { id: 'periods',            label: 'Périodes',         icon: 'calendar',           href: '/admin/examinations/period/list',        permission: 'view.exams.periods'    },
            { id: 'exams',              label: 'Sessions',         icon: 'clipboard-document', href: '/admin/examinations/exam/list',           permission: 'view.exams.list'       },
            { id: 'evaluations',        label: 'Évaluations',      icon: 'pencil-square',      href: '/admin/evaluations/list',                 permission: 'view.exams.list'       },
            { id: 'grade-entry',        label: 'Saisie des notes', icon: 'table-cells',        href: '/admin/evaluations/grade-entry',          permission: 'view.exams.marks'      },
            { id: 'grades-pending',     label: 'À valider',        icon: 'check-badge',        href: '/admin/evaluations/grades/pending',       permission: 'action.marks.manage'   },
            { id: 'bulletins',          label: 'Bulletins',        icon: 'document-text',      href: '/admin/bulletins/list',                   permission: 'view.bulletins.list'   },
            { id: 'schedules',          label: 'Programmations',   icon: 'calendar-days',      href: '/admin/examinations/schedule/list',       permission: 'view.exams.schedules'  },
            { id: 'marks-register',     label: 'Registres',        icon: 'table-cells',        href: '/admin/examinations/marks_register/list', permission: 'view.exams.marks'      },
            { id: 'marks-grade',        label: 'Barèmes',          icon: 'chart-bar',          href: '/admin/examinations/marks_grade/list',    permission: 'view.exams.grades'     },
        ],
    },
    {
        id: 'attendance', label: 'Présences', icon: 'user-check',
        children: [
            { id: 'attendance-students', label: 'Saisie',   icon: 'pencil-square', href: '/admin/attendance/students/list', permission: 'view.attendance.manage' },
            { id: 'attendance-report',   label: 'Rapports', icon: 'chart-bar',     href: '/admin/attendance/report',       permission: 'view.attendance.report' },
        ],
    },
    {
        id: 'homework', label: 'Devoirs', icon: 'pencil',
        children: [
            { id: 'homework-list',    label: 'Travaux',  icon: 'document-text', href: '/admin/practicalworks/homework/list', permission: 'view.homework.list'    },
            { id: 'homework-reports', label: 'Rapports', icon: 'chart-bar',     href: '/admin/practicalworks/reports',       permission: 'view.homework.reports' },
        ],
    },
    {
        id: 'fees', label: 'Contributions', icon: 'banknotes',
        children: [
            { id: 'fees-collect', label: 'Collecter', icon: 'plus-circle', href: '/admin/feescollections/collections/list',      permission: 'view.fees.collect' },
            { id: 'fees-list',    label: 'Rapports',  icon: 'chart-bar',   href: '/admin/feescollections/feescollects/feesList',  permission: 'view.fees.reports' },
        ],
    },
    {
        id: 'communicate', label: 'Communication', icon: 'bell',
        children: [
            { id: 'noticeboard', label: "Tableau d'affichage", icon: 'megaphone', href: '/admin/communicate/noticeboard/list', permission: 'view.communicate.noticeboard' },
            { id: 'send-mail',   label: 'Envoyer un mail',     icon: 'envelope',  href: '/admin/communicate/send_mail',        permission: 'view.communicate.mail'       },
        ],
    },
    {
        id: 'settings', label: 'Paramètres', icon: 'cog-6-tooth',
        href: '/admin/settings', permission: 'view.settings',
    },
    {
        id: 'staff', label: 'Personnel & RH', icon: 'user-group',
        children: [
            { id: 'staff-list',        label: 'Personnel',        icon: 'users',          href: '/admin/staff/list',              permission: 'view.staff.list'   },
            { id: 'staff-leaves',      label: 'Congés',           icon: 'calendar-days',  href: '/admin/staff/leaves/list',       permission: 'view.staff.leaves' },
            { id: 'leave-types',       label: 'Types de congés',  icon: 'tag',            href: '/admin/staff/leave-types/list',  permission: 'view.staff.leaves' },
            { id: 'staff-events',      label: 'Événements',       icon: 'sparkles',       href: '/admin/staff/events/list',       permission: 'view.staff.events' },
        ],
    },
];

// ─── Super Admin nav ──────────────────────────────────────────────────────────
export const superAdminNav: NavItem[] = [
    {
        id: 'dashboard', label: 'Dashboard', icon: 'home',
        href: '/superadmin/dashboard',
    },
    {
        id: 'users', label: 'Utilisateurs', icon: 'users',
        children: [
            { id: 'sa-admins',   label: 'Administrateurs', icon: 'shield',        href: '/admin/admin/list',   permission: 'view.users.admins'   },
            { id: 'sa-teachers', label: 'Professeurs',      icon: 'academic-cap', href: '/admin/teacher/list', permission: 'view.users.teachers' },
            { id: 'sa-students', label: 'Apprenants',       icon: 'user-group',   href: '/admin/student/list', permission: 'view.users.students' },
            { id: 'sa-parents',  label: 'Parents',          icon: 'users',        href: '/admin/parent/list',  permission: 'view.users.parents'  },
        ],
    },
    {
        id: 'sa-academics', label: 'Académique', icon: 'book-open',
        children: [
            { id: 'sa-classes',         label: 'Classes',          icon: 'building-library', href: '/admin/class/list',           permission: 'view.academics.classes'         },
            { id: 'sa-subjects',        label: 'Matières',         icon: 'book-open',        href: '/admin/subject/list',         permission: 'view.academics.subjects'        },
            { id: 'sa-assign-subjects', label: 'Assign. Matières', icon: 'arrows-right-left',href: '/admin/assign_subject/list',  permission: 'view.academics.assign_subjects' },
            { id: 'sa-assign-classes',  label: 'Assign. Classes',  icon: 'arrows-right-left',href: '/admin/assign_class/list',    permission: 'view.academics.assign_classes'  },
            { id: 'sa-timetable',       label: 'Emploi du temps',  icon: 'calendar',         href: '/admin/class_timetable/list', permission: 'view.academics.timetable'       },
        ],
    },
    {
        id: 'sa-examinations', label: 'Évaluations', icon: 'clipboard-document-list',
        children: [
            { id: 'sa-periods',        label: 'Périodes',         icon: 'calendar',           href: '/admin/examinations/period/list',        permission: 'view.exams.periods'    },
            { id: 'sa-exams',          label: 'Sessions',         icon: 'clipboard-document', href: '/admin/examinations/exam/list',           permission: 'view.exams.list'       },
            { id: 'sa-evaluations',    label: 'Évaluations',      icon: 'pencil-square',      href: '/admin/evaluations/list',                 permission: 'view.exams.list'       },
            { id: 'sa-grade-entry',    label: 'Saisie des notes', icon: 'table-cells',        href: '/admin/evaluations/grade-entry',          permission: 'view.exams.marks'      },
            { id: 'sa-grades-pending', label: 'À valider',        icon: 'check-badge',        href: '/admin/evaluations/grades/pending',       permission: 'action.marks.manage'   },
            { id: 'sa-bulletins',      label: 'Bulletins',        icon: 'document-text',      href: '/admin/bulletins/list',                   permission: 'view.bulletins.list'   },
            { id: 'sa-schedules',      label: 'Programmations',   icon: 'calendar-days',      href: '/admin/examinations/schedule/list',       permission: 'view.exams.schedules'  },
            { id: 'sa-marks-register', label: 'Registres',        icon: 'table-cells',        href: '/admin/examinations/marks_register/list', permission: 'view.exams.marks'      },
            { id: 'sa-marks-grade',    label: 'Barèmes',          icon: 'chart-bar',          href: '/admin/examinations/marks_grade/list',    permission: 'view.exams.grades'     },
        ],
    },
    {
        id: 'sa-staff', label: 'Ressources Humaines', icon: 'user-group',
        children: [
            { id: 'sa-staff-list',   label: 'Personnel',       icon: 'users',         href: '/admin/staff/list',             permission: 'view.staff.list'   },
            { id: 'sa-staff-leaves', label: 'Congés',          icon: 'calendar-days', href: '/admin/staff/leaves/list',      permission: 'view.staff.leaves' },
            { id: 'sa-leave-types',  label: 'Types de congés', icon: 'tag',           href: '/admin/staff/leave-types/list', permission: 'view.staff.leaves' },
            { id: 'sa-staff-events', label: 'Événements',      icon: 'sparkles',      href: '/admin/staff/events/list',      permission: 'view.staff.events' },
        ],
    },
    {
        id: 'sa-attendance', label: 'Présences', icon: 'user-check',
        children: [
            { id: 'sa-attendance-students', label: 'Saisie',   icon: 'pencil-square', href: '/admin/attendance/students/list', permission: 'view.attendance.manage' },
            { id: 'sa-attendance-report',   label: 'Rapports', icon: 'chart-bar',     href: '/admin/attendance/report',       permission: 'view.attendance.report' },
        ],
    },
    {
        id: 'sa-homework', label: 'Devoirs', icon: 'pencil',
        children: [
            { id: 'sa-homework-list',    label: 'Travaux',  icon: 'document-text', href: '/admin/practicalworks/homework/list', permission: 'view.homework.list'    },
            { id: 'sa-homework-reports', label: 'Rapports', icon: 'chart-bar',     href: '/admin/practicalworks/reports',       permission: 'view.homework.reports' },
        ],
    },
    {
        id: 'sa-fees', label: 'Contributions', icon: 'banknotes',
        children: [
            { id: 'sa-fees-collect', label: 'Collecter', icon: 'plus-circle', href: '/admin/feescollections/collections/list',     permission: 'view.fees.collect' },
            { id: 'sa-fees-list',    label: 'Rapports',  icon: 'chart-bar',   href: '/admin/feescollections/feescollects/feesList', permission: 'view.fees.reports' },
        ],
    },
    {
        id: 'sa-communicate', label: 'Communication', icon: 'bell',
        children: [
            { id: 'sa-noticeboard', label: "Tableau d'affichage", icon: 'megaphone', href: '/admin/communicate/noticeboard/list', permission: 'view.communicate.noticeboard' },
            { id: 'sa-send-mail',   label: 'Envoyer un mail',     icon: 'envelope',  href: '/admin/communicate/send_mail',        permission: 'view.communicate.mail'       },
        ],
    },
    // ── Configuration — exclusif super_admin (pas de permission = toujours visible) ──
    {
        id: 'configuration', label: 'Configuration', icon: 'cog-6-tooth',
        children: [
            { id: 'sa-settings',    label: 'Paramètres',            icon: 'cog-6-tooth',       href: '/superadmin/config/settings' },
            { id: 'sa-roles',       label: 'Rôles',                 icon: 'shield-check',      href: '/superadmin/config/roles' },
            { id: 'sa-permissions', label: 'Permissions',           icon: 'key',               href: '/superadmin/config/permissions' },
            { id: 'sa-assign',      label: 'Attribuer permissions', icon: 'arrows-right-left', href: '/superadmin/config/assign' },
            { id: 'sa-del-logs',    label: 'Journaux suppression',  icon: 'trash',             href: '/superadmin/deletion-logs' },
        ],
    },
];


// ─── Student nav ──────────────────────────────────────────────────────────────
export const studentNav: NavItem[] = [
    { id: 'dashboard',   label: 'Dashboard',       icon: 'home',                    href: '/student/dashboard' },
    { id: 'subjects',    label: 'Mes Matières',    icon: 'book-open',               href: '/student/my_subject' },
    { id: 'timetable',   label: 'Emploi du temps', icon: 'calendar',                href: '/student/my_timetable' },
    { id: 'exams',       label: 'Évaluations',     icon: 'clipboard-document-list', href: '/student/my_exam_timetable' },
    { id: 'grades',      label: 'Mes Notes',       icon: 'pencil-square',           href: '/student/my_grades' },
    { id: 'bulletins',   label: 'Mes Bulletins',   icon: 'document-text',           href: '/student/my_bulletins' },
    { id: 'results',     label: 'Mes Résultats',   icon: 'chart-bar',               href: '/student/my_exam_result' },
    { id: 'attendance',  label: 'Ma Présence',     icon: 'user-check',              href: '/student/my_attendance' },
    { id: 'homework',    label: 'Mes Devoirs',     icon: 'pencil',                  href: '/student/my_homework' },
    { id: 'fees',        label: 'Contributions',   icon: 'banknotes',               href: '/student/my_fees' },
    { id: 'noticeboard', label: 'Notifications',   icon: 'bell',                    href: '/student/my_noticeboard' },
    { id: 'calendar',    label: 'Calendrier',      icon: 'calendar',                href: '/student/my_calendar' },
];

// ─── Parent nav ───────────────────────────────────────────────────────────────
export const parentNav: NavItem[] = [
    { id: 'dashboard',   label: 'Dashboard',     icon: 'home',       href: '/parent/dashboard' },
    { id: 'my-students', label: 'Mes Enfants',   icon: 'user-group', href: '/parent/my_student' },
    { id: 'noticeboard', label: 'Notifications', icon: 'bell',       href: '/parent/my_noticeboard' },
];

// ─── Teacher nav (mis à jour) ─────────────────────────────────────────────────
export const teacherNav: NavItem[] = [
    { id: 'dashboard',      label: 'Dashboard',          icon: 'home',                    href: '/teacher/dashboard' },
    { id: 'my-students',    label: 'Mes Apprenants',     icon: 'user-group',              href: '/teacher/my_student' },
    { id: 'class-subject',  label: 'Matières & Classes', icon: 'book-open',               href: '/teacher/class_subject' },
    { id: 'evaluations',    label: 'Évaluations',        icon: 'pencil-square',           href: '/teacher/evaluations' },
    { id: 'grade-entry',    label: 'Saisie notes',       icon: 'clipboard-document-list', href: '/teacher/evaluations/grade-entry' },
    { id: 'marks-register', label: 'Registre (ancien)',  icon: 'table-cells',             href: '/teacher/marks_register' },
    {
        id: 'attendance', label: 'Présences', icon: 'user-check',
        children: [
            { id: 'attendance-list',   label: 'Saisie',   icon: 'pencil-square', href: '/teacher/attendance/student/list' },
            { id: 'attendance-report', label: 'Rapports', icon: 'chart-bar',     href: '/teacher/attendance/report' },
        ],
    },
    { id: 'homework',    label: 'Devoirs',       icon: 'pencil',   href: '/teacher/practicalworks/homework/list' },
    { id: 'noticeboard', label: 'Notifications', icon: 'bell',     href: '/teacher/my_noticeboard' },
    { id: 'calendar',    label: 'Calendrier',    icon: 'calendar', href: '/teacher/my_calendar' },
];

// ─── Map role → nav ───────────────────────────────────────────────────────────
export const navByRole: Record<number, NavItem[]> = {
    0: superAdminNav,
    1: adminNav,
    2: teacherNav,
    3: studentNav,
    4: parentNav,
};

// Rôles custom (user_type >= 5) utilisent adminNav filtré par leurs permissions
export const getNavForUserType = (userType: number): NavItem[] => {
    return navByRole[userType] ?? adminNav;
};
