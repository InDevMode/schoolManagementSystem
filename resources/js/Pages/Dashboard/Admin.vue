<template>
    <div class="space-y-5">

        <!-- ══ HEADER ═══════════════════════════════════════════════════════ -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Tableau de bord administrateur</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2 flex-wrap">
                    <span>{{ today.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
                    <span v-if="currentPeriod" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-violet-50 dark:bg-violet-900/20 text-violet-600 dark:text-violet-400 text-xs font-semibold border border-violet-100 dark:border-violet-800">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ currentPeriod.name }}
                    </span>
                </p>
            </div>
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold shadow-lg shadow-blue-500/20 w-fit self-start"
                  style="background: linear-gradient(135deg, #3B82F6, #60A5FA);">
                <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse inline-block flex-shrink-0"/>
                <span class="text-white">Administrateur</span>
            </span>
        </div>

        <!-- ══ TABS ══════════════════════════════════════════════════════════ -->
        <DashTabs :tabs="tabs">
            <template #default="{ active }">

                <!-- ── VUE GÉNÉRALE ───────────────────────────────────────── -->
                <div v-show="active === 'overview'" class="space-y-4">

                    <!-- KPI utilisateurs avec breakdown H/F -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                        <KpiCard label="Apprenants"      :value="totalStudent"         color="violet" href="/admin/student/list"
                            trend="+12%" :trendPositive="true"
                            :genderMale="totalStudentMale" :genderFemale="totalStudentFemale"
                            icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <KpiCard label="Professeurs"     :value="totalTeacher"         color="info"   href="/admin/teacher/list"
                            trend="+3%" :trendPositive="true"
                            :genderMale="totalTeacherMale" :genderFemale="totalTeacherFemale"
                            icon="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        <KpiCard label="Parents"         :value="totalParent"          color="amber"  href="/admin/parent/list"
                            trend="+8%" :trendPositive="true"
                            :genderMale="totalParentMale" :genderFemale="totalParentFemale"
                            icon="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        <KpiCard label="Administrateurs" :value="totalAdmin"           color="danger" href="/admin/admin/list"
                            :genderMale="totalAdminMale" :genderFemale="totalAdminFemale"
                            icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        <KpiCard label="Contributions"   :value="totalFeesCollections" color="success" href="/admin/feescollections/collections/list"
                            sub="Dossiers enregistrés"
                            icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </div>

                    <!-- Ligne stats académiques -->
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2 px-0.5">Académique</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                            <MiniCard label="Classes"         :value="totalClass"    icon="building-library"        color="sky"    href="/admin/class/list"/>
                            <MiniCard label="Matières"        :value="totalSubject"  icon="book-open"               color="teal"   href="/admin/subject/list"/>
                            <MiniCard label="Sessions examen" :value="totalExam"     icon="clipboard-document-list" color="orange" href="/admin/examinations/period/list"/>
                            <MiniCard label="Devoirs"         :value="totalHomework" icon="pencil"                  color="rose"   href="/admin/practicalworks/homework/list"/>
                            <MiniCard label="Travaux"         :value="totalWork ?? 0" icon="document-text"          color="violet" href="/admin/practicalworks/list"/>
                        </div>
                    </div>

                    <!-- Alertes & actions -->
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2 px-0.5">Alertes & actions</h3>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
                            <AlertCard label="Personnel actif"     :value="totalStaff ?? 0"          icon="user-group"    variant="default" href="/admin/staff/list"/>
                            <AlertCard label="Congés en attente"   :value="totalPendingLeaves ?? 0"  icon="calendar-days" variant="warning" href="/admin/staff/leaves/list"/>
                            <AlertCard label="Évals à valider"     :value="totalOpenEvals ?? 0"      icon="pencil-square" variant="danger"  href="/admin/evaluations/grades/pending"/>
                            <AlertCard label="Bulletins brouillon" :value="totalDraftBulletins ?? 0" icon="document-text" variant="info"    href="/admin/bulletins/list"/>
                        </div>
                    </div>

                    <!-- Présences du jour -->
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2 px-0.5">Présences du jour</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            <AttendanceBadge label="Présents"     :value="totalAttendanceStudentPresent"  color="success" icon="user-check"/>
                            <AttendanceBadge label="En retard"    :value="totalAttendanceStudentLate"     color="warning" icon="clock"/>
                            <AttendanceBadge label="Absents"      :value="totalAttendanceStudentAbsent"   color="danger"  icon="user-minus"/>
                            <AttendanceBadge label="Demi-journée" :value="totalAttendanceStudentHalfDay"  color="info"    icon="calendar-days"/>
                        </div>
                    </div>

                    <!-- Charts : sexe + répartition -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
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
                        <div class="lg:col-span-2 card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Répartition utilisateurs</h3>
                            <ApexBar
                                :series="userDistSeries"
                                :categories="['Apprenants','Professeurs','Parents','Admins']"
                                :colors="['#7C3AED','#3B82F6','#F59E0B','#EF4444']"
                                :height="150"
                                horizontal
                            />
                        </div>
                    </div>

                    <!-- Événements + congés -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        <div class="card p-0 overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Prochains événements</h3>
                                <a href="/admin/staff/events/list" class="text-xs text-primary-600 dark:text-primary-400 hover:underline font-medium">Voir tout →</a>
                            </div>
                            <div v-if="!upcomingEvents?.length" class="px-4 py-8 text-center text-xs text-gray-400">Aucun événement à venir</div>
                            <div v-else class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                <div v-for="ev in (upcomingEvents ?? []).slice(0,5)" :key="ev.id"
                                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex flex-col items-center justify-center text-white font-bold text-xs shadow-sm"
                                        :style="{ background: typeColors[ev.event_type ?? ev.extendedProps?.type] ?? '#7B74F0' }">
                                        <span class="text-base leading-none">{{ fmtDay(ev.event_date ?? ev.start) }}</span>
                                        <span class="text-[9px] uppercase">{{ fmtMonth(ev.event_date ?? ev.start) }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ ev.title }}</p>
                                        <p class="text-[10px] text-gray-400">{{ typeLabels[ev.event_type ?? ev.extendedProps?.type] ?? 'Événement' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card p-0 overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Personnel en congé</h3>
                                <a href="/admin/staff/leaves/list" class="text-xs text-primary-600 dark:text-primary-400 hover:underline font-medium">Gérer →</a>
                            </div>
                            <div v-if="!currentLeaves?.length" class="px-4 py-8 text-center text-xs text-gray-400">Aucun congé en cours</div>
                            <div v-else class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                <div v-for="leave in (currentLeaves ?? []).slice(0,5)" :key="leave.id"
                                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-white text-xs font-bold shadow-sm"
                                        :style="{ background: leave.color ?? '#7B74F0' }">
                                        {{ ((leave.last_name ?? leave.name ?? '?')[0]).toUpperCase() }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ leave.last_name }} {{ leave.name }}</p>
                                        <p class="text-[10px] text-gray-400">{{ leave.leave_type_name ?? 'Congé' }}</p>
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 font-semibold border border-amber-100 dark:border-amber-800">En cours</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── PRÉSENCES ───────────────────────────────────────────── -->
                <div v-show="active === 'attendance'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Statistiques de présence — Mon école</h2>
                        <PeriodFilter v-model="attendancePeriod" />
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <AttendanceBadge label="Présents"     :value="totalAttendanceStudentPresent"  color="success" icon="user-check"/>
                        <AttendanceBadge label="En retard"    :value="totalAttendanceStudentLate"     color="warning" icon="clock"/>
                        <AttendanceBadge label="Absents"      :value="totalAttendanceStudentAbsent"   color="danger"  icon="user-minus"/>
                        <AttendanceBadge label="Demi-journée" :value="totalAttendanceStudentHalfDay"  color="info"    icon="calendar-days"/>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Évolution mensuelle</h3>
                            <div class="overflow-x-auto">
                                <div style="min-width: 300px;">
                                    <ApexArea :series="attendanceSeries" :categories="months" :colors="['#10B981','#F59E0B','#EF4444','#3B82F6']" :height="150"/>
                                </div>
                            </div>
                        </div>
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Taux de présence</h3>
                            <ApexRadial :series="attendanceRadial" :labels="['Présents','En retard','Absents','Demi-j.']" :colors="['#10B981','#F59E0B','#EF4444','#3B82F6']"/>
                        </div>
                    </div>

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

                <!-- ── ACADÉMIQUE ──────────────────────────────────────────── -->
                <div v-show="active === 'academic'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Évaluations & Bulletins</h2>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <KpiCard label="Sessions examen"    :value="totalExam"             color="violet" href="/admin/examinations/period/list" icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        <KpiCard label="Évals ouvertes"     :value="totalOpenEvals ?? 0"  color="info"   href="/admin/evaluations/list" icon="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        <KpiCard label="Notes à valider"    :value="totalPendingGrades ?? 0" color="warning" href="/admin/evaluations/grades/pending" icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <KpiCard label="Bulletins brouillon":value="totalDraftBulletins ?? 0" color="danger" href="/admin/bulletins/list" icon="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Statuts évaluations</h3>
                            <ApexDonut
                                :series="[totalOpenEvals ?? 2, totalPendingGrades ?? 3, totalDraftBulletins ?? 1, 5]"
                                :labels="['Ouvertes','À valider','Brouillon','Validées']"
                                :colors="['#3B82F6','#F59E0B','#7C3AED','#10B981']"
                                :height="150"
                            />
                        </div>
                        <div class="lg:col-span-2 card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Évaluations par mois</h3>
                            <ApexBar
                                :series="evalMonthSeries"
                                :categories="months"
                                :colors="['#7C3AED','#3B82F6']"
                                :height="150"
                                stacked
                            />
                        </div>
                    </div>

                    <div class="card p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Progression bulletins</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <ProgressBar label="Bulletins générés"    :value="totalDraftBulletins ?? 0"     :max="totalStudent || 1" color="violet" />
                            <ProgressBar label="Bulletins publiés"    :value="totalPublishedBulletins ?? 0" :max="totalStudent || 1" color="success" />
                            <ProgressBar label="Devoirs assignés"     :value="totalHomework ?? 0"           :max="totalStudent || 1" color="warning" />
                        </div>
                    </div>
                </div>

                <!-- ── RH ──────────────────────────────────────────────────── -->
                <div v-show="active === 'hr'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Ressources Humaines</h2>
                        <PeriodFilter v-model="hrPeriod" />
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <KpiCard label="Personnel actif"  :value="totalStaff ?? 0"          color="violet" href="/admin/staff/list" icon="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        <KpiCard label="Congés en attente" :value="totalPendingLeaves ?? 0" color="warning" href="/admin/staff/leaves/list" icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        <KpiCard label="Événements prévus" :value="totalUpcomingEvents ?? 0" color="info" href="/admin/staff/events/list" icon="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        <KpiCard label="Total professeurs" :value="totalTeacher" color="success" href="/admin/teacher/list" icon="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Congés en cours</h3>
                            <div v-if="!currentLeaves?.length" class="flex items-center justify-center h-24 text-xs text-gray-400">Aucun congé en cours</div>
                            <div v-else class="space-y-2">
                                <div v-for="leave in currentLeaves.slice(0,6)" :key="leave.id"
                                    class="flex items-center gap-3 p-2 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-white text-xs font-bold"
                                        :style="{ background: leave.color ?? '#6366f1' }">
                                        {{ ((leave.last_name ?? leave.name ?? '?')[0]).toUpperCase() }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold truncate">{{ leave.last_name }} {{ leave.name }}</p>
                                        <p class="text-[10px] text-gray-400">{{ leave.leave_type_name ?? 'Congé' }} · {{ leave.role ?? '' }}</p>
                                    </div>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-700 font-semibold">En cours</span>
                                </div>
                            </div>
                        </div>
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Personnel vs Apprenants</h3>
                            <ApexDonut
                                :series="[totalTeacher, totalStaff ?? 0, totalAdmin]"
                                :labels="['Professeurs','Personnel','Admins']"
                                :colors="['#3B82F6','#10B981','#EF4444']"
                                :height="150"
                            />
                        </div>
                    </div>
                </div>

                <!-- ── CONTRIBUTIONS ───────────────────────────────────────── -->
                <div v-show="active === 'finance'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Contributions scolaires</h2>
                        <PeriodFilter v-model="financePeriod" />
                    </div>

                    <!-- KPI principaux -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <KpiCard label="Dossiers payés"
                            :value="feesStats?.countPaid ?? totalFeesCollections"
                            color="success"
                            icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <KpiCard label="En attente"
                            :value="feesStats?.countPending ?? 0"
                            color="amber"
                            icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <KpiCard label="Non payés"
                            :value="feesStats?.countUnpaid ?? 0"
                            color="danger"
                            icon="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <KpiCard label="Taux de collecte"
                            :value="feesRate + '%'"
                            color="violet"
                            icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z"/>
                    </div>

                    <!-- Montants -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3" v-if="feesStats">
                        <div class="card p-4 flex flex-col gap-1">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Montant total attendu</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">{{ fmtAmount(feesStats.totalAmount) }} <span class="text-xs font-normal text-gray-400">FCFA</span></span>
                        </div>
                        <div class="card p-4 flex flex-col gap-1">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Montant collecté</span>
                            <span class="text-xl font-bold text-green-600 dark:text-green-400">{{ fmtAmount(feesStats.paidAmount) }} <span class="text-xs font-normal text-gray-400">FCFA</span></span>
                        </div>
                        <div class="card p-4 flex flex-col gap-1">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Reste à collecter</span>
                            <span class="text-xl font-bold text-amber-600 dark:text-amber-400">{{ fmtAmount(feesStats.remainingAmount) }} <span class="text-xs font-normal text-gray-400">FCFA</span></span>
                        </div>
                    </div>

                    <!-- Graphiques : évolution mensuelle + donut modes -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Évolution des contributions (12 mois)</h3>
                            <ApexArea :series="feesAreaSeries" :categories="months" :colors="['#7C3AED','#10B981']" :height="180"/>
                        </div>
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Répartition par mode de paiement</h3>
                            <p class="text-xs text-gray-400 mb-3">Tous les modes utilisés dans cet établissement</p>
                            <div v-if="feesPaymentTypeSeries.length > 0">
                                <ApexDonut
                                    :series="feesPaymentTypeSeries"
                                    :labels="feesPaymentTypeLabels"
                                    :colors="feesPaymentTypeColors"
                                    :height="210"/>
                            </div>
                            <div v-else class="flex items-center justify-center h-28 text-xs text-gray-400">
                                Aucune donnée de mode de paiement disponible
                            </div>
                        </div>
                    </div>

                    <!-- Évolution mensuelle par mode -->
                    <div class="card p-4" v-if="feesMonthlyByTypeSeries.length > 0">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Évolution mensuelle par mode de paiement</h3>
                        <p class="text-xs text-gray-400 mb-3">Nombre de dossiers par mode sur les 12 derniers mois</p>
                        <ApexBar
                            :series="feesMonthlyByTypeSeries"
                            :categories="months"
                            :colors="feesPaymentTypeColors"
                            :height="200"
                            :stacked="true"/>
                    </div>

                    <!-- Tableau récapitulatif par classe -->
                    <div class="card p-4" v-if="feesStats?.byClass?.length">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Récapitulatif par classe</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <th class="text-left py-2 px-3 text-gray-500 dark:text-gray-400 font-medium">Classe</th>
                                        <th class="text-right py-2 px-3 text-gray-500 dark:text-gray-400 font-medium">Total</th>
                                        <th class="text-right py-2 px-3 text-gray-500 dark:text-gray-400 font-medium">Payés</th>
                                        <th class="text-right py-2 px-3 text-gray-500 dark:text-gray-400 font-medium">En attente</th>
                                        <th class="text-right py-2 px-3 text-gray-500 dark:text-gray-400 font-medium">Collecté</th>
                                        <th class="text-right py-2 px-3 text-gray-500 dark:text-gray-400 font-medium">Restant</th>
                                        <th class="text-right py-2 px-3 text-gray-500 dark:text-gray-400 font-medium">Taux</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(cls, i) in feesStats.byClass" :key="cls.class_id"
                                        class="border-b border-gray-50 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                                        <td class="py-2.5 px-3">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full flex-shrink-0"
                                                    :style="{ background: feesPaymentTypeColors[i % feesPaymentTypeColors.length] }"/>
                                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ cls.class_name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-right py-2.5 px-3 text-gray-600 dark:text-gray-300">{{ cls.total }}</td>
                                        <td class="text-right py-2.5 px-3">
                                            <span class="text-green-600 dark:text-green-400 font-semibold">{{ cls.paid_count }}</span>
                                        </td>
                                        <td class="text-right py-2.5 px-3">
                                            <span class="text-amber-500 font-semibold">{{ cls.pending_count }}</span>
                                        </td>
                                        <td class="text-right py-2.5 px-3 text-green-600 dark:text-green-400 font-semibold text-xs">
                                            {{ fmtAmount(cls.paid_amount) }}
                                        </td>
                                        <td class="text-right py-2.5 px-3 text-amber-600 dark:text-amber-400 text-xs">
                                            {{ fmtAmount(cls.remaining_amount) }}
                                        </td>
                                        <td class="text-right py-2.5 px-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                                :class="cls.total_amount > 0 && Math.round(cls.paid_amount / cls.total_amount * 100) >= 75
                                                    ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                                                    : cls.total_amount > 0 && Math.round(cls.paid_amount / cls.total_amount * 100) >= 40
                                                    ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'
                                                    : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'">
                                                {{ cls.total_amount > 0 ? Math.round(cls.paid_amount / cls.total_amount * 100) : 0 }}%
                                            </span>
                                        </td>
                                    </tr>
                                    <!-- Ligne totaux -->
                                    <tr class="bg-gray-50 dark:bg-gray-800/60 font-semibold">
                                        <td class="py-2.5 px-3 text-gray-700 dark:text-gray-200">Total</td>
                                        <td class="text-right py-2.5 px-3 text-gray-700 dark:text-gray-200">
                                            {{ feesStats.byClass.reduce((s, c) => s + c.total, 0) }}
                                        </td>
                                        <td class="text-right py-2.5 px-3 text-green-600 dark:text-green-400">
                                            {{ feesStats.byClass.reduce((s, c) => s + c.paid_count, 0) }}
                                        </td>
                                        <td class="text-right py-2.5 px-3 text-amber-500">
                                            {{ feesStats.byClass.reduce((s, c) => s + c.pending_count, 0) }}
                                        </td>
                                        <td class="text-right py-2.5 px-3 text-green-600 dark:text-green-400 text-xs">
                                            {{ fmtAmount(feesStats.byClass.reduce((s, c) => s + c.paid_amount, 0)) }}
                                        </td>
                                        <td class="text-right py-2.5 px-3 text-amber-600 dark:text-amber-400 text-xs">
                                            {{ fmtAmount(feesStats.byClass.reduce((s, c) => s + c.remaining_amount, 0)) }}
                                        </td>
                                        <td class="text-right py-2.5 px-3 text-violet-600 dark:text-violet-400">
                                            {{ feesRate }}%
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Détail par mode de paiement -->
                    <div class="card p-4" v-if="feesStats?.paymentTypes?.length">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Détail par mode de paiement</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <th class="text-left py-2 px-3 text-gray-500 dark:text-gray-400 font-medium">Mode</th>
                                        <th class="text-right py-2 px-3 text-gray-500 dark:text-gray-400 font-medium">Dossiers</th>
                                        <th class="text-right py-2 px-3 text-gray-500 dark:text-gray-400 font-medium">Montant collecté</th>
                                        <th class="text-right py-2 px-3 text-gray-500 dark:text-gray-400 font-medium">% dossiers</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(pt, i) in feesStats.paymentTypes" :key="pt.type"
                                        class="border-b border-gray-50 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                        <td class="py-2.5 px-3 flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{ background: feesPaymentTypeColors[i % feesPaymentTypeColors.length] }"/>
                                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ feesPaymentTypeLabels[i] }}</span>
                                        </td>
                                        <td class="text-right py-2.5 px-3 text-gray-700 dark:text-gray-300 font-semibold">{{ pt.count }}</td>
                                        <td class="text-right py-2.5 px-3 text-green-600 dark:text-green-400 font-semibold">{{ fmtAmount(pt.amount) }}</td>
                                        <td class="text-right py-2.5 px-3 text-gray-500 dark:text-gray-400">
                                            {{ (feesStats.countPaid + feesStats.countPending + feesStats.countUnpaid) > 0
                                                ? Math.round(pt.count / (feesStats.countPaid + feesStats.countPending + feesStats.countUnpaid) * 100)
                                                : 0 }}%
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Progression de collecte -->
                    <div class="card p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Progression de collecte</h3>
                        <div class="space-y-3">
                            <ProgressBar label="Dossiers payés / Total dossiers"
                                :value="feesStats?.countPaid ?? totalFeesCollections"
                                :max="(feesStats ? feesStats.countPaid + feesStats.countPending + feesStats.countUnpaid : totalFeesCollections) || 1"
                                color="success" />
                            <ProgressBar label="Dossiers en attente"
                                :value="feesStats?.countPending ?? 0"
                                :max="(feesStats ? feesStats.countPaid + feesStats.countPending + feesStats.countUnpaid : totalFeesCollections) || 1"
                                color="amber" />
                            <ProgressBar label="Montant collecté / Montant total"
                                :value="feesStats?.paidAmount ?? 0"
                                :max="feesStats?.totalAmount || 1"
                                color="violet" />
                            <ProgressBar label="Règlements aujourd'hui"
                                :value="totalFeesCollectionsToday ?? 0"
                                :max="(feesStats?.countPaid || totalFeesCollections || 1)"
                                color="info" />
                        </div>
                    </div>
                </div>

                <!-- ── COMMUNICATION ───────────────────────────────────────── -->
                <div v-show="active === 'comms'" class="space-y-4">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Communication & Tableaux d'affichage</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <KpiCard label="Notices totales"   :value="totalNoticeBoard ?? 0"  color="violet" icon="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        <KpiCard label="Devoirs assignés"  :value="totalHomework ?? 0"     color="info"   icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        <KpiCard label="Travaux pratiques" :value="totalWork ?? 0"          color="success" icon="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        <KpiCard label="Total présences"   :value="totalAttendance ?? 0"   color="warning" icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        <div class="card p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Prochains événements</h3>
                            <div v-if="!upcomingEvents?.length" class="text-center py-8 text-xs text-gray-400">Aucun événement</div>
                            <div v-else class="space-y-2">
                                <div v-for="ev in (upcomingEvents ?? []).slice(0, 6)" :key="ev.id"
                                    class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex flex-col items-center justify-center text-white font-bold text-xs"
                                        :style="{ background: typeColors[ev.event_type ?? ev.extendedProps?.type] ?? '#7B74F0' }">
                                        <span class="text-base leading-none">{{ fmtDay(ev.event_date ?? ev.start) }}</span>
                                        <span class="text-[9px] leading-none mt-0.5 uppercase">{{ fmtMonth(ev.event_date ?? ev.start) }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ ev.title }}</p>
                                        <p class="text-[10px] text-gray-400">{{ typeLabels[ev.event_type ?? ev.extendedProps?.type] ?? 'Événement' }}</p>
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

                <!-- ── CONFIGURATION ───────────────────────────────────────── -->
                <div v-show="active === 'config'" class="space-y-4">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Configuration — Mon école</h2>
                    <div class="card p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Accès rapides</h3>
                        <div class="grid grid-cols-1 gap-2">
                            <a v-for="link in adminQuickLinks" :key="link.href" :href="link.href"
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

            </template>
        </DashTabs>

        <!-- ── CALENDRIER ──────────────────────────────────────────────────── -->
        <AppCalendar
            title="Calendrier scolaire"
            subtitle="Cours, événements et activités"
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
    totalFeesCollectionsToday?: number; totalHomework?: number; totalWork?: number; totalAttendance?: number; totalNoticeBoard?: number;
    totalAttendanceStudentPresent: number; totalAttendanceStudentLate: number;
    totalAttendanceStudentAbsent: number; totalAttendanceStudentHalfDay: number;
    totalStaff?: number; totalPendingLeaves?: number; totalPendingGrades?: number;
    totalOpenEvals?: number; totalDraftBulletins?: number; totalPublishedBulletins?: number;
    totalUpcomingEvents?: number;
    totalStudentMale?: number; totalStudentFemale?: number;
    totalTeacherMale?: number; totalTeacherFemale?: number;
    totalParentMale?: number; totalParentFemale?: number;
    totalAdminMale?: number; totalAdminFemale?: number;
    attendanceByMonth?: { present: number[]; late: number[]; absent: number[]; halfday: number[] };
    feesStats?: {
        totalAmount: number; paidAmount: number; remainingAmount: number;
        countPaid: number; countPending: number; countUnpaid: number;
        collectionRate: number;
        paymentTypes: { type: string; count: number; amount: number }[];
        monthlyCount: number[]; monthlyPaid: number[];
        monthlyByType: { name: string; data: number[] }[];
        byClass: {
            class_id: number; class_name: string;
            total: number; paid_count: number; pending_count: number;
            paid_amount: number; total_amount: number; remaining_amount: number;
        }[];
    };
    currentLeaves?: any[]; upcomingEvents?: any[]; calendarEvents?: any[]; currentPeriod?: any;
    [key: string]: unknown;
}>();

