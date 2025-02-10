@extends('layouts.app')
@section('content')
<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between"
    >
        <h2 class="uppercase font-bold text-black dark:text-bodydark">
            Liste des programmes
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <span class="font-medium text-violet-600"><i class="fa-solid fa-landmark"></i></span>
                </li>
                <li>
                    /<a class="font-medium hover:text-violet-600 transition duration-300"
                        href="{{ url('student/dashboard') }}"> Dashboard</a>
                </li>
            </ol>
        </nav>
    </div>

    <div class="grid grid-cols-4 gap-4 mt-5">
        @foreach($getStudentTimetable as $studentTimetable)
        <div class="bg-white shadow-md rounded-lg border border-gray-300 p-4 dark:border-strokedark dark:bg-boxdark">
            <div class="font-bold text-md mb-3 text-center bg-violet-100 text-violet-800 py-2 rounded">
                {{ $studentTimetable['name'] }}
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
                    @forelse($studentTimetable['week'] as $studentTime)
                    <tr class="border-b hover:bg-violet-100 dark:hover:bg-gray-700">
                        <td class="px-4 py-3">{{ $studentTime['week_name'] }}</td>
                        <td class="px-4 py-3">
                            {{ $studentTime['start_time'] ? \Carbon\Carbon::parse($studentTime['start_time'])->format('G\h:i\m\i\n') : '-'
                            }} -
                            {{ $studentTime['end_time'] ? \Carbon\Carbon::parse($studentTime['end_time'])->format('G\h:i\m\i\n') : '-'
                            }}
                        </td>
                        <td class="px-4 py-3 text-center">{{ $studentTime['room_number'] ? : '-' }}</td>
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
@endsection
