@extends('layouts.app')
@section('content')
<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
    >
        <h2 class="text-title-md uppercase font-bold text-black dark:text-white">
            Liste des administrateurs
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <a class="font-medium" href="{{ url('admin/dashboard') }}">Dashboard</a>
                </li>
            </ol>
        </nav>
    </div>
    <div class="">
        <div class="mt-4">
            {{ $getAdmin->links('vendor.pagination.tailwind') }}
        </div>
    </div>
    <div
        class="rounded-sm border border-stroke bg-white px-5 pb-2.5 pt-6 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1"
    >
        <form action="" method="get">
                <div class="mb-4.5 grid grid-cols-8 gap-3 items-center">
                    <div class="w-full xl:w-1/8">
                        <input
                            type="text" id="name" name="name" value="{{ Request::get('name') }}"
                            placeholder="Recherchez par le nom"
                            class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                        />
                    </div>
                    <div class="w-full xl:w-1/8">
                        <input
                            type="text" id="last_name" name="last_name" value="{{ Request::get('last_name') }}"
                            placeholder="Recherchez par le prénom"
                            class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                        />
                    </div>
                    <div class="w-full xl:w-1/8">
                        <input
                            type="email" id="email" name="email" value="{{ Request::get('email') }}"
                            placeholder="Recherchez par email"
                            class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                        />
                    </div>
                    <div class="w-full xl:w-1/8">
                        <div
                            x-data="{ isOptionSelected: false }"
                            class="relative z-20 bg-transparent dark:bg-form-input"
                        >
                            <select id="status" name="status"
                                class="relative z-20 w-full appearance-none rounded border border-stroke bg-transparent px-5 py-3 outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary"
                                :class="isOptionSelected && 'text-black dark:text-white'"
                                @change="isOptionSelected = true"
                            >
                                <option value="" class="text-body">
                                   Filtrez par statut
                                </option>
                                <option  value="0" class="text-body" {{ Request::get('status') == '0' ? 'selected' : '' }}>Inactif</option>
                                <option value="1" class="text-body" {{ Request::get('status') == '1' ? 'selected' : '' }}>Actif</option>
                            </select>
                            <span
                                class="absolute right-4 top-1/2 z-30 -translate-y-1/2"
                            >
                            <svg
                                class="fill-current"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                              <g opacity="0.8">
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M5.29289 8.29289C5.68342 7.90237 6.31658 7.90237 6.70711 8.29289L12 13.5858L17.2929 8.29289C17.6834 7.90237 18.3166 7.90237 18.7071 8.29289C19.0976 8.68342 19.0976 9.31658 18.7071 9.70711L12.7071 15.7071C12.3166 16.0976 11.6834 16.0976 11.2929 15.7071L5.29289 9.70711C4.90237 9.31658 4.90237 8.68342 5.29289 8.29289Z"
                                    fill=""
                                ></path>
                              </g>
                            </svg>
                          </span>
                        </div>
                    </div>
                    <div class="w-full xl:w-1/8">
                        <div class="relative">
                            <input
                                class="form-datepicker w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                placeholder="j / m / a"
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
                    <div class="w-full xl:w-1/8">
                        <div class="relative">
                            <input
                                class="form-datepicker w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                placeholder="j / m / a"
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
                    <div class="w-full xl:w-1/8">
                            <button
                                class="flex w-full justify-between items-center rounded bg-violet-600 p-3 font-medium text-gray hover:bg-opacity-90"
                            >
                            Rechercher
                                <span class="inline-flex items-center text-sm text-gray-900">
                                    <i class="fa-solid fa-search text-white"></i>
                                </span>
                        </button>
                    </div>
                    <div class="w-full xl:w-1/8">
                        <a href="{{ url('admin/admin/list') }}"
                            class="flex w-full justify-center rounded bg-bodydark2 p-3 font-medium text-gray hover:bg-opacity-90"
                        >
                            Réïnitialisez les filtres
                        </a>
                    </div>
                </div>
        </form>

        <div class="flex flex-col">
            <div
                class="grid grid-cols-3 rounded-sm bg-gray-2 dark:bg-meta-4 sm:grid-cols-7"
            >
                <div class="p-2.5 xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Nom</h5>
                </div>
                <div class="p-2.5 text-center xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Prénoms</h5>
                </div>
                <div class="p-2.5 text-center xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Email</h5>
                </div>
                <div class="hidden p-2.5 text-center sm:block xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Status</h5>
                </div>
                <div class="hidden p-2.5 text-center sm:block xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Date de Création</h5>
                </div>
                <div class="hidden p-2.5 text-center sm:block xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Date de Modification</h5>
                </div>
                <div class="hidden p-2.5 text-center sm:block xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Actions</h5>
                </div>
            </div>

            @foreach($getAdmin as $index => $user)
            <div class="grid grid-cols-3 sm:grid-cols-7">
                <div class="flex items-center gap-3 p-2.5 xl:p-5">
                    <p class="hidden font-medium text-black dark:text-white sm:block">
                        {{ $user -> name }}
                    </p>
                </div>
                <div class="flex items-center justify-center p-2.5 xl:p-5">
                    <p class="font-medium text-black dark:text-white"> {{ $user -> last_name }}</p>
                </div>

                <div class="flex items-center justify-center p-2.5 xl:p-5">
                    <p class="font-medium me-2 px-2.5 py-0.5 rounded border border-gray-400">
                        {{ $user -> email }}</p>
                </div>

                @if($user->status == 0)
                <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                    <p class="font-medium text-black dark:text-white">
                    <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div>
                    Inactif
                    </p>
                </div>
                @elseif($user->status == 1)
                <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                    <p class="font-medium text-black dark:text-white">
                    <div class="h-2.5 w-2.5 rounded-full bg-emerald-500 me-2"></div>
                    Actif
                    </p>
                </div>
                @endif

                <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                    <p class="font-medium text-meta-5">
                        {{ \Carbon\Carbon::parse($user->created_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}
                    </p>
                </div>
                <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                    <p class="font-medium text-meta-5">
                        {{ \Carbon\Carbon::parse($user->updated_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}
                    </p>
                </div>
                <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                    <a href="{{ url('admin/admin/edit', $user -> id) }}" class="font-medium hover:text-violet-600"><i
                            class="fa-solid fa-pen-to-square"></i></a>
                    <a href="{{ url('admin/admin/delete', $user -> id) }}"
                       class="font-medium hover:text-red-600 ms-3"><i class="fa-solid fa-trash"></i></a>
                </div>
            </div>
            @endforeach
            @if($getAdmin->isEmpty())
            <div class="grid grid-cols-3 sm:grid-cols-7">
                <div> Aucun administrateur trouvé.</div>
            </div>
            @endif
            <div
                class="mb-6 mt-3 border-t border-gray-200 pt-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <h2 class="text-title-sm uppercase font-bold text-black dark:text-white">
                    Total
                </h2>
                <nav>
                    <ol class="flex items-center gap-2 bg-bodydark1 p-2 px-8 rounded">
                        <li>
                            <p class="text-md font-medium">{{ $getAdmin->total() }}</p>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection



