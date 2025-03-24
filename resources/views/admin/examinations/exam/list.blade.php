@extends('layouts.app')
@section('content')
<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between"
    >
        <h2 class="uppercase font-bold text-black dark:text-bodydark">
            Liste des évaluations
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <span class="font-medium text-violet-600"><i class="fa-solid fa-flask-vial"></i></span>
                </li>
                <li>
                    /<a class="font-medium hover:text-violet-600 transition duration-300"
                        href="{{ url('admin/dashboard') }}"> Dashboard</a>
                </li>
                <li>
                    /<a class="font-medium hover:text-violet-600 transition duration-300"
                        href="{{ url('admin/examinations/exam/add') }}"> Créer une évaluation</a>
                </li>
            </ol>
        </nav>
    </div>
    @include('message')
    <div class="my-5">
        {{ $getExams->links('vendor.pagination.tailwind') }}
    </div>
    <div
        class="rounded-lg border border-stroke bg-white px-5 pb-2.5 pt-6 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1"
    >
        <form action="" method="get">
            <div class="mb-4.5 grid grid-cols-2 xl:grid-cols-4 gap-3 items-center">
                <div class="w-full">
                    <input
                        type="text" id="exam_name" name="exam_name" value="{{ Request::get('exam_name') }}"
                        placeholder="évaluation..."
                        class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                    />
                </div>
                <div class="w-full">
                    <input
                        type="text" id="note" name="note" value="{{ Request::get('note') }}"
                        placeholder="note..."
                        class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                    />
                </div>
                <div class="w-full">
                    <input
                        type="text" id="created_name" name="created_name" value="{{ Request::get('created_name') }}"
                        placeholder="crée par..."
                        class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                    />
                </div>
                <div class="w-full">
                    <div class="relative">
                        <input id="created_at" name="created_at" value="{{ Request::get('created_at') }}"
                               class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                               placeholder="creation..."
                               data-class="flatpickr-right"
                        />

                        <div
                            class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center"
                        >
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 18 18"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M15.7504 2.9812H14.2879V2.36245C14.2879 2.02495 14.0066 1.71558 13.641 1.71558C13.2754 1.71558 12.9941 1.99683 12.9941 2.36245V2.9812H4.97852V2.36245C4.97852 2.02495 4.69727 1.71558 4.33164 1.71558C3.96602 1.71558 3.68477 1.99683 3.68477 2.36245V2.9812H2.25039C1.29414 2.9812 0.478516 3.7687 0.478516 4.75308V14.5406C0.478516 15.4968 1.26602 16.3125 2.25039 16.3125H15.7504C16.7066 16.3125 17.5223 15.525 17.5223 14.5406V4.72495C17.5223 3.7687 16.7066 2.9812 15.7504 2.9812ZM1.77227 8.21245H4.16289V10.9968H1.77227V8.21245ZM5.42852 8.21245H8.38164V10.9968H5.42852V8.21245ZM8.38164 12.2625V15.0187H5.42852V12.2625H8.38164V12.2625ZM9.64727 12.2625H12.6004V15.0187H9.64727V12.2625ZM9.64727 10.9968V8.21245H12.6004V10.9968H9.64727ZM13.8379 8.21245H16.2285V10.9968H13.8379V8.21245ZM2.25039 4.24683H3.71289V4.83745C3.71289 5.17495 3.99414 5.48433 4.35977 5.48433C4.72539 5.48433 5.00664 5.20308 5.00664 4.83745V4.24683H13.0504V4.83745C13.0504 5.17495 13.3316 5.48433 13.6973 5.48433C14.0629 5.48433 14.3441 5.20308 14.3441 4.83745V4.24683H15.7504C16.0316 4.24683 16.2566 4.47183 16.2566 4.75308V6.94683H1.77227V4.75308C1.77227 4.47183 1.96914 4.24683 2.25039 4.24683ZM1.77227 14.5125V12.2343H4.16289V14.9906H2.25039C1.96914 15.0187 1.77227 14.7937 1.77227 14.5125ZM15.7504 15.0187H13.8379V12.2625H16.2285V14.5406C16.2566 14.7937 16.0316 15.0187 15.7504 15.0187Z"
                                    fill="#64748B"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="w-full">
                    <div class="relative">
                        <input id="updated_at" name="updated_at" value="{{ Request::get('updated_at') }}"
                               class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                               placeholder="modification..."
                               data-class="flatpickr-right"
                        />
                        <div
                            class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center"
                        >
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 18 18"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M15.7504 2.9812H14.2879V2.36245C14.2879 2.02495 14.0066 1.71558 13.641 1.71558C13.2754 1.71558 12.9941 1.99683 12.9941 2.36245V2.9812H4.97852V2.36245C4.97852 2.02495 4.69727 1.71558 4.33164 1.71558C3.96602 1.71558 3.68477 1.99683 3.68477 2.36245V2.9812H2.25039C1.29414 2.9812 0.478516 3.7687 0.478516 4.75308V14.5406C0.478516 15.4968 1.26602 16.3125 2.25039 16.3125H15.7504C16.7066 16.3125 17.5223 15.525 17.5223 14.5406V4.72495C17.5223 3.7687 16.7066 2.9812 15.7504 2.9812ZM1.77227 8.21245H4.16289V10.9968H1.77227V8.21245ZM5.42852 8.21245H8.38164V10.9968H5.42852V8.21245ZM8.38164 12.2625V15.0187H5.42852V12.2625H8.38164V12.2625ZM9.64727 12.2625H12.6004V15.0187H9.64727V12.2625ZM9.64727 10.9968V8.21245H12.6004V10.9968H9.64727ZM13.8379 8.21245H16.2285V10.9968H13.8379V8.21245ZM2.25039 4.24683H3.71289V4.83745C3.71289 5.17495 3.99414 5.48433 4.35977 5.48433C4.72539 5.48433 5.00664 5.20308 5.00664 4.83745V4.24683H13.0504V4.83745C13.0504 5.17495 13.3316 5.48433 13.6973 5.48433C14.0629 5.48433 14.3441 5.20308 14.3441 4.83745V4.24683H15.7504C16.0316 4.24683 16.2566 4.47183 16.2566 4.75308V6.94683H1.77227V4.75308C1.77227 4.47183 1.96914 4.24683 2.25039 4.24683ZM1.77227 14.5125V12.2343H4.16289V14.9906H2.25039C1.96914 15.0187 1.77227 14.7937 1.77227 14.5125ZM15.7504 15.0187H13.8379V12.2625H16.2285V14.5406C16.2566 14.7937 16.0316 15.0187 15.7504 15.0187Z"
                                    fill="#64748B"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="w-full">
                    <button
                        class="flex w-full justify-between items-center rounded-lg bg-violet-600 px-3 py-2.5 font-medium text-gray hover:bg-opacity-90"
                    >
                        Rechercher
                        <span class="inline-flex items-center text-sm text-gray-900">
                                    <i class="fa-solid fa-search text-white"></i>
                                </span>
                    </button>
                </div>
                <div class="w-full">
                    <a href="{{ url('admin/examinations/exam/list') }}"
                       class="flex w-full justify-center rounded-lg bg-gray-500 px-3 py-2.5 font-medium text-gray hover:bg-opacity-90"
                    >
                        Réïnitialisez
                    </a>
                </div>
            </div>
        </form>
        <div class="relative overflow rounded-lg z-10">
            <table class="w-full text-[12px] text-left rtl:text-right text-white dark:text-white">
                <thead
                    class="rounded-sm bg-violet-600 uppercase text-white dark:bg-meta-4"
                >
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Examen
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Note
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Crée par
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Date de Création
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Date de Modification
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($getExams as $index => $exam)
                <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition duration-300 border-b dark:border-gray-600 hover:border-violet-400 dark:text-gray-200 text-gray-500">
                    <td class="px-6 py-3">
                        {{ $exam -> name }}
                    </td>
                    <td class="px-6 py-3 font-semibold">
                        {{ $exam -> note }}
                    </td>
                    <td class="px-6 py-3">
                        {{ $exam-> created_name }}
                    </td>
                    <td class="px-6 py-3">
                        {{ \Carbon\Carbon::parse($exam->created_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}
                    </td>
                    <td class="px-6 py-3">
                        {{ \Carbon\Carbon::parse($exam->updated_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}
                    </td>
                    <td class="px-6 py-3">
                        <div class="relative inline-block text-left" x-data="{ open: false }">
                            <div>
                                <button
                                    type="button"
                                    class="group inline-flex w-full justify-center gap-x-1.5 rounded-lg shadow-md bg-white dark:bg-gray-800 border dark:border-gray-600 dark:hover:text-violet-600 px-3 py-2 text-sm font-semibold text-gray-700 hover:text-violet-600 dark:text-gray-200 hover:bg-gray-100"
                                    @click="open = !open"
                                    id="menu-button"
                                    aria-expanded="true"
                                    aria-haspopup="true">
                                    Actions
                                    <svg class="-mr-1 size-5 group-hover:text-violet-600 text-gray-400"
                                         viewBox="0 0 20 20" fill="currentColor"
                                         aria-hidden="true">
                                        <path fill-rule="evenodd"
                                              d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                            <div
                                class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-md bg-white dark:bg-gray-800 ring-1 shadow-lg ring-black/5 focus:outline-hidden"
                                role="menu"
                                aria-orientation="vertical"
                                aria-labelledby="menu-button"
                                tabindex="{{ $index + 1 }}"
                                x-show="open"
                                @click.away="open = false"
                                x-transition
                            >
                                <div class="py-1">
                                    <a href="{{ url('admin/examinations/exam/edit', $exam->id) }}"
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:text-emerald-400 dark:hover:text-emerald-400"
                                       role="menuitem">Modifier</a>
                                    <button @click="openModal = true; deleteId = '{{ $exam->id }}'; deleteName = '{{ $exam->name }}'"
                                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:text-red-400 dark:hover:text-red-400">
                                        Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
                @include('admin.examinations.exam.delete')
                @if($getExams->isEmpty())
                <tr class="text-center text-gray-700 dark:text-bodydark1">
                    <td colspan="6" class="py-3"> Aucun examen trouvé.</td>
                </tr>
                @endif
                <tr class="">
                    <td colspan="6"
                        class="px-6 py-3"
                    >
                        <div class="mt-3 mb-3 flex items-center justify-between">
                            <h2 class="text-title-sm uppercase font-bold text-black dark:text-white">
                                Total
                            </h2>
                            <nav>
                                <ol class="flex items-center bg-white shadow-lg border border-gray-200 dark:border-gray-600 w-fit dark:bg-black py-2 px-8 rounded">
                                    <li>
                                        <p class="text-md font-semibold text-gray-700 dark:text-gray-200">
                                            {{ $getExams->total() }}</p>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

<script>
    function toggleMenu(event, index) {
        event.stopPropagation();
        document.querySelectorAll('.relative .hidden').forEach(menu => menu.classList.add('hidden'));
        const menu = document.getElementById('dropdown-menu-' + index);
        menu.classList.toggle('hidden');
    }

    document.addEventListener('click', function () {
        document.querySelectorAll('.relative .hidden').forEach(menu => menu.classList.add('hidden'));
    });
</script>


