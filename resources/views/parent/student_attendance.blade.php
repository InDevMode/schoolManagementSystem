@extends('layouts.app')
@section('content')
    <div class="m-5">
        <!-- Breadcrumb Start -->
        <div class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between">
            <h2 class="uppercase font-bold text-black dark:text-bodydark">
               Le rapport de présence de<span class="text-violet-600 bg-violet-100 rounded-full px-4 py-2 ms-5">{{ $getStudent->name }} {{ $getStudent->last_name }}</span>
            </h2>
            <nav>
                <ol class="flex items-center gap-2">
                    <li>
                        <span class="font-medium text-violet-600"><iconify-icon
                                icon="mdi:calendar-check"></iconify-icon></span>
                    </li>
                    <li>
                        /<a class="font-medium hover:text-violet-600 transition duration-300"
                            href="{{ url('student/dashboard') }}"> Dashboard</a>
                    </li>
                </ol>
            </nav>
        </div>
        @include('message')
        <div class="my-5">
            {{ $getParentStudentAttendance->links('vendor.pagination.tailwind') }}
        </div>
        <div
            class="rounded-lg border border-stroke bg-white px-5 pb-2.5 pt-6 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
            <form action="" method="get">
                <div class="mb-4.5 grid grid-cols-2 xl:grid-cols-5 gap-3 items-center">
                    <div class="w-full">
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-gray-100 dark:bg-form-input">
                            <select id="class_id" name="class_id"
                                class="relative z-20 w-full appearance-none rounded-lg border border-stroke bg-gray-100 px-5 py-2.5 outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                :class="isOptionSelected && 'text-black dark:text-white'" @change="isOptionSelected = true">
                                <option selected disabled value="" class="text-body">Choisissez une classe</option>
                                @foreach($getClassStudent as $class)
                                    <option value="{{ $class->class_id }}" class="text-body" {{ (Request::get('class_id') == $class->class_id) ? 'selected' : '' }}>
                                        {{ $class->class_name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="absolute right-4 top-1/2 z-30 -translate-y-1/2">
                                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g opacity="0.8">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M5.29289 8.29289C5.68342 7.90237 6.31658 7.90237 6.70711 8.29289L12 13.5858L17.2929 8.29289C17.6834 7.90237 18.3166 7.90237 18.7071 8.29289C19.0976 8.68342 19.0976 9.31658 18.7071 9.70711L12.7071 15.7071C12.3166 16.0976 11.6834 16.0976 11.2929 15.7071L5.29289 9.70711C4.90237 9.31658 4.90237 8.68342 5.29289 8.29289Z"
                                            fill=""></path>
                                    </g>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="relative">
                            <input id="start_attendance_date" name="start_attendance_date" value="{{ Request::get('start_attendance_date') }}"
                                class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                placeholder="date de début..." data-class="flatpickr-right" required />

                            <div class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M15.7504 2.9812H14.2879V2.36245C14.2879 2.02495 14.0066 1.71558 13.641 1.71558C13.2754 1.71558 12.9941 1.99683 12.9941 2.36245V2.9812H4.97852V2.36245C4.97852 2.02495 4.69727 1.71558 4.33164 1.71558C3.96602 1.71558 3.68477 1.99683 3.68477 2.36245V2.9812H2.25039C1.29414 2.9812 0.478516 3.7687 0.478516 4.75308V14.5406C0.478516 15.4968 1.26602 16.3125 2.25039 16.3125H15.7504C16.7066 16.3125 17.5223 15.525 17.5223 14.5406V4.72495C17.5223 3.7687 16.7066 2.9812 15.7504 2.9812ZM1.77227 8.21245H4.16289V10.9968H1.77227V8.21245ZM5.42852 8.21245H8.38164V10.9968H5.42852V8.21245ZM8.38164 12.2625V15.0187H5.42852V12.2625H8.38164V12.2625ZM9.64727 12.2625H12.6004V15.0187H9.64727V12.2625ZM9.64727 10.9968V8.21245H12.6004V10.9968H9.64727ZM13.8379 8.21245H16.2285V10.9968H13.8379V8.21245ZM2.25039 4.24683H3.71289V4.83745C3.71289 5.17495 3.99414 5.48433 4.35977 5.48433C4.72539 5.48433 5.00664 5.20308 5.00664 4.83745V4.24683H13.0504V4.83745C13.0504 5.17495 13.3316 5.48433 13.6973 5.48433C14.0629 5.48433 14.3441 5.20308 14.3441 4.83745V4.24683H15.7504C16.0316 4.24683 16.2566 4.47183 16.2566 4.75308V6.94683H1.77227V4.75308C1.77227 4.47183 1.96914 4.24683 2.25039 4.24683ZM1.77227 14.5125V12.2343H4.16289V14.9906H2.25039C1.96914 15.0187 1.77227 14.7937 1.77227 14.5125ZM15.7504 15.0187H13.8379V12.2625H16.2285V14.5406C16.2566 14.7937 16.0316 15.0187 15.7504 15.0187Z"
                                        fill="#64748B" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="relative">
                            <input id="end_attendance_date" name="end_attendance_date" value="{{ Request::get('end_attendance_date') }}"
                                class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                placeholder="date de fin..." data-class="flatpickr-right" required />

                            <div class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M15.7504 2.9812H14.2879V2.36245C14.2879 2.02495 14.0066 1.71558 13.641 1.71558C13.2754 1.71558 12.9941 1.99683 12.9941 2.36245V2.9812H4.97852V2.36245C4.97852 2.02495 4.69727 1.71558 4.33164 1.71558C3.96602 1.71558 3.68477 1.99683 3.68477 2.36245V2.9812H2.25039C1.29414 2.9812 0.478516 3.7687 0.478516 4.75308V14.5406C0.478516 15.4968 1.26602 16.3125 2.25039 16.3125H15.7504C16.7066 16.3125 17.5223 15.525 17.5223 14.5406V4.72495C17.5223 3.7687 16.7066 2.9812 15.7504 2.9812ZM1.77227 8.21245H4.16289V10.9968H1.77227V8.21245ZM5.42852 8.21245H8.38164V10.9968H5.42852V8.21245ZM8.38164 12.2625V15.0187H5.42852V12.2625H8.38164V12.2625ZM9.64727 12.2625H12.6004V15.0187H9.64727V12.2625ZM9.64727 10.9968V8.21245H12.6004V10.9968H9.64727ZM13.8379 8.21245H16.2285V10.9968H13.8379V8.21245ZM2.25039 4.24683H3.71289V4.83745C3.71289 5.17495 3.99414 5.48433 4.35977 5.48433C4.72539 5.48433 5.00664 5.20308 5.00664 4.83745V4.24683H13.0504V4.83745C13.0504 5.17495 13.3316 5.48433 13.6973 5.48433C14.0629 5.48433 14.3441 5.20308 14.3441 4.83745V4.24683H15.7504C16.0316 4.24683 16.2566 4.47183 16.2566 4.75308V6.94683H1.77227V4.75308C1.77227 4.47183 1.96914 4.24683 2.25039 4.24683ZM1.77227 14.5125V12.2343H4.16289V14.9906H2.25039C1.96914 15.0187 1.77227 14.7937 1.77227 14.5125ZM15.7504 15.0187H13.8379V12.2625H16.2285V14.5406C16.2566 14.7937 16.0316 15.0187 15.7504 15.0187Z"
                                        fill="#64748B" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-gray-100 dark:bg-form-input">
                            <select id="attendance_type" name="attendance_type"
                                class="relative z-20 w-full appearance-none rounded-lg border border-stroke bg-gray-100 px-5 py-2.5 outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                :class="isOptionSelected && 'text-black dark:text-white'" @change="isOptionSelected = true">
                                <option selected disabled value="" class="text-body">Choisissez la présence</option>
                                <option value="1" class="text-body" {{ (Request::get('attendance_type') == 1) ? 'selected' : '' }}>Présent(e)</option>
                                <option value="2" class="text-body" {{ (Request::get('attendance_type') == 2) ? 'selected' : '' }}>Retard</option>
                                <option value="3" class="text-body" {{ (Request::get('attendance_type') == 3) ? 'selected' : '' }}>Absent(e)</option>
                                <option value="4" class="text-body" {{ (Request::get('attendance_type') == 4) ? 'selected' : '' }}>Demi-journée</option>
                            </select>
                            <span class="absolute right-4 top-1/2 z-30 -translate-y-1/2">
                                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g opacity="0.8">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M5.29289 8.29289C5.68342 7.90237 6.31658 7.90237 6.70711 8.29289L12 13.5858L17.2929 8.29289C17.6834 7.90237 18.3166 7.90237 18.7071 8.29289C19.0976 8.68342 19.0976 9.31658 18.7071 9.70711L12.7071 15.7071C12.3166 16.0976 11.6834 16.0976 11.2929 15.7071L5.29289 9.70711C4.90237 9.31658 4.90237 8.68342 5.29289 8.29289Z"
                                            fill=""></path>
                                    </g>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="w-full">
                        <button
                            class="flex w-full justify-between items-center rounded-lg bg-violet-600 px-3 py-2.5 font-medium text-gray hover:bg-opacity-90">
                            Rechercher
                            <span class="inline-flex items-center text-sm text-gray-900">
                                <i class="fa-solid fa-search text-white"></i>
                            </span>
                        </button>
                    </div>
                    <div class="w-full">
                        <a href="{{ url('parent/my_student/attendance', $getStudent->id) }}"
                            class="flex w-full justify-center rounded-lg bg-gray-500 px-3 py-2.5 font-medium text-gray hover:bg-opacity-90">
                            Réïnitialisez
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="mt-5">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-white uppercase bg-violet-500 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Classe
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Statut
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Date de présence
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Date de création
                            </th </tr>
                    </thead>
                    <tbody>
                        @if(!empty($getParentStudentAttendance) && $getParentStudentAttendance->count() > 0)
                            @foreach($getParentStudentAttendance as $parentStudentAttendance)
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <td class="px-6 py-4">
                                                {{ $parentStudentAttendance->class_name }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="
                                                                                          {{ $parentStudentAttendance->attendance_type == 1 ? 'bg-emerald-200 text-emerald-800 rounded-full px-2 py-1' :
                                ($parentStudentAttendance->attendance_type == 2 ? 'bg-yellow-200 text-yellow-800 rounded-full px-2 py-1' :
                                    ($parentStudentAttendance->attendance_type == 3 ? 'bg-red-200 text-red-800 rounded-full px-2 py-1' :
                                        ($parentStudentAttendance->attendance_type == 4 ? 'bg-blue-200 text-blue-800 rounded-full px-2 py-1' : ''))) }}">
                                                    {{ $parentStudentAttendance->attendance_type == 1 ? 'Présent(e)' :
                                ($parentStudentAttendance->attendance_type == 2 ? 'Retard' :
                                    ($parentStudentAttendance->attendance_type == 3 ? 'Absent(e)' :
                                        ($parentStudentAttendance->attendance_type == 4 ? 'Demi-journée' : 'Non défini'))) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ \Carbon\Carbon::parse($parentStudentAttendance->attendance_date)->locale('fr')->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ \Carbon\Carbon::parse($parentStudentAttendance->created_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}
                                            </td>
                                        </tr>
                            @endforeach
                            @if(empty($getParentStudentAttendance) && !$getParentStudentAttendance->isEmpty())
                                <tr class="p-4 text-gray-700 font-semibold rounded-lg shadow-md text-center">
                                    <td colspan="8" class="px-6 py-3"> Aucune donnée trouvée.</td>
                                </tr>
                            @endif
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td colspan="4" class="px-6 py-3">
                                    <div class="mt-3 mb-3 flex items-center justify-between">
                                        <h2 class="text-title-sm uppercase font-bold text-black dark:text-white">
                                            Total
                                        </h2>
                                        <nav>
                                            <ol
                                                class="flex items-center bg-white shadow-lg border border-gray-200 dark:border-gray-600 w-fit dark:bg-black py-2 px-8 rounded">
                                                <li>
                                                    <p class="text-md font-semibold text-gray-700 dark:text-gray-200">
                                                        {{ $getParentStudentAttendance->total() }}
                                                    </p>
                                                </li>
                                            </ol>
                                        </nav>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr class="p-4 text-gray-700 font-semibold rounded-lg shadow-md text-center">
                                <td colspan="8" class="px-6 py-3"> Aucune donnée trouvée.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

<script>

</script>
