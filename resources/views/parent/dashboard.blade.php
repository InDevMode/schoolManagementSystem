@extends('layouts.app')
@section('content')
    <main>
        <div class="mx-auto max-w-screen-7xl p-4 md:p-6 2xl:p-5">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-5">
                <!-- Total FeesCollections AmoutPaid By Student -->
                <div onclick="window.location.href='{{ url('/student/my_fees') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-violet-500 to-violet-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <div class="flex justify-between items-start">
                        <span
                            class="text-3xl font-bold">{{ number_format($totalFeesCollectionsAmoutPaidByStudents, 0, ',', ' ') }}
                            F CFA</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-landmark text-white group-hover:text-violet-700"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Contributions</span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalFeesCollectionsAmountStudents > 0 ? round(($totalFeesCollectionsAmoutPaidByStudents * 100) / $totalFeesCollectionsAmountStudents, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total Parent Student  -->
                <div onclick="window.location.href='{{ url('/parent/my_student') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-green-500 to-green-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">

                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalParentStudent }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-user-graduate text-white group-hover:text-green-700"></i>
                        </div>
                    </div>

                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Apprenant{{ $totalParentStudent > 1 ? 's' : ''  }}</span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalStudent > 0 ? round(($totalParentStudent * 100) / $totalStudent, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total NoticeBoard Student -->
                <div onclick="window.location.href='{{ url('/parent/my_noticeboard') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-red-500 to-red-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalNoticeBoardParent }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-bell text-white group-hover:text-red-700"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Notification{{ $totalNoticeBoardParent > 1 ? 's' : ''  }}</span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalCommunicate > 0 ? round(($totalNoticeBoardParent * 100) / $totalCommunicate, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total Work Student -->
                <div onclick="window.location.href='{{ url('/parent/my_student') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-yellow-500 to-yellow-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalHomeworkStudent }}</span>
                        <span class="text-3xl font-bold">{{ $totalWorkStudent }} </span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid  fa-flask-vial text-white group-hover:text-yellow-700"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Travaux de Maison</span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalHomework > 0 ? round(($totalHomeworkStudent * 100) / $totalHomework, 2) : 0 }}%</span> <span
                            class="text-xs font-semibold text-emerald-200 group-hover:text-white">{{ $totalWork > 0 ? round(($totalWorkStudent * 100) / $totalWork, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total des présences -->
                <div onclick="window.location.href='{{ url('/parent/my_student') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-gray-600 to-gray-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold text-green-500">{{ $totalByAttendanceTypeStudentPresent }}</span>
                        <span class="text-3xl font-bold text-yellow-500">{{ $totalByAttendanceTypeStudentLate }}</span>
                        <span class="text-3xl font-bold text-red-500">{{ $totalByAttendanceTypeStudentAbsent }}</span>
                        <span class="text-3xl font-bold text-blue-500">{{ $totalByAttendanceTypeStudentHalfDay }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid  fa-user-check text-white group-hover:text-gray-700"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Présence{{ $totalAttendance > 1 ? 's' : '' }} </span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalAttendance > 0 ? round(($totalByAttendanceTypeStudentPresent * 100) / $totalAttendance, 2) : 0 }}%</span>
                        <span
                            class="text-xs font-semibold text-yellow-200 group-hover:text-white">{{ $totalAttendance > 0 ? round(($totalByAttendanceTypeStudentLate * 100) / $totalAttendance, 2) : 0 }}%</span>
                        <span
                            class="text-xs font-semibold text-red-200 group-hover:text-white">{{ $totalAttendance > 0 ? round(($totalByAttendanceTypeStudentAbsent * 100) / $totalAttendance, 2) : 0 }}%</span>
                        <span
                            class="text-xs font-semibold text-blue-200 group-hover:text-white">{{ $totalAttendance > 0 ? round(($totalByAttendanceTypeStudentHalfDay * 100) / $totalAttendance, 2) : 0 }}%</span>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection

<script></script>
