<template>
    <div class="space-y-5">

        <!-- ══ HEADER ═══════════════════════════════════════════════════════ -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Tableau de bord</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ today.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}
                    <span v-if="currentPeriod" class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-violet-50 dark:bg-violet-900/20 text-violet-600 dark:text-violet-400 text-xs font-semibold">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ currentPeriod.name }}
                    </span>
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-600 text-white text-xs font-semibold shadow w-fit">
                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse inline-block"/>Administrateur
            </span>
        </div>

        <!-- ══ KPI ROW ════════════════════════════════════════════════════════ -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <KpiCard label="Apprenants"     :value="totalStudent"  color="violet" href="/admin/student/list" trend="+12%" :trendPositive="true"
                icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            <KpiCard label="Professeurs"    :value="totalTeacher" color="info"   href="/admin/teacher/list" trend="+3%" :trendPositive="true"
                icon="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
            <KpiCard label="Parents"        :value="totalParent"  color="amber"  href="/admin/parent/list" trend="+8%" :trendPositive="true"
                icon="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            <KpiCard label="Contributions"  :value="totalFeesCollections" color="success" href="/admin/feescollections/collections/list"
                sub="Dossiers enregistrés"
                icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </div>

        <!-- ══ TABS ══════════════════════════════════════════════════════════ -->
        <DashTabs :tabs="tabs">
            <template #default="{ active }">

                <!-- ── VUE GÉNÉRALE ───────────────────────────────────────── -->
                <div v-show="active === 'overview'" class="space-y-4">
                    <!-- Ligne stats académiques -->
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-2.5">
                        <MiniCard label="Administrateurs" :value="totalAdmin"    icon="shield"                  color="slate"   href="/admin/admin/list"/>
                        <MiniCard label="Classes"          :value="totalClass"   icon="building-library"        color="sky"     href="/admin/class/list"/>
                        <MiniCard label="Matières"         :value="totalSubject" icon="book-open"               color="teal"    href="/admin/subject/list"/>
                        <MiniCard label="Sessions examen"  :value="totalExam"    icon="clipboard-document-list" color="orange"  href="/admin/examinations/period/list"/>
                        <MiniCard label="Devoirs"          :value="totalHomework" icon="pencil"                 color="rose"    href="/admin/practicalworks/homework/list"/>
                    </div>
                    <!-- Alertes -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <AlertCard label="Personnel actif"     :value="totalStaff ?? 0"          icon="user-group"    variant="default" href="/admin/staff/list"/>
                        <AlertCard label="Congés en attente"   :value="totalPendingLeaves ?? 0"  icon="calendar-days" variant="warning" href="/admin/staff/leaves/list"/>
                        <AlertCard label="Évals à valider"     :value="totalOpenEvals ?? 0"      icon="pencil-square" variant="danger"  href="/admin/evaluations/grades/pending"/>
                        <AlertCard label="Bulletins brouillon" :value="totalDraftBulletins ?? 0" icon="document-text" variant="info"    href="/admin/bulletins/list"/>
                    </div>

                    <!-- Charts ligne 1 -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Apprenants par sexe</h3>
                            <p class="text-xs text-gray-400 mb-3">Répartition garçons / filles</p>
                            <ApexDonut
                                :series="[totalStudentMale ?? Math.round(totalStudent * 0.55), totalStudentFemale ?? Math.round(totalStudent * 0.45)]"
                                :labels="['Garçons', 'Filles']"
                                :colors="['#7C3AED', '#F472B6']"
                                center-label="Total"
                                :center-value="totalStudent"
                                :height="200"
                            />
                        </div>
                        <div class="lg:col-span-2 card p-5">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Répartition utilisateurs</h3>
                            </div>
                            <ApexBar
                                :series="userDistSeries"
                                :categories="['Apprenants','Professeurs','Parents','Admins']"
                                :colors="['#7C3AED','#3B82F6','#F59E0B','#EF4444']"
                                :height="200"
                                horizontal
                            />
                        </div>
                    </div>

                    <!-- Présences + événements + congés -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <AttendanceBadge label="Présents"     :value="totalAttendanceStudentPresent"  color="success" icon="user-check"/>
                        <AttendanceBadge label="En retard"    :value="totalAttendanceStudentLate"     color="warning" icon="clock"/>
                        <AttendanceBadge label="Absents"      :value="totalAttendanceStudentAbsent"   color="danger"  icon="user-minus"/>
                        <AttendanceBadge label="Demi-journée" :value="totalAttendanceStudentHalfDay"  color="info"    icon="calendar-days"/>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="card p-0 overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Prochains événements</h3>
                                <a href="/admin/staff/events/list" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">Voir tout</a>
                            </div>
                            <div v-if="!upcomingEvents?.length" class="px-4 py-8 text-center text-xs text-gray-400">Aucun événement</div>
                            <div v-else class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                <div v-for="ev in (upcomingEvents ?? []).slice(0,5)" :key="ev.id"
                                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex flex-col items-center justify-center text-white font-bold text-xs"
                                        :style="{ background: typeColors[ev.event_type ?? ev.extendedProps?.type] ?? '#6366f1' }">
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
                                <a href="/admin/staff/leaves/list" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">Gérer</a>
                            </div>
                            <div v-if="!currentLeaves?.length" class="px-4 py-8 text-center text-xs text-gray-400">Aucun congé en cours</div>
                            <div v-else class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                <div v-for="leave in (currentLeaves ?? []).slice(0,5)" :key="leave.id"
                                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/30">
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

                <!-- ── PRÉSENCES ───────────────────────────────────────────── -->
                <div v-show="active === 'attendance'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Statistiques de présence</h2>
                        <PeriodFilter v-model="attendancePeriod" />
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <AttendanceBadge label="Présents"     :value="totalAttendanceStudentPresent"  color="success" icon="user-check"/>
                        <AttendanceBadge label="En retard"    :value="totalAttendanceStudentLate"     color="warning" icon="clock"/>
                        <AttendanceBadge label="Absents"      :value="totalAttendanceStudentAbsent"   color="danger"  icon="user-minus"/>
                        <AttendanceBadge label="Demi-journée" :value="totalAttendanceStudentHalfDay"  color="info"    icon="calendar-days"/>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Évolution mensuelle</h3>
                            <ApexArea :series="attendanceSeries" :categories="months" :colors="['#10B981','#F59E0B','#EF4444','#3B82F6']" :height="220"/>
                        </div>
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Taux de présence</h3>
                            <ApexRadial :series="attendanceRadial" :labels="['Présents','Retards','Absents','Demi-j.']" :colors="['#10B981','#F59E0B','#EF4444','#3B82F6']" :height="220"/>
                        </div>
                    </div>

                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Progression par mois</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <ProgressBar v-for="(m, i) in months" :key="m" :label="m"
                                :value="attendanceMonthData[i] ?? 0" :max="totalStudent || 1"
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

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Statuts évaluations</h3>
                            <ApexDonut
                                :series="[totalOpenEvals ?? 2, totalPendingGrades ?? 3, totalDraftBulletins ?? 1, 5]"
                                :labels="['Ouvertes','À valider','Brouillon','Validées']"
                                :colors="['#3B82F6','#F59E0B','#7C3AED','#10B981']"
                                :height="200"
                            />
                        </div>
                        <div class="lg:col-span-2 card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Évaluations par mois</h3>
                            <ApexBar
                                :series="evalMonthSeries"
                                :categories="months"
                                :colors="['#7C3AED','#3B82F6']"
                                :height="200"
                                stacked
                            />
                        </div>
                    </div>

                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Progression bulletins</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <ProgressBar label="Bulletins générés"    :value="totalDraftBulletins ?? 0" :max="totalStudent || 1" color="violet" />
                            <ProgressBar label="Bulletins publiés"    :value="totalPublishedBulletins ?? 0" :max="totalStudent || 1" color="success" />
                            <ProgressBar label="Devoirs assignés"     :value="totalHomework ?? 0" :max="totalStudent || 1" color="warning" />
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
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Congés en cours</h3>
                            <div v-if="!currentLeaves?.length" class="flex items-center justify-center h-32 text-xs text-gray-400">Aucun congé en cours</div>
                            <div v-else class="space-y-2">
                                <div v-for="leave in currentLeaves.slice(0,8)" :key="leave.id"
                                    class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/30">
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
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Personnel vs Apprenants</h3>
                            <ApexDonut
                                :series="[totalTeacher, totalStaff ?? 0, totalAdmin]"
                                :labels="['Professeurs','Personnel','Admins']"
                                :colors="['#3B82F6','#10B981','#EF4444']"
                                :height="200"
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
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <KpiCard label="Total dossiers"    :value="totalFeesCollections"    color="violet" icon="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <KpiCard label="Dossiers auj."     :value="totalFeesCollectionsToday ?? 0" color="success" icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2"/>
                        <KpiCard label="Devoirs assignés"  :value="totalHomework ?? 0"      color="info"   icon="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        <KpiCard label="Taux collecte"     :value="feesRate + '%'"           color="amber"  icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z"/>
                    </div>
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Évolution des contributions (12 mois)</h3>
                        <ApexArea :series="feesAreaSeries" :categories="months" :colors="['#7C3AED','#10B981']" :height="240"/>
                    </div>
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Progression de collecte</h3>
                        <div class="space-y-3">
                            <ProgressBar label="Dossiers actifs / Total apprenants" :value="totalFeesCollections" :max="totalStudent || 1" color="violet" />
                            <ProgressBar label="Règlements aujourd'hui" :value="totalFeesCollectionsToday ?? 0" :max="(totalFeesCollections || 1)" color="success" />
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
import { AppCalendar } from '@/Components/UI';
import type { CalEvent } from '@/Components/UI';

