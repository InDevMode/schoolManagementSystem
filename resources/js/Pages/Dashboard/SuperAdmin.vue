<template>
    <div class="space-y-5">

        <!-- ══ HEADER ═══════════════════════════════════════════════════════ -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Vue d'ensemble système</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ today.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}
                    <span v-if="currentPeriod" class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-violet-50 dark:bg-violet-900/20 text-violet-600 dark:text-violet-400 text-xs font-semibold">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ currentPeriod.name }}
                    </span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-600 text-white text-xs font-semibold shadow">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse inline-block"/>
                    Super Admin
                </span>
            </div>
        </div>

        <!-- ══ KPI ROW — Utilisateurs ════════════════════════════════════════ -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <KpiCard label="Apprenants"    :value="totalStudent"  color="violet" href="/admin/student/list"
                trend="+12%" :trendPositive="true"
                icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"
            />
            <KpiCard label="Professeurs"   :value="totalTeacher" color="info"   href="/admin/teacher/list"
                trend="+3%" :trendPositive="true"
                icon="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"
            />
            <KpiCard label="Parents"       :value="totalParent"  color="amber"  href="/admin/parent/list"
                trend="+8%" :trendPositive="true"
                icon="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
            />
            <KpiCard label="Personnel actif" :value="totalStaff ?? 0" color="success" href="/admin/staff/list"
                sub="Staff RH actif"
                icon="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
            />
        </div>

        <!-- ══ TABS ══════════════════════════════════════════════════════════ -->
        <DashTabs :tabs="tabs">
            <template #default="{ active }">

                <!-- ── TAB : VUE GÉNÉRALE ──────────────────────────────────── -->
                <div v-show="active === 'overview'" class="space-y-4">

                    <!-- Ligne 1 : stats académiques -->
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-2.5">
                        <MiniCard label="Administrateurs" :value="totalAdmin"   icon="shield"                  color="slate"   href="/admin/admin/list"/>
                        <MiniCard label="Classes"         :value="totalClass"   icon="building-library"        color="sky"     href="/admin/class/list"/>
                        <MiniCard label="Matières"        :value="totalSubject" icon="book-open"               color="teal"    href="/admin/subject/list"/>
                        <MiniCard label="Périodes exam"   :value="totalExam"    icon="clipboard-document-list" color="orange"  href="/admin/examinations/period/list"/>
                        <MiniCard label="Devoirs"         :value="totalHomework" icon="pencil"                 color="rose"    href="/admin/practicalworks/homework/list"/>
                    </div>

                    <!-- Ligne 2 : RBAC -->
                    <div class="grid grid-cols-2 gap-2.5">
                        <MiniCard label="Rôles système"       :value="totalRoles"       icon="shield-check" color="violet" href="/superadmin/config/roles"/>
                        <MiniCard label="Permissions système" :value="totalPermissions" icon="key"          color="blue"   href="/superadmin/config/permissions"/>
                    </div>

                    <!-- Ligne 3 : Alertes + Évals -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <AlertCard label="Congés en attente"  :value="totalPendingLeaves ?? 0"  icon="calendar-days" variant="warning" href="/admin/staff/leaves/list"/>
                        <AlertCard label="Notes à valider"    :value="totalPendingGrades ?? 0"  icon="check-badge"   variant="danger"  href="/admin/evaluations/grades/pending"/>
                        <AlertCard label="Bulletins brouillon":value="totalDraftBulletins ?? 0" icon="document-text" variant="info"    href="/admin/bulletins/list"/>
                        <AlertCard label="Événements à venir" :value="totalUpcomingEvents ?? 0" icon="sparkles"      variant="default" href="/admin/staff/events/list"/>
                    </div>

                    <!-- Ligne 4 : Charts — Répartition utilisateurs + contributions -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                        <!-- Donut utilisateurs -->
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Répartition utilisateurs</h3>
                            <p class="text-xs text-gray-400 mb-3">Par rôle</p>
                            <ApexDonut
                                :series="[totalStudent, totalTeacher, totalParent, totalAdmin, totalStaff ?? 0]"
                                :labels="['Apprenants', 'Professeurs', 'Parents', 'Admins', 'Personnel']"
                                :colors="['#7C3AED', '#3B82F6', '#F59E0B', '#EF4444', '#10B981']"
                                center-label="Utilisateurs"
                                :center-value="totalUser"
                                :height="220"
                            />
                        </div>

                        <!-- Donut sexe -->
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Apprenants par sexe</h3>
                            <p class="text-xs text-gray-400 mb-3">Répartition garçons / filles</p>
                            <ApexDonut
                                :series="[totalStudentMale ?? 0, totalStudentFemale ?? 0]"
                                :labels="['Garçons', 'Filles']"
                                :colors="['#7C3AED', '#F472B6']"
                                center-label="Total"
                                :center-value="totalStudent"
                                :height="220"
                            />
                        </div>

                        <!-- Contributions -->
                        <div class="card p-5 flex flex-col">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Contributions</h3>
                            <p class="text-xs text-gray-400 mb-4">Paiements collectés</p>
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
                            <a href="/admin/feescollections/collections/list" class="mt-4 text-center text-xs font-medium text-primary-600 dark:text-primary-400 hover:underline block">
                                Voir les contributions →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ── TAB : PRÉSENCES ───────────────────────────────────── -->
                <div v-show="active === 'attendance'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Statistiques de présence</h2>
                        <PeriodFilter v-model="attendancePeriod" />
                    </div>

                    <!-- Badges résumé -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <AttendanceBadge label="Présents"     :value="totalAttendanceStudentPresent"  color="success" icon="user-check"/>
                        <AttendanceBadge label="En retard"    :value="totalAttendanceStudentLate"     color="warning" icon="clock"/>
                        <AttendanceBadge label="Absents"      :value="totalAttendanceStudentAbsent"   color="danger"  icon="user-minus"/>
                        <AttendanceBadge label="Demi-journée" :value="totalAttendanceStudentHalfDay"  color="info"    icon="calendar-days"/>
                    </div>

                    <!-- Chart barres présences mensuel -->
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Évolution des présences</h3>
                        <ApexBar
                            :series="attendanceSeries"
                            :categories="months"
                            :colors="['#10B981','#F59E0B','#EF4444','#3B82F6']"
                            :height="260"
                        />
                    </div>

                    <!-- Radial chart taux -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Taux par type</h3>
                            <ApexRadial
                                :series="attendanceRadial"
                                :labels="['Présents','En retard','Absents','Demi-j.']"
                                :colors="['#10B981','#F59E0B','#EF4444','#3B82F6']"
                                :height="240"
                            />
                        </div>
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Progression par mois</h3>
                            <div class="space-y-3 mt-2">
                                <ProgressBar v-for="(m, i) in months.slice(0, 6)" :key="m"
                                    :label="m" :value="attendanceMonthData[i] ?? 0" :max="totalStudent || 1"
                                    :color="['success','info','violet','warning','primary','amber'][i % 6] as any"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── TAB : ACADÉMIQUE ──────────────────────────────────── -->
                <div v-show="active === 'academic'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Sessions & Évaluations</h2>
                    </div>

                    <!-- Sessions exam stats -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <KpiCard label="Sessions actives"    :value="totalExam"           color="violet" icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        <KpiCard label="Évals ouvertes"      :value="totalOpenEvals ?? 0" color="info"   icon="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        <KpiCard label="Notes à valider"     :value="totalPendingGrades ?? 0" color="warning" icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <KpiCard label="Bulletins brouillon" :value="totalDraftBulletins ?? 0" color="danger"  icon="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </div>

                    <!-- Barres évals par type -->
                    <div class="card p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Évaluations par type</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Répartition des évaluations de l'année</p>
                            </div>
                        </div>
                        <ApexBar
                            :series="evalTypeSeries"
                            :categories="['Interro', 'Devoir', 'Travail maison', 'Examen blanc']"
                            :colors="['#7C3AED','#3B82F6','#10B981','#F59E0B']"
                            :height="220"
                        />
                    </div>

                    <!-- Sessions académiques + moyennes -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Sessions académiques</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/40">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Période courante</span>
                                    <span class="text-sm font-bold text-violet-600 dark:text-violet-400">{{ currentPeriod?.name ?? '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/40">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Année scolaire</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ currentPeriod?.school_year ?? '—' }}</span>
                                </div>
                                <ProgressBar label="Bulletins publiés" :value="totalPublishedBulletins ?? 0" :max="totalStudent || 1" color="success" />
                                <ProgressBar label="Bulletins brouillon" :value="totalDraftBulletins ?? 0" :max="totalStudent || 1" color="warning" />
                            </div>
                        </div>
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Moyennes remarquables</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Meilleure moyenne</span>
                                    <span class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ topAverage ?? '—' }}/20</span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-lg bg-red-50 dark:bg-red-900/20">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Moyenne la plus faible</span>
                                    <span class="text-lg font-black text-red-600 dark:text-red-400">{{ lowAverage ?? '—' }}/20</span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-lg bg-violet-50 dark:bg-violet-900/20">
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

                    <!-- Personnel par rôle RH -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Personnel par fonction</h3>
                            <ApexDonut
                                :series="staffRoleSeries"
                                :labels="staffRoleLabels"
                                :colors="['#7C3AED','#3B82F6','#10B981','#F59E0B','#EF4444','#06B6D4']"
                                :height="220"
                            />
                        </div>
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Personnel en congé actuellement</h3>
                            <div v-if="!currentLeaves?.length" class="flex items-center justify-center h-32 text-xs text-gray-400">
                                Aucun congé en cours
                            </div>
                            <div v-else class="space-y-2">
                                <div v-for="leave in (currentLeaves ?? []).slice(0, 6)" :key="leave.id ?? leave.staff_id"
                                    class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/30">
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

                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Évolution des contributions</h3>
                        <ApexArea
                            :series="feesAreaSeries"
                            :categories="months"
                            :colors="['#7C3AED','#10B981']"
                            :height="240"
                        />
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
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Prochains événements</h3>
                            <div v-if="!upcomingEvents?.length" class="text-center py-8 text-xs text-gray-400">Aucun événement</div>
                            <div v-else class="space-y-2">
                                <div v-for="ev in (upcomingEvents ?? []).slice(0, 6)" :key="ev.id"
                                    class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/30">
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
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Activité communication</h3>
                            <ApexBar
                                :series="commSeries"
                                :categories="months.slice(0, 6)"
                                :colors="['#7C3AED','#3B82F6','#10B981']"
                                :height="200"
                            />
                        </div>
                    </div>
                </div>

                <!-- ── TAB : CONFIGURATION ───────────────────────────────── -->
                <div v-show="active === 'config'" class="space-y-4">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Configuration système & RBAC</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Rôles & Permissions</h3>
                            <div class="space-y-3">
                                <ProgressBar label="Rôles définis" :value="totalRoles" :max="20" color="violet" />
                                <ProgressBar label="Permissions configurées" :value="totalPermissions" :max="100" color="info" />
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <a href="/superadmin/config/roles" class="block p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:border-violet-300 dark:hover:border-violet-700 text-center text-xs font-semibold text-violet-600 dark:text-violet-400 transition-colors">
                                    Gérer les rôles
                                </a>
                                <a href="/superadmin/config/permissions" class="block p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:border-violet-300 dark:hover:border-violet-700 text-center text-xs font-semibold text-violet-600 dark:text-violet-400 transition-colors">
                                    Gérer les permissions
                                </a>
                            </div>
                        </div>
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Accès rapides</h3>
                            <div class="grid grid-cols-1 gap-2">
                                <a v-for="link in quickLinks" :key="link.href" :href="link.href"
                                    class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-sm transition-all">
                                    <div class="w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
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
import { useDark } from '@vueuse/core';
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

