@extends('layouts.app')
@section('content')
<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between"
    >
        <h2 class="uppercase font-bold text-black dark:text-bodydark">
            Horaires de cours pour <span class="text-violet-600 bg-violet-100 dark:bg-violet-600 dark:text-white rounded-full px-4 py-2 ms-5">{{ $getStudent->name }} {{ $getStudent->last_name }}</span>
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <span class="font-medium text-violet-600"><i class="fa-solid fa-landmark"></i></span>
                </li>
                <li>
                    /<a class="font-medium hover:text-violet-600 transition duration-300"
                        href="{{ url('parent/my_student/'.$getStudent->id.'/subject') }}"> Liste des cours </a>
                </li>
            </ol>
        </nav>
    </div>

    <div class="p-4 flex items-center justify-center">
        <div class="w-full max-w-screen-xl bg-white shadow-lg mt-24 rounded-lg dark:border-strokedark dark:bg-boxdark">
            @foreach($getTeacherTimetable as $teacherTimetable)
            <div class="bg-white shadow-md rounded-lg border border-gray-200 p-4 dark:border-strokedark dark:bg-boxdark">
                <div class="font-bold text-md mb-3 text-center bg-violet-100 text-violet-800 py-2 rounded">
                    {{ $getClass->name }} - {{ $getSubject->name }}
                </div>
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left border border-gray-200 dark:border-strokedark rounded-lg">
                        <thead class="text-white uppercase bg-violet-500">
                        <tr>
                            <th scope="col" class="px-4 py-3">Jour</th>
                            <th scope="col" class="px-4 py-3">Heures</th>
                            <th scope="col" class="px-4 py-3">Salle</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($teacherTimetable['week'] as $teacherTime)
                        <tr class="border-b hover:bg-violet-100 dark:hover:bg-gray-700">
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
@endsection