const today  = new Date();
const months = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
const attendancePeriod = ref('month');
const hrPeriod         = ref('month');
const financePeriod    = ref('month');

const tabs = [
    { key: 'overview',   label: 'Vue générale',       icon: 'chart-bar' },
    { key: 'attendance', label: 'Présences',           icon: 'user-check',   badge: props.totalAttendanceStudentAbsent },
    { key: 'academic',   label: 'Académique',          icon: 'academic-cap', badge: (props.totalPendingGrades as number ?? 0) + (props.totalDraftBulletins ?? 0) || undefined },
    { key: 'hr',         label: 'Ressources humaines', icon: 'user-group',   badge: props.totalPendingLeaves },
    { key: 'finance',    label: 'Contributions',       icon: 'banknotes' },
    { key: 'comms',      label: 'Communication',       icon: 'megaphone' },
    { key: 'config',     label: 'Configuration',       icon: 'cog-6-tooth' },
];

const userDistSeries = [{ name: 'Utilisateurs', data: [props.totalStudent, props.totalTeacher, props.totalParent, props.totalAdmin] }];

// ── Attendance — données réelles par mois ─────────────────────────────────────
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
    Math.round(props.totalAttendanceStudentPresent / totalAtt.value * 100),
    Math.round(props.totalAttendanceStudentLate    / totalAtt.value * 100),
    Math.round(props.totalAttendanceStudentAbsent  / totalAtt.value * 100),
    Math.round(props.totalAttendanceStudentHalfDay / totalAtt.value * 100),
]);
const evalMonthSeries = [
    { name: 'Brouillon', data: [2, 3, 4, 2, 5, 3, 4, 2, 3, 5, 4, 3] },
    { name: 'Validées',  data: [1, 2, 3, 5, 4, 6, 5, 7, 6, 4, 5, 6] },
];
const feesRate = computed(() => props.feesStats?.collectionRate ?? Math.round(Math.min(100, (props.totalFeesCollections / (props.totalStudent || 1)) * 100)));

