<template>
    <div class="space-y-5">

        <!-- ══ HEADER ═══════════════════════════════════════════════════════ -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Tableau de bord de l'ensemble du système</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ today.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold shadow-lg shadow-primary-500/30"
                      style="background: linear-gradient(135deg, #7B74F0, #9189f5);">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse inline-block flex-shrink-0"/>
                    <span class="text-white">Super Admin</span>
                </span>
            </div>
        </div>

        <!-- ══ TABS ══════════════════════════════════════════════════════════ -->
        <DashTabs :tabs="tabs">
            <template #default="{ active }">

                <!-- ── TAB : VUE GÉNÉRALE ──────────────────────────────────── -->
                <div v-show="active === 'overview'" class="space-y-5">

                    <!-- ── SECTION UTILISATEURS ────────────────────────── -->
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3 px-0.5">Utilisateurs du système</h3>
                        <!-- Ligne 1 : Super Admin | Admins | Professeurs | Apprenants | Parents | Personnel -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

                            <!-- Super Admin -->
                            <div class="relative overflow-hidden rounded-2xl p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 shadow-sm">
                                <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full opacity-10 dark:opacity-5 pointer-events-none bg-slate-400"/>
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-md bg-gradient-to-br from-slate-500 to-slate-700 mb-2">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-xl font-black text-gray-900 dark:text-white leading-none tabular-nums">{{ totalSuperAdmin }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Super Admins</p>
                                <div class="mt-1.5 space-y-0.5">
                                    <div class="flex items-center justify-between text-[10px]">
                                        <span class="text-blue-600 font-semibold">{{ totalSuperAdminMale ?? 0 }}H · {{ safePercent(totalSuperAdminMale, totalSuperAdmin) }}%</span>
                                    </div>
                                    <div class="flex items-center justify-between text-[10px]">
                                        <span class="text-pink-500 font-semibold">{{ totalSuperAdminFemale ?? 0 }}F · {{ safePercent(totalSuperAdminFemale, totalSuperAdmin) }}%</span>
                                    </div>
                                    <div class="text-[10px] text-gray-400 font-medium">{{ safePercent(totalSuperAdmin, totalUser) }}% du total</div>
                                </div>
                            </div>

                            <!-- Admins -->
                            <a href="/admin/admin/list" class="relative overflow-hidden rounded-2xl p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 shadow-sm cursor-pointer hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                                <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full opacity-10 dark:opacity-5 pointer-events-none bg-red-400"/>
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-md bg-gradient-to-br from-red-400 to-red-600 mb-2">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <p class="text-xl font-black text-gray-900 dark:text-white leading-none tabular-nums">{{ totalAdmin }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Admins</p>
                                <div class="mt-1.5 space-y-0.5">
                                    <div class="text-[10px] text-blue-600 font-semibold">{{ totalAdminMale ?? 0 }}H · {{ safePercent(totalAdminMale, totalAdmin) }}%</div>
                                    <div class="text-[10px] text-pink-500 font-semibold">{{ totalAdminFemale ?? 0 }}F · {{ safePercent(totalAdminFemale, totalAdmin) }}%</div>
                                    <div class="text-[10px] text-gray-400 font-medium">{{ safePercent(totalAdmin, totalUser) }}% du total</div>
                                </div>
                            </a>

                            <!-- Professeurs -->
                            <a href="/admin/teacher/list" class="relative overflow-hidden rounded-2xl p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 shadow-sm cursor-pointer hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                                <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full opacity-10 dark:opacity-5 pointer-events-none bg-blue-400"/>
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-md bg-gradient-to-br from-blue-400 to-blue-600 mb-2">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                </div>
                                <p class="text-xl font-black text-gray-900 dark:text-white leading-none tabular-nums">{{ totalTeacher }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Professeurs</p>
                                <div class="mt-1.5 space-y-0.5">
                                    <div class="text-[10px] text-blue-600 font-semibold">{{ totalTeacherMale ?? 0 }}H · {{ safePercent(totalTeacherMale, totalTeacher) }}%</div>
                                    <div class="text-[10px] text-pink-500 font-semibold">{{ totalTeacherFemale ?? 0 }}F · {{ safePercent(totalTeacherFemale, totalTeacher) }}%</div>
                                    <div class="text-[10px] text-gray-400 font-medium">{{ safePercent(totalTeacher, totalUser) }}% du total</div>
                                </div>
                            </a>

                            <!-- Apprenants -->
                            <a href="/admin/student/list" class="relative overflow-hidden rounded-2xl p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 shadow-sm cursor-pointer hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                                <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full opacity-10 dark:opacity-5 pointer-events-none bg-violet-400"/>
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-md bg-gradient-to-br from-violet-400 to-violet-600 mb-2">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <p class="text-xl font-black text-gray-900 dark:text-white leading-none tabular-nums">{{ totalStudent }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Apprenants</p>
                                <div class="mt-1.5 space-y-0.5">
                                    <div class="text-[10px] text-blue-600 font-semibold">{{ totalStudentMale ?? 0 }}H · {{ safePercent(totalStudentMale, totalStudent) }}%</div>
                                    <div class="text-[10px] text-pink-500 font-semibold">{{ totalStudentFemale ?? 0 }}F · {{ safePercent(totalStudentFemale, totalStudent) }}%</div>
                                    <div class="text-[10px] text-gray-400 font-medium">{{ safePercent(totalStudent, totalUser) }}% du total</div>
                                </div>
                            </a>

                            <!-- Parents -->
                            <a href="/admin/parent/list" class="relative overflow-hidden rounded-2xl p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 shadow-sm cursor-pointer hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                                <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full opacity-10 dark:opacity-5 pointer-events-none bg-amber-400"/>
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-md bg-gradient-to-br from-amber-400 to-orange-500 mb-2">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <p class="text-xl font-black text-gray-900 dark:text-white leading-none tabular-nums">{{ totalParent }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Parents</p>
                                <div class="mt-1.5 space-y-0.5">
                                    <div class="text-[10px] text-blue-600 font-semibold">{{ totalParentMale ?? 0 }}H · {{ safePercent(totalParentMale, totalParent) }}%</div>
                                    <div class="text-[10px] text-pink-500 font-semibold">{{ totalParentFemale ?? 0 }}F · {{ safePercent(totalParentFemale, totalParent) }}%</div>
                                    <div class="text-[10px] text-gray-400 font-medium">{{ safePercent(totalParent, totalUser) }}% du total</div>
                                </div>
                            </a>

                            <!-- Personnel -->
                            <a href="/admin/staff/list" class="relative overflow-hidden rounded-2xl p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 shadow-sm cursor-pointer hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                                <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full opacity-10 dark:opacity-5 pointer-events-none bg-emerald-400"/>
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-md bg-gradient-to-br from-emerald-400 to-emerald-600 mb-2">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-xl font-black text-gray-900 dark:text-white leading-none tabular-nums">{{ totalStaff ?? 0 }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Personnel</p>
                                <div class="mt-1.5 space-y-0.5">
                                    <div class="text-[10px] text-gray-400 font-medium">Staff RH actif</div>
                                    <div class="text-[10px] text-gray-400 font-medium">{{ safePercent(totalStaff, totalUser) }}% du total</div>
                                </div>
                            </a>

                        </div>
                    </div>

                    <!-- ── SECTION ÉCOLES ────────────────────────────── -->
                    <div v-if="schoolsStats?.length">
                        <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3 px-0.5">Écoles</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                            <div v-for="school in schoolsStats" :key="school.school_id"
                                class="relative overflow-hidden rounded-2xl p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 shadow-sm hover:shadow-md transition-all">
                                <div class="absolute -top-3 -right-3 w-16 h-16 rounded-full opacity-10 bg-violet-400 pointer-events-none"/>
                                <!-- Nom de l'école -->
                                <div class="flex items-start gap-2 mb-3">
                                    <div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center text-white text-xs font-bold shadow-sm"
                                        :style="{ background: schoolColor(school.school_id) }">
                                        {{ (school.school_name?.[0] ?? '?').toUpperCase() }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-gray-900 dark:text-white truncate" :title="school.school_name">{{ school.school_name }}</p>
                                        <p class="text-[10px] text-gray-400">{{ school.total_users }} utilisateurs</p>
                                    </div>
                                </div>
                                <!-- Stats utilisateurs -->
                                <div class="grid grid-cols-2 gap-x-3 gap-y-1 text-[10px]">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Apprenants</span>
                                        <span class="font-bold text-violet-600">{{ school.total_students }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Professeurs</span>
                                        <span class="font-bold text-blue-600">{{ school.total_teachers }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Parents</span>
                                        <span class="font-bold text-amber-600">{{ school.total_parents }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Admins</span>
                                        <span class="font-bold text-red-600">{{ school.total_admins }}</span>
                                    </div>
                                    <div class="flex justify-between col-span-2">
                                        <span class="text-gray-500">Personnel</span>
                                        <span class="font-bold text-emerald-600">{{ school.total_staff }}</span>
                                    </div>
                                </div>
                                <!-- Barre de répartition -->
                                <div class="mt-2 flex h-1.5 rounded-full overflow-hidden gap-px">
                                    <div class="bg-violet-500 rounded-l-full transition-all" :style="{ width: safePercent(school.total_students, school.total_users) + '%' }"/>
                                    <div class="bg-blue-500 transition-all"                   :style="{ width: safePercent(school.total_teachers, school.total_users) + '%' }"/>
                                    <div class="bg-amber-500 transition-all"                   :style="{ width: safePercent(school.total_parents,  school.total_users) + '%' }"/>
                                    <div class="bg-red-500 transition-all"                     :style="{ width: safePercent(school.total_admins,   school.total_users) + '%' }"/>
                                    <div class="bg-emerald-500 rounded-r-full transition-all"  :style="{ width: safePercent(school.total_staff,    school.total_users) + '%' }"/>
                                </div>
                                <div class="mt-1 text-[9px] text-gray-400 text-right">{{ safePercent(school.total_students, school.total_users) }}% apprenants</div>
                            </div>
                        </div>
                    </div>

                    <!-- ── SECTION ACADÉMIQUE & EXAMENS ──────────────── -->
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3 px-0.5">Académique & Examens</h3>
                        <div class="grid grid-cols-3 sm:grid-cols-5 gap-2.5">
                            <MiniCard label="Classes"          :value="totalClass"           icon="building-library"        color="sky"     href="/admin/class/list"/>
                            <MiniCard label="Matières"         :value="totalSubject"         icon="book-open"               color="teal"    href="/admin/subject/list"/>
                            <MiniCard label="Assign. matières" :value="totalClassSubject ?? 0" icon="link"                  color="blue"    href="/admin/subject/list"/>
                            <MiniCard label="Périodes"         :value="totalExam"            icon="clipboard-document-list" color="orange"  href="/admin/examinations/period/list"/>
                            <MiniCard label="Devoirs"          :value="totalHomework ?? 0"   icon="pencil"                  color="rose"    href="/admin/practicalworks/homework/list"/>
                        </div>
                    </div>

                    <!-- ── SECTION CONFIGURATION SYSTÈME ─────────────── -->
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3 px-0.5">Configuration système</h3>
                        <div class="grid grid-cols-3 sm:grid-cols-5 gap-2.5">
                            <MiniCard label="Rôles système"      :value="totalRoles"                   icon="shield-check"   color="violet" href="/superadmin/config/roles"/>
                            <MiniCard label="Permissions"        :value="totalPermissions"             icon="key"            color="blue"   href="/superadmin/config/permissions"/>
                            <MiniCard label="Écoles"             :value="totalSchools ?? 0"            icon="building-office" color="slate" href="/superadmin/schools"/>
                            <MiniCard label="Journaux suppr."    :value="totalDeletionLogs ?? 0"       icon="document-text"  color="orange" href="/superadmin/deletion-logs"/>
                            <MiniCard label="Attr. permissions"  :value="totalPermissionAssignments ?? 0" icon="user-check"  color="teal"   href="/superadmin/config/assign"/>
                        </div>
                    </div>

                    <!-- ── ALERTES ─────────────────────────────────────── -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <AlertCard label="Congés en attente"  :value="totalPendingLeaves ?? 0"  icon="calendar-days" variant="warning" href="/admin/staff/leaves/list"/>
                        <AlertCard label="Notes à valider"    :value="totalPendingGrades ?? 0"  icon="check-badge"   variant="danger"  href="/admin/evaluations/grades/pending"/>
                        <AlertCard label="Bulletins brouillon":value="totalDraftBulletins ?? 0" icon="document-text" variant="info"    href="/admin/bulletins/list"/>
                        <AlertCard label="Événements à venir" :value="totalUpcomingEvents ?? 0" icon="sparkles"      variant="default" href="/admin/staff/events/list"/>
                    </div>

                    <!-- ── CHARTS ──────────────────────────────────────── -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-0.5">Répartition utilisateurs</h3>
                            <p class="text-xs text-gray-400 mb-2">Par rôle</p>
                            <ApexDonut
                                :series="[totalStudent, totalTeacher, totalParent, totalAdmin, totalStaff ?? 0]"
                                :labels="['Apprenants', 'Professeurs', 'Parents', 'Admins', 'Personnel']"
                                :colors="['#7C3AED', '#3B82F6', '#F59E0B', '#EF4444', '#10B981']"
                                center-label="Utilisateurs"
                                :center-value="totalUser"
                                :height="150"
                            />
                        </div>
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-0.5">Apprenants par sexe</h3>
                            <p class="text-xs text-gray-400 mb-2">Répartition garçons / filles</p>
                            <ApexDonut
                                :series="[totalStudentMale ?? 0, totalStudentFemale ?? 0]"
                                :labels="['Garçons', 'Filles']"
                                :colors="['#7C3AED', '#F472B6']"
                                center-label="Total"
                                :center-value="totalStudent"
                                :height="150"
                            />
                        </div>
                        <div class="card p-4 flex flex-col">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-0.5">Contributions</h3>
                            <p class="text-xs text-gray-400 mb-3">Paiements collectés</p>
                            <div class="space-y-3 flex-1">
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-500">Total dossiers</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ (totalFeesCollections ?? 0).toLocaleString('fr-FR') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-500">Aujourd'hui</span>
                                    <span class="text-sm font-bold text-emerald-600">{{ (totalFeesCollectionsToday ?? 0).toLocaleString('fr-FR') }}</span>
                                </div>
                                <ProgressBar label="Taux collecte" :value="totalFeesCollections ?? 0" :max="totalStudent" color="violet" />
                            </div>
                            <a href="/admin/feescollections/collections/list" class="mt-3 text-center text-xs font-medium text-primary-600 dark:text-primary-400 hover:underline block">
                                Voir les contributions →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ── TAB : PRÉSENCES ───────────────────────────────────── -->
                <div v-show="active === 'attendance'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Statistiques de présence — Tous les apprenants</h2>
                        <PeriodFilter v-model="attendancePeriod" />
                    </div>

                    <!-- Badges résumé global -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <AttendanceBadge label="Présents"     :value="totalAttendanceStudentPresent"  color="success" icon="user-check"/>
                        <AttendanceBadge label="En retard"    :value="totalAttendanceStudentLate"     color="warning" icon="clock"/>
                        <AttendanceBadge label="Absents"      :value="totalAttendanceStudentAbsent"   color="danger"  icon="user-minus"/>
                        <AttendanceBadge label="Demi-journée" :value="totalAttendanceStudentHalfDay"  color="info"    icon="calendar-days"/>
                    </div>

                    <!-- Tableau par école — amélioré avec totaux -->
                    <div class="card overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Présences par école</h3>
                            <span class="text-xs text-gray-400">{{ attendanceBySchool?.length ?? 0 }} école(s)</span>
                        </div>
                        <div v-if="attendanceBySchool?.length" class="overflow-x-auto">
                            <table class="w-full min-w-[540px]">
                                <thead class="bg-gray-50 dark:bg-gray-700/40">
                                    <tr>
                                        <th class="text-left px-4 py-2.5 text-xs font-bold text-gray-600 dark:text-gray-300 border-b border-gray-100 dark:border-gray-700">École</th>
                                        <th class="text-right px-3 py-2.5 text-xs font-bold text-emerald-600 border-b border-gray-100 dark:border-gray-700">
                                            <div class="flex items-center justify-end gap-1">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"/>Présents
                                            </div>
                                        </th>
                                        <th class="text-right px-3 py-2.5 text-xs font-bold text-amber-600 border-b border-gray-100 dark:border-gray-700">
                                            <div class="flex items-center justify-end gap-1">
                                                <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"/>Retards
                                            </div>
                                        </th>
                                        <th class="text-right px-3 py-2.5 text-xs font-bold text-red-600 border-b border-gray-100 dark:border-gray-700">
                                            <div class="flex items-center justify-end gap-1">
                                                <span class="w-2 h-2 rounded-full bg-red-500 inline-block"/>Absents
                                            </div>
                                        </th>
                                        <th class="text-right px-4 py-2.5 text-xs font-bold text-blue-600 border-b border-gray-100 dark:border-gray-700">
                                            <div class="flex items-center justify-end gap-1">
                                                <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"/>Demi-j.
                                            </div>
                                        </th>
                                        <th class="text-right px-4 py-2.5 text-xs font-bold text-gray-500 border-b border-gray-100 dark:border-gray-700">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/40">
                                    <tr v-for="school in attendanceBySchool" :key="school.school_id"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <span class="w-6 h-6 rounded-md flex-shrink-0 flex items-center justify-center text-white text-[9px] font-bold shadow-sm"
                                                    :style="{ background: schoolColor(school.school_id) }">
                                                    {{ (school.school_name?.[0] ?? '?').toUpperCase() }}
                                                </span>
                                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 truncate max-w-[160px]" :title="school.school_name">{{ school.school_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <span class="text-sm font-bold text-emerald-600">{{ school.present.toLocaleString('fr-FR') }}</span>
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <span class="text-sm font-bold text-amber-600">{{ school.late.toLocaleString('fr-FR') }}</span>
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <span class="text-sm font-bold text-red-600">{{ school.absent.toLocaleString('fr-FR') }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="text-sm font-bold text-blue-600">{{ school.halfday.toLocaleString('fr-FR') }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">
                                                {{ (school.present + school.late + school.absent + school.halfday).toLocaleString('fr-FR') }}
                                            </span>
                                        </td>
                                    </tr>
                                    <!-- Ligne total -->
                                    <tr class="bg-gray-50 dark:bg-gray-700/20 font-bold">
                                        <td class="px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200">TOTAL SYSTÈME</td>
                                        <td class="px-3 py-2.5 text-right text-xs font-bold text-emerald-700">{{ totalAttendanceStudentPresent.toLocaleString('fr-FR') }}</td>
                                        <td class="px-3 py-2.5 text-right text-xs font-bold text-amber-700">{{ totalAttendanceStudentLate.toLocaleString('fr-FR') }}</td>
                                        <td class="px-3 py-2.5 text-right text-xs font-bold text-red-700">{{ totalAttendanceStudentAbsent.toLocaleString('fr-FR') }}</td>
                                        <td class="px-4 py-2.5 text-right text-xs font-bold text-blue-700">{{ totalAttendanceStudentHalfDay.toLocaleString('fr-FR') }}</td>
                                        <td class="px-4 py-2.5 text-right">
                                            <span class="text-xs font-black text-gray-700 dark:text-gray-200 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded-full">
                                                {{ (totalAttendanceStudentPresent + totalAttendanceStudentLate + totalAttendanceStudentAbsent + totalAttendanceStudentHalfDay).toLocaleString('fr-FR') }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="px-4 py-8 text-center text-xs text-gray-400">
                            Aucune donnée de présence par école disponible
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Évolution mensuelle</h3>
                            <div class="overflow-x-auto">
                                <div style="min-width: 320px;">
                                    <ApexArea :series="attendanceSeries" :categories="months" :colors="['#10B981','#F59E0B','#EF4444','#3B82F6']" :height="150"/>
                                </div>
                            </div>
                        </div>
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Taux de présence</h3>
                            <ApexRadial :series="attendanceRadial" :labels="['Présents','En retard','Absents','Demi-j.']" :colors="['#10B981','#F59E0B','#EF4444','#3B82F6']"/>
                        </div>
                    </div>

                    <!-- Rapport mensuel présents -->
                    <div class="card p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Rapport mensuel (présents)</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            <ProgressBar v-for="(m, i) in months" :key="m" :label="m"
                                :value="(attendanceByMonth as any)?.present?.[i] ?? 0"
                                :max="totalAttendanceStudentPresent || 1"
                                :color="['success','info','violet','warning','primary','amber','success','info','violet','warning','primary','amber'][i] as any"
                            />
                        </div>
                    </div>
                </div>

                <!-- ── TAB : ACADÉMIQUE ──────────────────────────────────── -->
                <div v-show="active === 'academic'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Sessions & Évaluations</h2>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <KpiCard label="Sessions actives"    :value="totalExam"           color="violet" icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        <KpiCard label="Évals ouvertes"      :value="totalOpenEvals ?? 0" color="info"   icon="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        <KpiCard label="Notes à valider"     :value="totalPendingGrades ?? 0" color="warning" icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <KpiCard label="Bulletins brouillon" :value="totalDraftBulletins ?? 0" color="danger"  icon="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </div>
                    <div class="card p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Évaluations par type</h3>
                        </div>
                        <ApexBar :series="evalTypeSeries" :categories="['Interro', 'Devoir', 'Travail maison', 'Examen blanc']" :colors="['#7C3AED','#3B82F6','#10B981','#F59E0B']" :height="150"/>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Sessions académiques</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-700/40">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Période courante</span>
                                    <span class="text-sm font-bold text-violet-600 dark:text-violet-400">{{ currentPeriod?.name ?? '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-700/40">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Année scolaire</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ currentPeriod?.school_year ?? '—' }}</span>
                                </div>
                                <ProgressBar label="Bulletins publiés" :value="totalPublishedBulletins ?? 0" :max="totalStudent || 1" color="success" />
                                <ProgressBar label="Bulletins brouillon" :value="totalDraftBulletins ?? 0" :max="totalStudent || 1" color="warning" />
                            </div>
                        </div>
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Moyennes remarquables</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Meilleure moyenne</span>
                                    <span class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ topAverage ?? '—' }}/20</span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-red-50 dark:bg-red-900/20">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Moyenne la plus faible</span>
                                    <span class="text-lg font-black text-red-600 dark:text-red-400">{{ lowAverage ?? '—' }}/20</span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-violet-50 dark:bg-violet-900/20">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Taux de réussite</span>
                                    <span class="text-lg font-black text-violet-600 dark:text-violet-400">{{ successRate ?? '—' }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── TAB : RH ──────────────────────────────────────────── -->
                <div v-show="active === 'hr'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Ressources Humaines</h2>
                        <PeriodFilter v-model="hrPeriod" />
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <KpiCard label="Personnel total"  :value="totalStaff ?? 0"         color="violet" href="/admin/staff/list" icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <KpiCard label="Congés en attente" :value="totalPendingLeaves ?? 0" color="warning" href="/admin/staff/leaves/list" icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        <KpiCard label="Congés approuvés" :value="totalApprovedLeaves ?? 0" color="success" href="/admin/staff/leaves/list" icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <KpiCard label="Événements" :value="totalUpcomingEvents ?? 0" color="info" href="/admin/staff/events/list" icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Personnel par fonction</h3>
                            <ApexDonut :series="staffRoleSeries" :labels="staffRoleLabels" :colors="['#7C3AED','#3B82F6','#10B981','#F59E0B','#EF4444','#06B6D4']" :height="150"/>
                        </div>
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Personnel en congé actuellement</h3>
                            <div v-if="!currentLeaves?.length" class="flex items-center justify-center h-32 text-xs text-gray-400">Aucun congé en cours</div>
                            <div v-else class="space-y-2">
                                <div v-for="leave in (currentLeaves ?? []).slice(0, 6)" :key="leave.id ?? leave.staff_id"
                                    class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-white text-xs font-bold"
                                        :style="{ background: leave.color ?? '#6366f1' }">
                                        {{ ((leave.last_name ?? leave.name ?? '?')[0]).toUpperCase() }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ leave.last_name }} {{ leave.name }}</p>
                                        <p class="text-[10px] text-gray-400">{{ leave.leave_type_name ?? 'Congé' }}</p>
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 font-semibold">En cours</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── TAB : FINANCES ────────────────────────────────────── -->
                <div v-show="active === 'finance'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Contributions & Paiements</h2>
                        <PeriodFilter v-model="financePeriod" />
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <KpiCard label="Total dossiers" :value="totalFeesCollections ?? 0" color="violet" icon="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <KpiCard label="Dossiers aujourd'hui" :value="totalFeesCollectionsToday ?? 0" color="success" icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <KpiCard label="Devoirs / Travaux" :value="totalHomework ?? 0" color="info" icon="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        <KpiCard label="Taux collecte" :value="feesRate + '%'" color="amber" icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </div>
                    <div class="card p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Évolution des contributions</h3>
                        <ApexArea :series="feesAreaSeries" :categories="months" :colors="['#7C3AED','#10B981']" :height="150"/>
                    </div>
                </div>

                <!-- ── TAB : COMMUNICATION ───────────────────────────────── -->
                <div v-show="active === 'comms'" class="space-y-4">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Communication & Tableaux d'affichage</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <KpiCard label="Notices totales"    :value="totalNoticeBoard ?? 0"  color="violet" icon="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        <KpiCard label="Devoirs assignés"   :value="totalHomework ?? 0"     color="info"   icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        <KpiCard label="Travaux pratiques" :value="totalWork ?? 0"           color="success" icon="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        <KpiCard label="Total présences"   :value="totalAttendance ?? 0"    color="warning" icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Prochains événements</h3>
                            <div v-if="!upcomingEvents?.length" class="text-center py-8 text-xs text-gray-400">Aucun événement</div>
                            <div v-else class="space-y-2">
                                <div v-for="ev in (upcomingEvents ?? []).slice(0, 6)" :key="ev.id"
                                    class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex flex-col items-center justify-center text-white font-bold text-xs"
                                        :style="{ background: eventTypeColor(ev.event_type ?? ev.extendedProps?.type) }">
                                        <span class="text-base leading-none">{{ fmtDay(ev.event_date ?? ev.start) }}</span>
                                        <span class="text-[9px] leading-none mt-0.5 uppercase">{{ fmtMonth(ev.event_date ?? ev.start) }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ ev.title }}</p>
                                        <p class="text-[10px] text-gray-400">{{ eventTypeLabel(ev.event_type ?? ev.extendedProps?.type) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Activité communication</h3>
                            <ApexBar :series="commSeries" :categories="months.slice(0, 6)" :colors="['#7C3AED','#3B82F6','#10B981']" :height="150"/>
                        </div>
                    </div>
                </div>

                <!-- ── TAB : CONFIGURATION ───────────────────────────────── -->
                <div v-show="active === 'config'" class="space-y-4">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Configuration système & RBAC</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Rôles & Permissions</h3>
                            <div class="space-y-3">
                                <ProgressBar label="Rôles définis" :value="totalRoles" :max="20" color="violet" />
                                <ProgressBar label="Permissions configurées" :value="totalPermissions" :max="200" color="info" />
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <a href="/superadmin/config/roles" class="block p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-violet-300 dark:hover:border-violet-700 text-center text-xs font-semibold text-violet-600 dark:text-violet-400 transition-colors">
                                    Gérer les rôles
                                </a>
                                <a href="/superadmin/config/permissions" class="block p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-violet-300 dark:hover:border-violet-700 text-center text-xs font-semibold text-violet-600 dark:text-violet-400 transition-colors">
                                    Gérer les permissions
                                </a>
                            </div>
                        </div>
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Accès rapides</h3>
                            <div class="grid grid-cols-1 gap-2">
                                <a v-for="link in quickLinks" :key="link.href" :href="link.href"
                                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-sm transition-all">
                                    <div class="w-8 h-8 rounded-xl bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                        <NavIcon :name="link.icon" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                                    </div>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ link.label }}</span>
                                    <svg class="ml-auto w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </template>
        </DashTabs>

        <!-- ── CALENDRIER ──────────────────────────────────────────────────── -->
        <AppCalendar
            title="Calendrier scolaire"
            subtitle="Cours, événements et activités de l'établissement"
            :course-events="[]"
            :events="calendarEventsFormatted"
        />

    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import AttendanceBadge from '@/Components/Dashboard/AttendanceBadge.vue';
import MiniCard        from '@/Components/Dashboard/MiniCard.vue';
import AlertCard       from '@/Components/Dashboard/AlertCard.vue';
import KpiCard         from '@/Components/Dashboard/KpiCard.vue';
import DashTabs        from '@/Components/Dashboard/DashTabs.vue';
import PeriodFilter    from '@/Components/Dashboard/PeriodFilter.vue';
import ProgressBar     from '@/Components/Dashboard/ProgressBar.vue';
import ApexDonut       from '@/Components/Dashboard/ApexDonut.vue';
import ApexBar         from '@/Components/Dashboard/ApexBar.vue';
import ApexArea        from '@/Components/Dashboard/ApexArea.vue';
import ApexRadial      from '@/Components/Dashboard/ApexRadial.vue';
import NavIcon         from '@/Components/Layout/NavIcon.vue';
import { AppCalendar } from '@/Components/UI';
import type { CalEvent } from '@/Components/UI';

const props = defineProps<{
    totalUser: number; totalAdmin: number; totalTeacher: number; totalStudent: number; totalParent: number;
    totalClass: number; totalSubject: number; totalExam: number; totalFeesCollections: number;
    totalFeesCollectionsToday?: number; totalCommunicate?: number; totalNoticeBoard?: number;
    totalHomework?: number; totalWork?: number; totalAttendance?: number;
    totalAttendanceStudentPresent: number; totalAttendanceStudentLate: number;
    totalAttendanceStudentAbsent: number; totalAttendanceStudentHalfDay: number;
    totalWeek?: number; totalClassTimetable?: number;
    totalRoles: number; totalPermissions: number;
    totalStaff?: number; totalPendingLeaves?: number; totalPendingGrades?: number;
    totalUpcomingEvents?: number; totalApprovedLeaves?: number;
    totalOpenEvals?: number; totalDraftBulletins?: number; totalPublishedBulletins?: number;
    totalSuperAdmin?: number; totalSuperAdminMale?: number; totalSuperAdminFemale?: number;
    totalStudentMale?: number; totalStudentFemale?: number;
    totalTeacherMale?: number; totalTeacherFemale?: number;
    totalParentMale?: number; totalParentFemale?: number;
    totalAdminMale?: number; totalAdminFemale?: number;
    totalSchools?: number; totalDeletionLogs?: number; totalPermissionAssignments?: number;
    totalClassSubject?: number;
    attendanceByMonth?: { present: number[]; late: number[]; absent: number[]; halfday: number[] };
    attendanceBySchool?: { school_id: number; school_name: string; present: number; late: number; absent: number; halfday: number }[];
    schoolsStats?: { school_id: number; school_name: string; total_users: number; total_students: number; total_teachers: number; total_parents: number; total_admins: number; total_staff: number }[];
    topAverage?: number | null; lowAverage?: number | null; successRate?: number | null;
    staffRoleData?: Record<string, number>;
    currentLeaves?: any[]; upcomingEvents?: any[]; calendarEvents?: any[]; currentPeriod?: any;
    [key: string]: unknown;
}>();

const today  = new Date();
const months = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];

const attendancePeriod = ref('month');
const hrPeriod         = ref('month');
const financePeriod    = ref('month');

// ── Helper calcul pourcentage ─────────────────────────────────────────────────
const safePercent = (part: number | undefined | null, total: number | undefined | null): number => {
    if (!part || !total) return 0;
    return Math.round((part / total) * 100);
};

// ── Couleurs des écoles (palette cyclique) ───────────────────────────────────
const schoolPalette = ['#7C3AED','#3B82F6','#10B981','#F59E0B','#EF4444','#06B6D4','#8B5CF6','#EC4899','#14B8A6','#F97316'];
const schoolColor = (id: number) => schoolPalette[id % schoolPalette.length];

const tabs = [
    { key: 'overview',   label: 'Vue générale',       icon: 'chart-bar' },
    { key: 'attendance', label: 'Présences',           icon: 'user-check',   badge: props.totalAttendanceStudentAbsent },
    { key: 'academic',   label: 'Académique',          icon: 'academic-cap', badge: ((props.totalPendingGrades as number ?? 0) + (props.totalDraftBulletins ?? 0)) || undefined },
    { key: 'hr',         label: 'Ressources humaines', icon: 'user-group',   badge: props.totalPendingLeaves },
    { key: 'finance',    label: 'Contributions',       icon: 'banknotes' },
    { key: 'comms',      label: 'Communication',       icon: 'megaphone' },
    { key: 'config',     label: 'Configuration',       icon: 'cog-6-tooth' },
];

// ── Super Admin count — toujours depuis le serveur (user_type=0) ──────────────
const totalSuperAdmin = computed(() => props.totalSuperAdmin ?? 0);

// ── Attendance ────────────────────────────────────────────────────────────────
const attendanceSeries = computed(() => {
    const att = props.attendanceByMonth;
    if (att && att.present?.length === 12) {
        return [
            { name: 'Présents',     data: att.present },
            { name: 'Retards',      data: att.late },
            { name: 'Absents',      data: att.absent },
            { name: 'Demi-journée', data: att.halfday },
        ];
    }
    return [
        { name: 'Présents',     data: Array(12).fill(props.totalAttendanceStudentPresent) },
        { name: 'Retards',      data: Array(12).fill(props.totalAttendanceStudentLate) },
        { name: 'Absents',      data: Array(12).fill(props.totalAttendanceStudentAbsent) },
        { name: 'Demi-journée', data: Array(12).fill(props.totalAttendanceStudentHalfDay) },
    ];
});
const totalAtt = computed(() =>
    props.totalAttendanceStudentPresent + props.totalAttendanceStudentLate +
    props.totalAttendanceStudentAbsent + props.totalAttendanceStudentHalfDay || 1
);
const attendanceRadial = computed(() => [
    Math.round(props.totalAttendanceStudentPresent  / totalAtt.value * 100),
    Math.round(props.totalAttendanceStudentLate     / totalAtt.value * 100),
    Math.round(props.totalAttendanceStudentAbsent   / totalAtt.value * 100),
    Math.round(props.totalAttendanceStudentHalfDay  / totalAtt.value * 100),
]);

// ── Évals ─────────────────────────────────────────────────────────────────────
const evalTypeSeries = [{ name: 'Évaluations', data: [12, 8, 15, 5] }];

// ── Finance ───────────────────────────────────────────────────────────────────
const feesRate = computed(() => {
    const total = props.totalStudent || 1;
    return Math.round(Math.min(100, (props.totalFeesCollections / total) * 100));
});
const feesAreaSeries = [
    { name: 'Contributions', data: [40, 55, 70, 80, 60, 75, 90, 85, 70, 95, 100, 88] },
    { name: 'Objectif',      data: [50, 60, 65, 75, 70, 80, 85, 90, 85, 95, 100, 100] },
];

// ── Staff rôles ───────────────────────────────────────────────────────────────
const staffRoleSeries = computed(() => Object.values(props.staffRoleData ?? { Professeurs: props.totalTeacher, Personnel: props.totalStaff ?? 0 }));
const staffRoleLabels = computed(() => Object.keys(props.staffRoleData ?? { Professeurs: 'Professeurs', Personnel: 'Personnel' }));

// ── Comms ─────────────────────────────────────────────────────────────────────
const commSeries = [
    { name: 'Notices',     data: [5, 7, 3, 9, 6, 8] },
    { name: 'Devoirs',     data: [8, 12, 10, 14, 9, 11] },
    { name: 'Travaux',     data: [3, 5, 4, 6, 5, 7] },
];

// ── Quick links ───────────────────────────────────────────────────────────────
const quickLinks = [
    { href: '/superadmin/config/roles',       label: 'Gestion des rôles',       icon: 'shield-check' },
    { href: '/superadmin/config/permissions', label: 'Gestion des permissions', icon: 'key' },
    { href: '/superadmin/config/assign',      label: 'Attribution permissions', icon: 'user' },
    { href: '/superadmin/users',              label: 'Tous les utilisateurs',   icon: 'users' },
    { href: '/superadmin/deletion-logs',      label: 'Journaux de suppression', icon: 'document-text' },
    { href: '/superadmin/config/settings',    label: 'Paramètres système',      icon: 'cog-6-tooth' },
];

// ── Calendar ──────────────────────────────────────────────────────────────────
const typeColors: Record<string, string> = {
    academic: '#3b82f6', cultural: '#8b5cf6', administrative: '#f59e0b',
    exam: '#ef4444', ceremony: '#10b981', trip: '#06b6d4',
};
const typeLabels: Record<string, string> = {
    academic: 'Académique', cultural: 'Culturel', administrative: 'Administratif',
    exam: 'Examen', ceremony: 'Cérémonie', trip: 'Sortie',
};
const calendarEventsFormatted = computed<CalEvent[]>(() => {
    if (props.calendarEvents?.length) {
        return (props.calendarEvents as any[]).map(ev => ({
            id: ev.id, title: ev.title, start: ev.start ?? ev.event_date,
            color: ev.color ?? typeColors[ev.extendedProps?.type ?? ev.event_type] ?? '#7B74F0',
            start_time: ev.start_time ?? '', end_time: ev.end_time ?? '',
            extendedProps: {
                type_label: typeLabels[ev.extendedProps?.type ?? ev.event_type] ?? 'Événement',
                description: ev.description ?? '', location: ev.location ?? '',
                start_time: ev.start_time ?? '', end_time: ev.end_time ?? '',
            },
        }));
    }
    const m = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');
    return [
        { id: 1, title: 'Conseil de direction',   start: `${m}-05`, color: '#7C3AED', start_time: '09:00', end_time: '11:00', extendedProps: { type_label: 'Admin', location: 'Salle 1' } },
        { id: 2, title: 'Audit permissions RBAC', start: `${m}-12`, color: '#3B82F6', start_time: '14:00', end_time: '16:00', extendedProps: { type_label: 'Config', location: 'Serveur' } },
    ] as CalEvent[];
});

const fmtDay   = (d: string) => d ? new Date(d).getDate() : '';
const fmtMonth = (d: string) => d ? months[new Date(d).getMonth()] : '';
const eventTypeColor = (type: string) => typeColors[type] ?? '#6366f1';
const eventTypeLabel = (type: string) => typeLabels[type] ?? type ?? '—';
</script>
