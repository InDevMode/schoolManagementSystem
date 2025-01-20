@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 mt-14">
        <div class="mb-8 font-semibold space-x-2">
            <span class="text-violet-500 text-[25px]"><i class="fa-solid fa-house-chimney"></i></span>
            <span>/</span>
            <span class="">Dashboard</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-4">
            <div
                class="max-w-sm p-6 bg-violet-500 hover:bg-white border border-gray-300 rounded-lg shadow group transition-all duration-700">
                <span class="mb-3 group-hover:text-violet-500 text-white">
                    <i class="fa-solid fa-2x fa-user-tag"></i>
                </span>
                <a href="#">
                    <h5 class="mb-2 text-2xl font-semibold tracking-tight uppercase group-hover:text-gray-900 text-white">
                        Total des
                        Utilisateurs</h5>
                </a>
            </div>
            <div
                class="max-w-sm p-6 bg-red-500 hover:bg-white border border-gray-300 rounded-lg shadow group transition-all duration-700">
                <span class="mb-3 group-hover:text-red-500 text-white">
                    <i class="fa-solid fa-2x fa-user-secret"></i>
                </span>
                <a href="#">
                    <h5 class="mb-2 text-2xl font-semibold tracking-tight uppercase group-hover:text-gray-900 text-white">
                        Total des
                        Administrateus</h5>
                </a>
            </div>
            <div
                class="max-w-sm p-6 bg-amber-500 hover:bg-white border border-gray-300 rounded-lg shadow group transition-all duration-700">
                <span class="mb-3 group-hover:text-amber-500 text-white">
                    <i class="fa-solid fa-2x fa-user-tie"></i>
                </span>
                <a href="#">
                    <h5 class="mb-2 text-2xl font-semibold tracking-tight uppercase group-hover:text-gray-900 text-white">
                        Total des
                        Professeurs</h5>
                </a>
            </div>
            <div
                class="max-w-sm p-6 bg-emerald-500 hover:bg-white border border-gray-300 rounded-lg shadow group transition-all duration-700">
                <span class="mb-3 group-hover:text-emerald-500 text-white">
                    <i class="fa-solid fa-2x fa-user-graduate"></i>
                </span>
                <a href="#">
                    <h5 class="mb-2 text-2xl font-semibold tracking-tight uppercase group-hover:text-gray-900 text-white">
                        Total des
                        Elèves</h5>
                </a>
            </div>
            <div
                class="max-w-sm p-6 bg-pink-500 hover:bg-white border border-gray-300 rounded-lg shadow group transition-all duration-700">
                <span class="mb-3 group-hover:text-pink-500 text-white">
                    <i class="fa-solid fa-2x fa-person-breastfeeding"></i>
                </span>
                <a href="#">
                    <h5 class="mb-2 text-2xl font-semibold tracking-tight uppercase group-hover:text-gray-900 text-white">
                        Total des
                        Parents</h5>
                </a>
            </div>

        </div>
        <div class="flex items-center justify-center h-48 mb-4 rounded bg-gray-50">
            <p class="text-2xl text-gray-400 dark:text-gray-500">
                <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 18 18">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 1v16M1 9h16"/>
                </svg>
            </p>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="flex items-center justify-center rounded bg-gray-50 h-28">
                <p class="text-2xl text-gray-400 dark:text-gray-500">
                    <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 18 18">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M9 1v16M1 9h16"/>
                    </svg>
                </p>
            </div>
            <div class="flex items-center justify-center rounded bg-gray-50 h-28">
                <p class="text-2xl text-gray-400 dark:text-gray-500">
                    <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 18 18">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M9 1v16M1 9h16"/>
                    </svg>
                </p>
            </div>
            <div class="flex items-center justify-center rounded bg-gray-50 h-28">
                <p class="text-2xl text-gray-400 dark:text-gray-500">
                    <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 18 18">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M9 1v16M1 9h16"/>
                    </svg>
                </p>
            </div>
            <div class="flex items-center justify-center rounded bg-gray-50 h-28">
                <p class="text-2xl text-gray-400 dark:text-gray-500">
                    <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 18 18">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M9 1v16M1 9h16"/>
                    </svg>
                </p>
            </div>
        </div>
        <div class="flex items-center justify-center h-48 mb-4 rounded bg-gray-50">
            <p class="text-2xl text-gray-400 dark:text-gray-500">
                <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 18 18">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 1v16M1 9h16"/>
                </svg>
            </p>
        </div>

        <div data-dial-init class="fixed end-6 bottom-6 group">
            <div id="speed-dial-menu-default" class="flex flex-col items-center hidden mb-4 space-y-2">
                <a href="{{ url('admin/subject/list') }}" data-tooltip-target="tooltip-matres"
                   data-tooltip-placement="left"
                   class="flex justify-center items-center w-[52px] h-[52px] text-gray-500 hover:text-gray-900 bg-white rounded-full border border-gray-200 dark:border-gray-600 shadow-sm dark:hover:text-white dark:text-gray-400 hover:bg-gray-50 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:focus:ring-gray-400">
                    <i class="fa-solid fa-book-open-reader"></i>
                    <span class="sr-only">Matières</span>
                </a>
                <div id="tooltip-matres" role="tooltip"
                     class="absolute z-10 invisible inline-block w-auto px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                    Matières
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
                <a href="{{ url('admin/subject/list') }}" data-tooltip-target="tooltip-student"
                   data-tooltip-placement="left"
                   class="flex justify-center items-center w-[52px] h-[52px] text-gray-500 hover:text-gray-900 bg-white rounded-full border border-gray-200 dark:border-gray-600 shadow-sm dark:hover:text-white dark:text-gray-400 hover:bg-gray-50 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:focus:ring-gray-400">
                    <i class="fa-solid fa-user-graduate"></i>
                    <span class="sr-only">Elèves</span>
                </a>
                <div id="tooltip-student" role="tooltip"
                     class="absolute z-10 invisible inline-block w-auto px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                    Elèves
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
                <a href="{{ url('admin/class/list') }}" data-tooltip-target="tooltip-class"
                   data-tooltip-placement="left"
                   class="flex justify-center items-center w-[52px] h-[52px] text-gray-500 hover:text-gray-900 bg-white rounded-full border border-gray-200 hover:bg-gray-50 focus:ring-4 focus:ring-gray-300 focus:outline-none">
                    <i class="fa-solid fa-landmark"></i>
                    <span class="sr-only">Classes</span>
                </a>
                <div id="tooltip-class" role="tooltip"
                     class="absolute z-10 invisible inline-block w-auto px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                    Classes
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
                <a href="{{ url('admin/assign_subject/list') }}" data-tooltip-target="tooltip-assignations"
                   data-tooltip-placement="left"
                   class="flex justify-center items-center w-[52px] h-[52px] text-gray-500 hover:text-gray-900 bg-white rounded-full border border-gray-200 shadow-sm hover:bg-gray-50 focus:ring-4 focus:ring-gray-300 focus:outline-none">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span class="sr-only">Assignations</span>
                </a>
                <div id="tooltip-assignations" role="tooltip"
                     class="absolute z-10 invisible inline-block w-auto px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                    Assignations
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
                <a href="{{ url('admin/admin/list') }}" data-tooltip-target="tooltip-administrates" data-tooltip-placement="left"
                   class="flex justify-center items-center w-[52px] h-[52px] text-gray-500 hover:text-gray-900 bg-white rounded-full border border-gray-200 shadow-sm hover:bg-gray-50 focus:ring-4 focus:ring-gray-300 focus:outline-none">
                    <i class="fa-solid fa-user-secret"></i>
                    <span class="sr-only">Administrateurs</span>
                </a>
                <div id="tooltip-administrates" role="tooltip"
                     class="absolute z-10 invisible inline-block w-auto px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                    Administrateurs
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </div>
            <button type="button" data-dial-toggle="speed-dial-menu-default" aria-controls="speed-dial-menu-default"
                    aria-expanded="false"
                    class="flex items-center justify-center text-white bg-violet-500 rounded-full w-14 h-14 hover:bg-violet-600 focus:ring-4 focus:ring-violet-300 focus:outline-non">
                <span class="transition-transform group-hover:rotate-45"><i class="fa-solid fa-2x fa-plus"></i></span>
                <span class="sr-only">Open actions menu</span>
            </button>
        </div>


    </div>
</div>
@endsection

