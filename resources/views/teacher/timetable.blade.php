@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg dark:border-gray-700 mt-14">
        @include('message')
        <div class="flex justify-between pt-2 mb-5">
            <div class="space-x-2 font-semibold">
                <span class="text-violet-500"><i class="fa-solid fa-clock"></i></span>
                <span><i class="fa-solid fa-chevron-right"></i></span>
                <span class="hover:underline hover:text-violet-500 transition-all duration-300">
                    <a href="{{ url('teacher/dashboard') }}">Dashboard</a>
                </span>
                <span><i class="fa-solid fa-chevron-right"></i></span>
                <span class="hover:underline hover:text-violet-500 transition-all duration-300">
                    <a href="{{ url('teacher/class_subject') }}">Liste de mes cours et horaires</a>
                </span>
                <span><i class="fa-solid fa-chevron-right"></i></span>
                <span>{{ $getSubject->name }}</span>
            </div>
        </div>

        <div class="p-4 flex items-center justify-center">
            <div class="w-full max-w-screen-xl bg-white shadow-lg mt-24 rounded-md">
                    @foreach($getTeacherTimetable as $teacherTimetable)
                    <div class="bg-white shadow-md rounded-lg border border-gray-300 p-4">
                        <div class="font-bold text-md mb-3 text-center bg-violet-100 text-violet-800 py-2 rounded">
                            {{ $getClass->name }} - {{ $getSubject->name }}
                        </div>
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left border border-gray-200 rounded-lg">
                                <thead class="text-white uppercase bg-violet-500">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Jour</th>
                                    <th scope="col" class="px-4 py-3">Heures</th>
                                    <th scope="col" class="px-4 py-3">Salle</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($teacherTimetable['week'] as $teacherTime)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $teacherTime['week_name'] }}</td>
                                    <td class="px-4 py-3">
                                        {{ $teacherTime['start_time'] ? \Carbon\Carbon::parse($teacherTime['start_time'])->format('G\h i\m\i\n') : '-'
                                        }} -
                                        {{ $teacherTime['end_time'] ? \Carbon\Carbon::parse($teacherTime['end_time'])->format('G\h i\m\i\n') : '-'
                                        }}
                                    </td>
                                    <td class="px-4 py-3">{{ $teacherTime['room_number'] ? : '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="p-6 text-center text-gray-500">
                                        Aucun horaire défini.
                                    </td>
                                </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