// ── Données contributions réelles ─────────────────────────────────────────────
const feesAreaSeries = computed(() => {
    const fs = props.feesStats;
    if (fs?.monthlyCount?.length === 12) {
        return [
            { name: 'Contributions', data: fs.monthlyCount },
            { name: 'Montant payé',  data: fs.monthlyPaid  },
        ];
    }
    return [
        { name: 'Contributions', data: [15, 22, 30, 40, 38, 45, 50, 55, 48, 60, 65, 70] },
        { name: 'Objectif',      data: [20, 25, 30, 40, 42, 48, 52, 58, 55, 62, 68, 75] },
    ];
});

const feesPaymentTypeSeries = computed(() => {
    const fs = props.feesStats;
    if (fs?.paymentTypes?.length) return fs.paymentTypes.map(p => p.count);
    return [];
});
const feesPaymentTypeLabels = computed(() => {
    const fs = props.feesStats;
    const labels: Record<string, string> = {
        cash: 'Espèces', check: 'Chèque', transfer: 'Virement', virement: 'Virement',
        kkiapay: 'Kkiapay', paypal: 'PayPal', stripe: 'Stripe', fedapay: 'FedaPay',
    };
    if (fs?.paymentTypes?.length) return fs.paymentTypes.map(p => labels[p.type] ?? p.type);
    return [];
});
const feesPaymentTypeColors = ['#7C3AED','#10B981','#3B82F6','#F59E0B','#EF4444','#06B6D4','#8B5CF6','#EC4899'];

