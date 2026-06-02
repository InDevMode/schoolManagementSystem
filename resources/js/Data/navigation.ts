import type { NavItem } from '@/types';

export const adminNav: NavItem[] = [
    {
        id: 'dashboard',
        label: 'Dashboard',
        icon: 'home',
        href: '/admin/dashboard',
    },
    {
        id: 'users',
        label: 'Utilisateurs',
        icon: 'users',
        children: [
            { id: 'admins',   label: 'Administrateurs', icon: 'shield',       href: '/admin/admin/list' },
            { id: 'teachers', label: 'Professeurs',      icon: 'academic-cap', href: '/admin/teacher/list' },
            { id: 'students', label: 'Apprenants',       icon: 'user-group',   href: '/admin/student/list' },
            { id: 'parents',  label: 'Parents',          icon: 'users',        href: '/admin/parent/list' },
        ],
    },
    {
        id: 'academics',
        label: 'Académique',
        icon: 'book-open',
        children: [
            { id: 'classes',         label: 'Classes',          icon: 'building-library', href: '/admin/class/list' },
            { id: 'subjects',        label: 'Matières',         icon: 'book-open',        href: '/admin/subject/list' },
            { id: 'assign-subjects', label: 'Assign. Matières', icon: 'arrows-right-left',href: '/admin/assign_subject/list' },
            { id: 'assign-classes',  label: 'Assign. Classes',  icon: 'arrows-right-left',href: '/admin/assign_class/list' },
            { id: 'timetable',       label: 'Emploi du temps',  icon: 'calendar',         href: '/admin/class_timetable/list' },
        ],
    },
    {
        id: 'examinations',
        label: 'Évaluations',
        icon: 'clipboard-document-list',
        children: [
            { id: 'periods',        label: 'Périodes',       icon: 'calendar',              href: '/admin/examinations/period/list' },
            { id: 'exams',          label: 'Examens',        icon: 'clipboard-document',    href: '/admin/examinations/exam/list' },
            { id: 'schedules',      label: 'Programmations', icon: 'calendar-days',         href: '/admin/examinations/schedule/list' },
            { id: 'marks-register', label: 'Registres',      icon: 'table-cells',           href: '/admin/examinations/marks_register/list' },
            { id: 'marks-grade',    label: 'Barèmes',        icon: 'chart-bar',             href: '/admin/examinations/marks_grade/list' },
        ],
    },
    {
        id: 'attendance',
        label: 'Présences',
        icon: 'user-check',
        children: [
            { id: 'attendance-students', label: 'Saisie',   icon: 'pencil-square', href: '/admin/attendance/students/list' },
            { id: 'attendance-report',   label: 'Rapports', icon: 'chart-bar',     href: '/admin/attendance/report' },
        ],
    },
    {
        id: 'homework',
        label: 'Devoirs',
        icon: 'pencil',
        children: [
            { id: 'homework-list',    label: 'Travaux',  icon: 'document-text', href: '/admin/practicalworks/homework/list' },
            { id: 'homework-reports', label: 'Rapports', icon: 'chart-bar',     href: '/admin/practicalworks/reports' },
        ],
    },
    {
        id: 'fees',
        label: 'Contributions',
        icon: 'banknotes',
        children: [
            { id: 'fees-collect', label: 'Collecter', icon: 'plus-circle', href: '/admin/feescollections/collections/list' },
            { id: 'fees-list',    label: 'Rapports',  icon: 'chart-bar',   href: '/admin/feescollections/feescollects/feesList' },
        ],
    },
    {
        id: 'communicate',
        label: 'Communication',
        icon: 'bell',
        children: [
            { id: 'noticeboard', label: 'Tableau d\'affichage', icon: 'megaphone', href: '/admin/communicate/noticeboard/list' },
            { id: 'send-mail',   label: 'Envoyer un mail',      icon: 'envelope',  href: '/admin/communicate/send_mail' },
        ],
    },
    {
        id: 'settings',
        label: 'Paramètres',
        icon: 'cog-6-tooth',
        href: '/admin/settings',
    },
];

export const teacherNav: NavItem[] = [
    { id: 'dashboard',      label: 'Dashboard',        icon: 'home',                  href: '/teacher/dashboard' },
    { id: 'my-students',    label: 'Mes Apprenants',   icon: 'user-group',            href: '/teacher/my_student' },
    { id: 'class-subject',  label: 'Matières & Classes',icon: 'book-open',            href: '/teacher/class_subject' },
    { id: 'marks-register', label: 'Registre',         icon: 'clipboard-document-list',href: '/teacher/marks_register' },
    {
        id: 'attendance',
        label: 'Présences',
        icon: 'user-check',
        children: [
            { id: 'attendance-list',   label: 'Saisie',   icon: 'pencil-square', href: '/teacher/attendance/student/list' },
            { id: 'attendance-report', label: 'Rapports', icon: 'chart-bar',     href: '/teacher/attendance/report' },
        ],
    },
    { id: 'homework',       label: 'Devoirs',          icon: 'pencil',                href: '/teacher/practicalworks/homework/list' },
    { id: 'noticeboard',    label: 'Notifications',    icon: 'bell',                  href: '/teacher/my_noticeboard' },
    { id: 'calendar',       label: 'Calendrier',       icon: 'calendar',              href: '/teacher/my_calendar' },
];

export const studentNav: NavItem[] = [
    { id: 'dashboard',    label: 'Dashboard',       icon: 'home',                   href: '/student/dashboard' },
    { id: 'subjects',     label: 'Mes Matières',    icon: 'book-open',              href: '/student/my_subject' },
    { id: 'timetable',    label: 'Emploi du temps', icon: 'calendar',               href: '/student/my_timetable' },
    { id: 'exams',        label: 'Évaluations',     icon: 'clipboard-document-list',href: '/student/my_exam_timetable' },
    { id: 'results',      label: 'Mes Résultats',   icon: 'chart-bar',              href: '/student/my_exam_result' },
    { id: 'attendance',   label: 'Ma Présence',     icon: 'user-check',             href: '/student/my_attendance' },
    { id: 'homework',     label: 'Mes Devoirs',     icon: 'pencil',                 href: '/student/my_homework' },
    { id: 'fees',         label: 'Contributions',   icon: 'banknotes',              href: '/student/my_fees' },
    { id: 'noticeboard',  label: 'Notifications',   icon: 'bell',                   href: '/student/my_noticeboard' },
    { id: 'calendar',     label: 'Calendrier',      icon: 'calendar',               href: '/student/my_calendar' },
];

export const parentNav: NavItem[] = [
    { id: 'dashboard',   label: 'Dashboard',      icon: 'home',       href: '/parent/dashboard' },
    { id: 'my-students', label: 'Mes Enfants',    icon: 'user-group', href: '/parent/my_student' },
    { id: 'noticeboard', label: 'Notifications',  icon: 'bell',       href: '/parent/my_noticeboard' },
];

export const navByRole: Record<number, NavItem[]> = {
    1: adminNav,
    2: teacherNav,
    3: studentNav,
    4: parentNav,
};