const isDark = useDark();

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
    totalStudentMale?: number; totalStudentFemale?: number;
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

const tabs = [
    { key: 'overview',   label: 'Vue générale',   icon: 'chart-bar' },
    { key: 'attendance', label: 'Présences',       icon: 'user-check',  badge: props.totalAttendanceStudentAbsent },
    { key: 'academic',   label: 'Académique',      icon: 'academic-cap', badge: (props.totalPendingGrades ?? 0) + (props.totalDraftBulletins ?? 0) },
    { key: 'hr',         label: 'RH',              icon: 'user-group',  badge: props.totalPendingLeaves },
    { key: 'finance',    label: 'Contributions',   icon: 'banknotes' },
    { key: 'comms',      label: 'Communication',   icon: 'megaphone' },
    { key: 'config',     label: 'Configuration',   icon: 'cog-6-tooth' },
];

// ── Attendance ────────────────────────────────────────────────────────────────
const attendanceMonthData = [
    props.totalAttendanceStudentPresent, Math.round(props.totalAttendanceStudentPresent * 0.95),
    Math.round(props.totalAttendanceStudentPresent * 0.88), Math.round(props.totalAttendanceStudentPresent * 0.92),
    Math.round(props.totalAttendanceStudentPresent * 0.97), Math.round(props.totalAttendanceStudentPresent * 0.9),
];
const attendanceSeries = [
    { name: 'Présents',     data: [props.totalAttendanceStudentPresent, ...Array(11).fill(0).map((_,i) => Math.max(0, props.totalAttendanceStudentPresent - i * 3))] },
    { name: 'Retards',      data: [props.totalAttendanceStudentLate,    ...Array(11).fill(0).map((_,i) => Math.max(0, props.totalAttendanceStudentLate + i * 1))] },
    { name: 'Absents',      data: [props.totalAttendanceStudentAbsent,  ...Array(11).fill(0).map((_,i) => Math.max(0, props.totalAttendanceStudentAbsent + i * 2))] },
    { name: 'Demi-journée', data: [props.totalAttendanceStudentHalfDay, ...Array(11).fill(0).map((_,i) => Math.max(0, props.totalAttendanceStudentHalfDay - i * 1))] },
];
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
const evalTypeSeries = [
    { name: 'Évaluations', data: [12, 8, 15, 5] },
];

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
