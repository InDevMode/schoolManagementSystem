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
    <div class="font-bold text-md mb-3 text-center bg-violet-100 text-violet-800 dark:text-violet-100 dark:bg-violet-950 py-2 rounded">
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
                    Type
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($examResult['subject'] as $subjectValue)
            <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition duration-300 border-b dark:border-gray-600 hover:border-violet-400 dark:text-gray-200 text-gray-500">
                <td class="px-6 py-3">
                    {{ $subjectValue }}
                </td>
                <td class="px-6 py-3">
                    {{ $subjectValue }}
                </td>
            </tr>
            @endforeach
            @if($examResult['subject']->isEmpty())
            <tr class="text-center text-gray-700 dark:text-bodydark1">
                <td colspan="6" class="py-3"> Aucun résultat examen trouvé.</td>
            </tr>
            @endif
            </tbody>
        </table>
    </div>
    @endforeach
</div>
@endsection