const isDark = useDark();
const props = defineProps<{
    totalUser: number; totalAdmin: number; totalTeacher: number; totalStudent: number; totalParent: number;
    totalClass: number; totalSubject: number; totalExam: number; totalFeesCollections: number;
    totalFeesCollectionsToday?: number; totalHomework?: number; totalWork?: number; totalAttendance?: number;
    totalAttendanceStudentPresent: number; totalAttendanceStudentLate: number;
    totalAttendanceStudentAbsent: number; totalAttendanceStudentHalfDay: number;
    totalStaff?: number; totalPendingLeaves?: number; totalOpenEvals?: number; totalDraftBulletins?: number;
    totalPublishedBulletins?: number; totalUpcomingEvents?: number;
    totalStudentMale?: number; totalStudentFemale?: number;
    currentLeaves?: any[]; upcomingEvents?: any[]; calendarEvents?: any[]; currentPeriod?: any;
    [key: string]: unknown;
}>();

const today  = new Date();
const months = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
const attendancePeriod = ref('month');
const hrPeriod         = ref('month');
const financePeriod    = ref('month');

const tabs = [
    { key: 'overview',   label: 'Vue générale',  icon: 'chart-bar' },
    { key: 'attendance', label: 'Présences',      icon: 'user-check',  badge: props.totalAttendanceStudentAbsent },
    { key: 'academic',   label: 'Académique',     icon: 'academic-cap', badge: (props.totalPendingGrades as number ?? 0) + (props.totalDraftBulletins ?? 0) || undefined },
    { key: 'hr',         label: 'RH',             icon: 'user-group',  badge: props.totalPendingLeaves },
    { key: 'finance',    label: 'Contributions',  icon: 'banknotes' },
];

