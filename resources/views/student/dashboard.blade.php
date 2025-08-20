@extends('layouts.app')
@section('content')
    <main>
        <div class="mx-auto max-w-screen-7xl p-4 md:p-6 2xl:p-10">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-5">

                <!-- Total FeesCollections AmoutPaid By Student -->
                <div onclick="window.location.href='{{ url('/student/my_fees') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-violet-500 to-violet-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ number_format($totalFeesCollectionsAmoutPaidByStudent, 0, ',', ' ') }} F CFA</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-landmark text-white group-hover:text-violet-700"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Contributions</span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalFeesCollectionsAmountStudent > 0 ? round(($totalFeesCollectionsAmoutPaidByStudent * 100) / $totalFeesCollectionsAmountStudent, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total Student Subject -->
                <div onclick="window.location.href='{{ url('/student/my_subject') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-green-500 to-green-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">

                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalStudentSubject }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-user-graduate text-white group-hover:text-green-700"></i>
                        </div>
                    </div>

                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Matière{{ $totalStudentSubject  > 1 ? 's' : ''  }}</span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalSubject > 0 ? round(($totalStudentSubject * 100) / $totalSubject, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total NoticeBoard Student -->
                <div onclick="window.location.href='{{ url('/student/my_noticeboard') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-red-500 to-red-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalNoticeBoardStudent }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-bell text-white group-hover:text-red-700"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Notification{{ $totalNoticeBoardStudent  > 1 ? 's' : ''  }}</span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalCommunicate > 0 ? round(($totalNoticeBoardStudent * 100) / $totalCommunicate, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total Work Student -->
                <div onclick="window.location.href='{{ url('/student/my_homework') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-yellow-500 to-yellow-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalWorkStudent }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid  fa-flask-vial text-white group-hover:text-yellow-700"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Travaux de Maison</span>
                        <span
                            class="text-xs font-semibold text-green-200 group-hover:text-white">{{ $totalHomeworkStudent > 0 ? round(($totalWorkStudent * 100) / $totalHomeworkStudent, 2) : 0 }}%</span>
                    </div>
                </div>

                <!-- Total Attendance Student -->
                <div onclick="window.location.href='{{ url('/student/my_attendance') }}'"
                    class="group cursor-pointer rounded-2xl p-6 shadow-md bg-gradient-to-r from-teal-500 to-teal-700 text-white h-32 flex flex-col justify-between hover:shadow-lg transition hover:scale-105">
                    <div class="flex justify-between items-start">
                        <span class="text-3xl font-bold">{{ $totalAttendanceStudent }}</span>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 group-hover:bg-white">
                            <i class="fa-solid fa-book-open-reader text-white group-hover:text-teal-700"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold">Présence</span>
                        <span
                            class="text-xs font-semibold text-red-200 group-hover:text-white">{{ $totalAttendance > 0 ? round(($totalAttendanceStudent * 100) / $totalAttendance, 2) : 0 }}%</span>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection

<script></script>