const feesMonthlyByTypeSeries = computed(() => {
    const fs = props.feesStats;
    if (fs?.monthlyByType?.length) return fs.monthlyByType;
    return [];
});

const fmtAmount = (n: number) => new Intl.NumberFormat('fr-FR').format(n);


const typeColors: Record<string, string> = { academic: '#3b82f6', cultural: '#8b5cf6', administrative: '#f59e0b', exam: '#ef4444', ceremony: '#10b981', trip: '#06b6d4' };
const typeLabels: Record<string, string> = { academic: 'Académique', cultural: 'Culturel', administrative: 'Admin', exam: 'Examen', ceremony: 'Cérémonie', trip: 'Sortie' };
const fmtDay   = (d: string) => d ? new Date(d).getDate() : '';
const fmtMonth = (d: string) => d ? months[new Date(d).getMonth()] : '';
const eventTypeColor = (type: string) => typeColors[type] ?? '#6366f1';
const eventTypeLabel = (type: string) => typeLabels[type] ?? type ?? '—';

// ── Communication ──────────────────────────────────────────────────────────────
const commSeries = [
    { name: 'Notices',  data: [5, 7, 3, 9, 6, 8] },
    { name: 'Devoirs',  data: [8, 12, 10, 14, 9, 11] },
    { name: 'Travaux',  data: [3, 5, 4, 6, 5, 7] },
];