const userDistSeries = [{ name: 'Utilisateurs', data: [props.totalStudent, props.totalTeacher, props.totalParent, props.totalAdmin] }];
const totalAtt = computed(() => props.totalAttendanceStudentPresent + props.totalAttendanceStudentLate + props.totalAttendanceStudentAbsent + props.totalAttendanceStudentHalfDay || 1);
const attendanceMonthData = Array(12).fill(0).map((_, i) => Math.max(0, props.totalAttendanceStudentPresent - i * 2));
const attendanceSeries = [
    { name: 'Présents',     data: Array(12).fill(0).map((_, i) => Math.max(0, props.totalAttendanceStudentPresent - i * 2)) },
    { name: 'Retards',      data: Array(12).fill(0).map((_, i) => Math.max(0, props.totalAttendanceStudentLate + i)) },
    { name: 'Absents',      data: Array(12).fill(0).map((_, i) => Math.max(0, props.totalAttendanceStudentAbsent + i)) },
];
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
const feesRate = computed(() => Math.round(Math.min(100, (props.totalFeesCollections / (props.totalStudent || 1)) * 100)));
const feesAreaSeries = [
    { name: 'Contributions', data: [15, 22, 30, 40, 38, 45, 50, 55, 48, 60, 65, 70] },
    { name: 'Objectif',      data: [20, 25, 30, 40, 42, 48, 52, 58, 55, 62, 68, 75] },
];

const typeColors: Record<string, string> = { academic: '#3b82f6', cultural: '#8b5cf6', administrative: '#f59e0b', exam: '#ef4444', ceremony: '#10b981', trip: '#06b6d4' };
const typeLabels: Record<string, string> = { academic: 'Académique', cultural: 'Culturel', administrative: 'Admin', exam: 'Examen', ceremony: 'Cérémonie', trip: 'Sortie' };
const fmtDay   = (d: string) => d ? new Date(d).getDate() : '';
const fmtMonth = (d: string) => d ? months[new Date(d).getMonth()] : '';
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
