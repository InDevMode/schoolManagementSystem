@extends('layouts.app')
@section('content')
<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between"
    >
        <h2 class="uppercase font-bold text-black dark:text-bodydark">
            Les résultats d'examens <span class="text-violet-500">{{ $getStudent->name }} {{ $getStudent->last_name }}</span>
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <span class="font-medium text-violet-600"><i class="fa-solid fa-square-poll-horizontal"></i></span>
                </li>
                <li>
                    /<a class="font-medium hover:text-violet-600 transition duration-300"
                        href="{{ url('parent/dashboard') }}"> Dashboard</a>
                </li>
            </ol>
        </nav>
    </div>

    @foreach($getExamResultStudent as $index => $examResult)
    <div class="bg-white rounded">
        <div
            class="px-6 font-bold text-md bg-violet-100 text-violet-800 dark:text-violet-100 dark:bg-violet-950 py-2 rounded">
            {{ $examResult['exam_name'] }}
        </div>
        <div class="relative overflow rounded-lg z-10">
            <table class="w-full text-[12px] text-left rtl:text-right text-white dark:text-white">
                <thead
                    class="rounded-sm bg-violet-600 uppercase text-white dark:bg-meta-4"
                >
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Matière
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Travail de classe
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Travail de maison
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Travail d'examens
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Travaux d'essai
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Note obtenue
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Note de passage / totale
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Résultat
                    </th>
                </tr>
                </thead>
              <tbody class="z-20 bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @if(empty($examResult['subject']))
                <tr class="text-center text-gray-700 dark:text-bodydark1">
                    <td colspan="7" class="py-3"> Aucun résultat examen trouvé.</td>
                </tr>
                @else
                @php
                $total_class_work = 0;
                $total_home_work = 0;
                $total_test_work = 0;
                $total_exam_work = 0;
                $total_score = 0;
                $passing_marks = 0;
                $full_marks = 0;
                @endphp
                @foreach($examResult['subject'] as $subjectValue)
                @php
                $total_class_work = $total_class_work + $subjectValue['class_work'];
                $total_home_work = $total_home_work + $subjectValue['home_work'];
                $total_test_work = $total_test_work + $subjectValue['test_work'];
                $total_exam_work = $total_exam_work + $subjectValue['exam_work'];
                $total_score = $total_score + $subjectValue['score_marks'];
                $passing_marks = $passing_marks + $subjectValue['passing_marks'];
                $full_marks = $full_marks + $subjectValue['full_marks'];
                $percentage = ($total_score * 100) / $full_marks;
                $getGrade = \App\Models\MarksGradeModel::getGrade($percentage);
                @endphp
               <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 transition-colors">
                    <td class="font-semibold px-6 py-3">
                        {{ $subjectValue['subject_name'] }}
                    </td>
                    <td class="px-6 py-3">
                        {{ $subjectValue['class_work'] }}
                    </td>
                    <td class="px-6 py-3">
                        {{ $subjectValue['home_work'] }}
                    </td>
                    <td class="px-6 py-3">
                        {{ $subjectValue['test_work'] }}
                    </td>
                    <td class="px-6 py-3">
                        {{ $subjectValue['exam_work'] }}
                    </td>
                    <td class="font-semibold px-6 py-3">
                        {{ $subjectValue['score_marks'] }}
                    </td>
                    <td class="px-6 py-3">
                        {{ $subjectValue['passing_marks'] }} / {{ $subjectValue['full_marks'] }}
                    </td>
                    <td class="px-6 py-3">
                        @if($subjectValue['score_marks'] >= $subjectValue['passing_marks'])
                        <span class="bg-emerald-100 text-emerald-700 rounded-full px-3 py-1">Validé</span>
                        @else
                        <span class="bg-red-100 text-red-700 rounded-full px-3 py-1">Non validé</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                <tr class="hover:bg-violet-100 text-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-6 py-3">
                        <div class="flex flex-col space-y-2">
                            <span class="font-semibold uppercase">
                                Pourcentage : {{ round(($total_score * 100) / $full_marks, 2) }} %
                            </span>

                            <hr class="border-t border-gray-300">

                            <span class="text-sm text-gray-700 dark:text-gray-200">
                                Grade : {{ $getGrade }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-3">
                        <span class="font-semibold uppercase">{{ $total_class_work }} </span>
                    </td>
                    <td class="px-6 py-3">
                        <span class="font-semibold uppercase">{{ $total_home_work }} </span>
                    </td>
                    <td class="px-6 py-3">
                        <span class="font-semibold uppercase">{{ $total_test_work }} </span>
                    </td>
                    <td class="px-6 py-3">
                        <span class="font-semibold uppercase">{{ $total_exam_work }} </span>
                    </td>
                    <td class="px-6 py-3">
                        <span class="font-semibold uppercase">{{ $total_score }} </span>
                    </td>
                    <td class="px-6 py-3">
                        <span class="font-semibold uppercase">{{ $passing_marks }} / {{ $full_marks }} </span>
                    </td>
                    <td class="px-6 py-3">
                        @if($subjectValue['score_marks'] >= $subjectValue['passing_marks'])
                        <span class="bg-emerald-100 text-emerald-700 rounded-full px-3 py-1">Validé</span>
                        @else
                        <span class="bg-red-100 text-red-700 rounded-full px-3 py-1">Non validé</span>
                        @endif
                    </td>
                </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>
@endsection