// ── Quick links admin ──────────────────────────────────────────────────────────
const adminQuickLinks = [
    { href: '/admin/class/list',                     label: 'Gestion des classes',          icon: 'building-library' },
    { href: '/admin/subject/list',                   label: 'Gestion des matières',         icon: 'book-open' },
    { href: '/admin/examinations/period/list',       label: 'Sessions d\'examen',           icon: 'clipboard-document-list' },
    { href: '/admin/feescollections/collections/list', label: 'Contributions scolaires',    icon: 'banknotes' },
    { href: '/admin/staff/leaves/list',              label: 'Congés du personnel',          icon: 'calendar-days' },
    { href: '/admin/bulletins/list',                 label: 'Bulletins scolaires',          icon: 'document-text' },
];
const calendarEventsFormatted = computed<CalEvent[]>(() => {
    if (props.calendarEvents?.length) {
        return (props.calendarEvents as any[]).map(ev => ({
            id: ev.id, title: ev.title, start: ev.start ?? ev.event_date,
            color: ev.color ?? typeColors[ev.extendedProps?.type ?? ev.event_type] ?? '#7B74F0',
            start_time: ev.start_time ?? '', end_time: ev.end_time ?? '',
            extendedProps: { type_label: typeLabels[ev.extendedProps?.type ?? ev.event_type] ?? 'Événement', description: ev.description ?? '', location: ev.location ?? '', start_time: ev.start_time ?? '', end_time: ev.end_time ?? '' },
        }));
    }
    const m = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');
    return [
        { id: 1, title: 'Réunion parents', start: `${m}-03`, color: '#f472b6', start_time: '08:00', end_time: '10:00', extendedProps: { type_label: 'Réunion', location: 'Salle 1' } },
        { id: 2, title: 'Examen Maths',    start: `${m}-08`, color: '#a78bfa', start_time: '09:00', end_time: '12:00', extendedProps: { type_label: 'Examen',  location: 'Grande salle' } },
    ] as CalEvent[];
});
</script>
