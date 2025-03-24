@extends('layouts.app')
@section('content')
<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between"
    >
        <h2 class="uppercase font-bold text-black dark:text-bodydark">
            Son calendrier d'examens
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <span class="font-medium text-violet-600"><i class="fa-solid fa-calendar-days"></i></span>
                </li>
                <li>
                    /<a class="font-medium hover:text-violet-600 transition duration-300"
                        href="{{ url('parent/my_student') }}"> Apprenants</a>
                </li>
            </ol>
        </nav>
    </div>


    <div class="grid grid-cols-1 gap-4 mt-5">
        @foreach($getExamTimetable as $examTimetable)
        <div class="bg-white shadow-md rounded-lg border border-gray-300 p-4 dark:border-strokedark dark:bg-boxdark">
            <div class="font-bold text-md mb-3 text-center bg-violet-100 text-violet-800 dark:text-violet-100 dark:bg-violet-950 py-2 rounded">
                {{ $examTimetable['name'] }}
            </div>
            <div class="relative overflow-x-auto">
                <table class="w-full text-[12px] text-left border border-gray-200 dark:border-strokedark rounded-lg">
                    <thead class="text-white uppercase bg-violet-500">
                    <tr>
                        <th scope="col" class="px-4 py-3">Matière</th>
                        <th scope="col" class="px-4 py-3">Jour</th>
                        <th scope="col" class="px-4 py-3">Date</th>
                        <th scope="col" class="px-4 py-3">Heures</th>
                        <th scope="col" class="px-4 py-3">Salle</th>
                        <th scope="col" class="px-4 py-3">Note totale</th>
                        <th scope="col" class="px-4 py-3">Note de passage</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($examTimetable['getExams'] as $exams)
                    <tr class="border-b hover:bg-violet-100 dark:hover:bg-gray-700">
                        <td class="px-4 py-3">{{ $exams['subject_name'] }}</td>
                        <td class="px-4 py-3">{{ $exams['exam_date'] ? \Carbon\Carbon::parse($exams['exam_date'])->locale('fr')->translatedFormat('l') : '-' }}</td>
                        <td class="px-4 py-3">{{ $exams['exam_date'] ? \Carbon\Carbon::parse($exams['exam_date'])->locale('fr')->translatedFormat('d M Y') : '-' }}</td>
                        <td class="px-4 py-3">
                            {{ $exams['start_time'] ? \Carbon\Carbon::parse($exams['start_time'])->format('G\h:i\m\i\n') : '-'
                            }} -
                            {{ $exams['end_time'] ? \Carbon\Carbon::parse($exams['end_time'])->format('G\h:i\m\i\n') : '-'
                            }}
                        </td>
                        <td class="px-4 py-3">{{ $exams['room_number'] ? : '-' }}</td>
                        <td class="px-4 py-3">{{ $exams['full_marks'] ? : '-' }}</td>
                        <td class="px-4 py-3">{{ $exams['passing_marks'] ? : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-6 text-center text-gray-700 dark:text-bodydark1">
                            Aucun programme d'examen défini.
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
