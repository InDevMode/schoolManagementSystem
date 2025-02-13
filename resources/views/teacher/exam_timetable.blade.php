@extends('layouts.app')
@section('content')
<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between"
    >
        <h2 class="uppercase font-bold text-black dark:text-bodydark">
            Mon calendrier d'examens
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <span class="font-medium text-violet-600"><i class="fa-solid fa-calendar-days"></i></span>
                </li>
                <li>
                    /<a class="font-medium hover:text-violet-600 transition duration-300"
                        href="{{ url('teacher/dashboard') }}"> Dashboard</a>
                </li>
            </ol>
        </nav>
    </div>


    <div class="grid grid-cols-2 gap-4 mt-5">
        @forelse($getExamTimetable as $examTimetable)
        <div class="bg-white shadow-md rounded-lg border border-gray-300 p-4 dark:border-strokedark dark:bg-boxdark">
            <div class="flex justify-between font-bold text-md mb-3 bg-violet-100 dark:bg-violet-950 text-violet-800 dark:text-violet-100 px-4 py-3 rounded">
                 <span class="uppercase">Classe :</span>
                 <span class="font-semibold">{{ $examTimetable['class_name'] }}</span>
            </div>
            @forelse($examTimetable['getExams'] as $exams)
            <div class="flex justify-between font-bold text-md my-3 bg-violet-100 text-violet-800 dark:bg-gray-800 dark:text-gray-200 px-4 py-3 rounded">
                <span class="uppercase"> Evaluation :</span>
                <span class="font-semibold">{{ $exams['exam_name'] }}</span>
            </div>
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left border border-gray-200 dark:border-strokedark rounded-lg">
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
                    @forelse($exams['subjectSchedule'] as $exam)
                    <tr class="border-b hover:bg-violet-100 dark:hover:bg-gray-700">
                        <td class="px-4 py-3">{{ $exam['subject_name'] }}</td>
                        <td class="px-4 py-3">
                            {{ $exam['exam_date'] ? \Carbon\Carbon::parse($exam['exam_date'])->locale('fr')->translatedFormat('l') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $exam['exam_date'] ? \Carbon\Carbon::parse($exam['exam_date'])->locale('fr')->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $exam['exam_date'] ? \Carbon\Carbon::parse($exam['exam_date'])->locale('fr')->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $exam['start_time'] ? \Carbon\Carbon::parse($exam['start_time'])->format('G\h i\m\i\n') : '-' }}
                            -
                            {{ $exam['end_time'] ? \Carbon\Carbon::parse($exam['end_time'])->format('G\h i\m\i\n') : '-' }}
                        </td>
                        <td class="px-4 py-3">{{ $exam['room_number'] ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $exam['full_marks'] ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $exam['passing_marks'] ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-700 dark:text-bodydark1">
                            Aucun programme d'examen défini.
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @empty
            <div class="p-6 text-center text-gray-700 dark:text-bodydark1">
                Aucun examen défini pour cette classe.
            </div>
            @endforelse
        </div>
        @empty
        <div class="p-6 text-center text-gray-700 dark:text-bodydark1 col-span-2">
            Aucun emploi du temps d'examen disponible.
        </div>
        @endforelse
    </div>


</div>
@endsection
