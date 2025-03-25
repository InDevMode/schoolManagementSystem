@extends('layouts.app')
@section('content')
<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between"
    >
        <h2 class="uppercase font-bold text-black dark:text-bodydark">
            Mes résultats d'examens
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <span class="font-medium text-violet-600"><i class="fa-solid fa-square-poll-horizontal"></i></span>
                </li>
                <li>
                    /<a class="font-medium hover:text-violet-600 transition duration-300"
                        href="{{ url('student/dashboard') }}"> Dashboard</a>
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
                        Score
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Note de passage & totale
                    </th>
                </tr>
                </thead>
                <tbody>
                @if(empty($examResult['subject']))
                <tr class="text-center text-gray-700 dark:text-bodydark1">
                    <td colspan="7" class="py-3"> Aucun résultat examen trouvé.</td>
                </tr>
                @else
                @foreach($examResult['subject'] as $subjectValue)
                <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition duration-300 border-b dark:border-gray-600 hover:border-violet-400 dark:text-gray-200 text-gray-500">
                    <td class="px-6 py-3">
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
                    <td class="px-6 py-3">
                        {{ $subjectValue['score_marks'] }}
                    </td>
                    <td class="px-6 py-3">
                        {{ $subjectValue['passing_marks'] }} / {{ $subjectValue['full_marks'] }}
                    </td>
                </tr>
                @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
    @endforeach


</div>
@endsection
