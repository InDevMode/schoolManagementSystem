@extends('layouts.app')
@section('content')
    <main>
        <div class="mx-auto max-w-screen-7xl p-4 md:p-6 2xl:p-5">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-5">

                <!-- Total Users (non cliquable) -->
                <div
                    class="group rounded-2xl p-6 shadow-md bg-gradient-to-r from-blue-500 to-blue-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <!-- Ligne du haut -->
                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalUser }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-users text-white group-hover:text-blue-700"></i>
                        </div>
                    </div>

                    <!-- Ligne du bas -->
                    <div class="flex justify-between items-end">
                        <span
                            class="text-sm font-bold group-hover:text-white">Utilisateur{{ $totalUser > 1 ? 's' : '' }}</span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalUser > 0 ? round(($totalUser * 100) / $totalUser, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total Admin (cliquable) -->
                <div onclick="window.location.href='{{ url('/admin/admin/list') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-purple-500 to-purple-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">

                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalAdmin }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-user-shield text-white group-hover:text-purple-700"></i>
                        </div>
                    </div>

                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Administrateur{{ $totalAdmin > 1 ? 's' : '' }}</span>
                        <span
                            class="text-xs font-semibold text-red-200 group-hover:text-white">{{ $totalUser > 0 ? round(($totalAdmin * 100) / $totalUser, 2) : 0 }}%</span>

                    </div>
                </div>

                <!-- Total Students -->
                <div onclick="window.location.href='{{ url('/admin/student/list') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-green-500 to-green-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">

                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalStudent }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-user-graduate text-white group-hover:text-green-700"></i>
                        </div>
                    </div>

                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Apprenant{{ $totalStudent > 1 ? 's' : '' }}</span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalUser > 0 ? round(($totalStudent * 100) / $totalUser, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total Parents -->
                <div onclick="window.location.href='{{ url('/admin/parent/list') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-pink-500 to-pink-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">

                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalParent }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-user-group text-white group-hover:text-pink-700"></i>
                        </div>
                    </div>

                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Parent{{ $totalParent > 1 ? 's' : '' }}</span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalUser > 0 ? round(($totalParent * 100) / $totalUser, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total Teachers -->
                <div onclick="window.location.href='{{ url('/admin/teacher/list') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-orange-500 to-orange-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">

                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalTeacher }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-chalkboard-teacher text-white group-hover:text-orange-700"></i>
                        </div>
                    </div>

                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Professeur{{ $totalTeacher > 1 ? 's' : '' }}</span>
                        <span
                            class="text-xs font-semibold text-red-200 group-hover:text-white">{{ $totalUser > 0 ? round(($totalTeacher * 100) / $totalUser, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total Classes -->
                <div onclick="window.location.href='{{ url('/admin/class/list') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-violet-500 to-violet-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalClass }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-landmark text-white group-hover:text-violet-700"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Classe{{ $totalClass > 1 ? 's' : '' }}</span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalClass > 0 ? round(($totalClass * 100) / $totalClass, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total Subject -->
                <div onclick="window.location.href='{{ url('/admin/subject/list') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-teal-500 to-teal-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalSubject }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-book-open-reader text-white group-hover:text-teal-700"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Matière{{ $totalSubject > 1 ? 's' : '' }}</span>
                        <span
                            class="text-xs font-semibold text-red-200 group-hover:text-white">{{ $totalSubject > 0 ? round(($totalSubject * 100) / $totalSubject, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total Fees Collections -->
                <div onclick="window.location.href='{{ url('/admin/feescollections/feescollects/feesList') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-amber-500 to-amber-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalFeesCollections }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-credit-card text-white group-hover:text-amber-700"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Contribution{{ $totalFeesCollections > 1 ? 's' : '' }}</span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalFeesCollections > 0 ? round(($totalFeesCollections * 100) / $totalFeesCollections, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total Communicates -->
                <div onclick="window.location.href='{{ url('/admin/communicate/noticeboard/list') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-red-500 to-red-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalCommunicate }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-bell text-white group-hover:text-red-700"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Notification{{ $totalCommunicate > 1 ? 's' : '' }}</span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalCommunicate > 0 ? round(($totalCommunicate * 100) / $totalCommunicate, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total Exams -->
                <div onclick="window.location.href='{{ url('/admin/examinations/exam/list') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-yellow-500 to-yellow-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalExam }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid  fa-flask-vial text-white group-hover:text-yellow-700"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Evaluation{{ $totalExam > 1 ? 's' : '' }}</span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalExam > 0 ? round(($totalExam * 100) / $totalExam, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total des homeworks -->
                <div onclick="window.location.href='{{ url('/admin/practicalworks/reports') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-emerald-500 to-emerald-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold text-gray-100">{{ $totalHomework }}</span>
                        <span class="text-3xl font-bold">{{ $totalWork }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid  fa-user-check text-white group-hover:text-emerald-700"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Trav{{ $totalWork > 1 ? 'aux' : 'ail' }} de
                            maison{{ $totalWork > 1 ? 's' : '' }} </span>
                        <span
                            class="text-xs font-semibold text-gray-200 group-hover:text-white">{{ $totalWork > 0 ? round(($totalHomework * 100) / $totalWork, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total des présences -->
                <div onclick="window.location.href='{{ url('/admin/attendance/report') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-gray-600 to-gray-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold text-green-500">{{ $totalAttendanceStudentPresent }}</span>
                        <span class="text-3xl font-bold text-yellow-500">{{ $totalAttendanceStudentLate }}</span>
                        <span class="text-3xl font-bold text-red-500">{{ $totalAttendanceStudentAbsent }}</span>
                        <span class="text-3xl font-bold text-blue-500">{{ $totalAttendanceStudentHalfDay }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid  fa-user-check text-white group-hover:text-gray-700"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Présence{{ $totalAttendance > 1 ? 's' : '' }} </span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalAttendance > 0 ? round(($totalAttendanceStudentPresent * 100) / $totalAttendance, 2) : 0 }}%</span>
                        <span
                            class="text-xs font-semibold text-yellow-200 group-hover:text-white">{{ $totalAttendance > 0 ? round(($totalAttendanceStudentLate * 100) / $totalAttendance, 2) : 0 }}%</span>
                        <span
                            class="text-xs font-semibold text-red-200 group-hover:text-white">{{ $totalAttendance > 0 ? round(($totalAttendanceStudentAbsent * 100) / $totalAttendance, 2) : 0 }}%</span>
                        <span
                            class="text-xs font-semibold text-blue-200 group-hover:text-white">{{ $totalAttendance > 0 ? round(($totalAttendanceStudentHalfDay * 100) / $totalAttendance, 2) : 0 }}%</span>
                    </div>
                </div>



            </div>



            <div class="mt-4 grid grid-cols-12 gap-4 md:mt-6 md:gap-6 2xl:mt-7.5 2xl:gap-7.5">
                <!-- ====== Chart One Start -->
                @include('layouts.partials.chart-01')
                <!-- ====== Chart One End -->

                <!-- ====== Chart Two Start -->
                @include('layouts.partials.chart-02')
                <!-- ====== Chart Two End -->

                <!-- ====== Chart Three Start -->
                @include('layouts.partials.chart-03')
                <!-- ====== Chart Three End -->

                <!-- ====== Map One Start -->
                @include('layouts.partials.map-01')
                <!-- ====== Map One End -->

                <!-- ====== Table One Start -->
                <div class="col-span-12 xl:col-span-8">
                    @include('layouts.partials.table-01')
                </div>
                <!-- ====== Table One End -->

                <!-- ====== Chat Card Start -->
                <div
                    class="col-span-12 rounded-sm border border-stroke bg-white py-6 shadow-default dark:border-strokedark dark:bg-boxdark xl:col-span-4">
                    <h4 class="mb-6 px-7.5 text-xl font-bold text-black dark:text-white">
                        Chats
                    </h4>

                    <div>
                        <a href="" class="flex items-center gap-5 px-7.5 py-3 hover:bg-gray-3 dark:hover:bg-meta-4">
                            <div class="relative h-14 w-14 rounded-full">
                                <img src="{{ asset('public/images/user/user-03.png') }}" alt="User" />
                                <span
                                    class="absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-white bg-meta-3"></span>
                            </div>

                            <div class="flex flex-1 items-center justify-between">
                                <div>
                                    <h5 class="font-medium text-black dark:text-white">
                                        Devid Heilo
                                    </h5>
                                    <p>
                                        <span class="text-sm font-medium text-black dark:text-white">Hello, how are
                                            you?</span>
                                        <span class="text-xs"> . 12 min</span>
                                    </p>
                                </div>
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-primary">
                                    <span class="text-sm font-medium text-white">3</span>
                                </div>
                            </div>
                        </a>
                        <a href="" class="flex items-center gap-5 px-7.5 py-3 hover:bg-gray-3 dark:hover:bg-meta-4">
                            <div class="relative h-14 w-14 rounded-full">
                                <img src="{{ asset('public/images/user/user-05.png') }}" alt="User" />
                                <span
                                    class="absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-white bg-meta-3"></span>
                            </div>

                            <div class="flex flex-1 items-center justify-between">
                                <div>
                                    <h5 class="font-medium">Henry Fisher</h5>
                                    <p>
                                        <span class="text-sm font-medium">I am waiting for you</span>
                                        <span class="text-xs"> . 5:54 PM</span>
                                    </p>
                                </div>
                            </div>
                        </a>
                        <a href="" class="flex items-center gap-5 px-7.5 py-3 hover:bg-gray-3 dark:hover:bg-meta-4">
                            <div class="relative h-14 w-14 rounded-full">
                                <img src="{{ asset('public/images/user/user-05.png') }}" alt="User" />
                                <span
                                    class="absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-white bg-meta-6"></span>
                            </div>

                            <div class="flex flex-1 items-center justify-between">
                                <div>
                                    <h5 class="font-medium">Wilium Smith</h5>
                                    <p>
                                        <span class="text-sm font-medium">Where are you now?</span>
                                        <span class="text-xs"> . 10:12 PM</span>
                                    </p>
                                </div>
                            </div>
                        </a>
                        <a href="" class="flex items-center gap-5 px-7.5 py-3 hover:bg-gray-3 dark:hover:bg-meta-4">
                            <div class="relative h-14 w-14 rounded-full">
                                <img src="{{ asset('public/images/user/user-01.png') }}" alt="User" />
                                <span
                                    class="absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-white bg-meta-3"></span>
                            </div>

                            <div class="flex flex-1 items-center justify-between">
                                <div>
                                    <h5 class="font-medium text-black dark:text-white">
                                        Henry Deco
                                    </h5>
                                    <p>
                                        <span class="text-sm font-medium text-black dark:text-white">Thank you so
                                            much!</span>
                                        <span class="text-xs"> . Sun</span>
                                    </p>
                                </div>
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-primary">
                                    <span class="text-sm font-medium text-white">2</span>
                                </div>
                            </div>
                        </a>
                        <a href="" class="flex items-center gap-5 px-7.5 py-3 hover:bg-gray-3 dark:hover:bg-meta-4">
                            <div class="relative h-14 w-14 rounded-full">
                                <img src="{{ asset('public/images/user/user-02.png') }}" alt="User" />
                                <span
                                    class="absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-white bg-meta-7"></span>
                            </div>

                            <div class="flex flex-1 items-center justify-between">
                                <div>
                                    <h5 class="font-medium">Jubin Jack</h5>
                                    <p>
                                        <span class="text-sm font-medium">I really love that!</span>
                                        <span class="text-xs"> . Oct 23</span>
                                    </p>
                                </div>
                            </div>
                        </a>
                        <a href="" class="flex items-center gap-5 px-7.5 py-3 hover:bg-gray-3 dark:hover:bg-meta-4">
                            <div class="relative h-14 w-14 rounded-full">
                                <img src="{{ asset('public/images/user/user-05.png') }}" alt="User" />
                                <span
                                    class="absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-white bg-meta-6"></span>
                            </div>

                            <div class="flex flex-1 items-center justify-between">
                                <div>
                                    <h5 class="font-medium">Wilium Smith</h5>
                                    <p>
                                        <span class="text-sm font-medium">Where are you now?</span>
                                        <span class="text-xs"> . Sep 20</span>
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- ====== Chat Card End -->
            </div>
        </div>
    </main>
@endsection

<script></script>
