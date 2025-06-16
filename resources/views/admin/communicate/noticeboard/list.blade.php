@extends('layouts.app')
@section('content')
    <div class="m-5">
        <!-- Breadcrumb Start -->
        <div class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between">
            <h2 class="uppercase font-bold text-black dark:text-bodydark">
                Affichage de la liste des notifications
            </h2>
            <nav>
                <ol class="flex items-center gap-2">
                    <li>
                        <span class="font-medium text-violet-600"><iconify-icon
                                icon="mdi:bulletin-board"></iconify-icon></span>
                    </li>
                    <li>
                        /<a class="font-medium hover:text-violet-600 transition duration-300"
                            href="{{ url('admin/dashboard') }}"> Dashboard</a>
                    </li>
                    <li>
                        /<a class="font-medium hover:text-violet-600 transition duration-300"
                            href="{{ url('admin/communicate/noticeboard/add') }}"> Créer un message</a>
                    </li>
                </ol>
            </nav>
        </div>
        @include('message')
        <div class="mt-4">
            {{ $getNoticeBoard->links('vendor.pagination.tailwind') }}
        </div>
        <div
            class="rounded-lg border border-stroke bg-white px-5 pb-2.5 pt-6 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
            <form action="" method="get">
                <div class="mb-4.5 grid grid-cols-2 xl:grid-cols-5 gap-3 items-center">
                    <div class="w-full">
                        <input type="text" id="title" name="title" value="{{ Request::get('title') }}"
                            placeholder="titre..."
                            class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600" />
                    </div>
                    <div class="w-full">
                        <div class="relative">
                            <input id="date_notice_to" name="date_notice_to"
                                value="{{ Request::get('date_notice_to') }}"
                                class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                placeholder="date d'affichage de ..." data-class="flatpickr-right" required />

                            <div class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center">
                                <iconify-icon icon="lucide:calendar" width="24" height="24"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="relative">
                            <input id="date_notice_from" name="date_notice_from"
                                value="{{ Request::get('date_notice_from') }}"
                                class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                placeholder="date d'affichage à..." data-class="flatpickr-right" required />

                            <div class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center">
                                <iconify-icon icon="lucide:calendar" width="24" height="24"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="relative">
                            <input id="publish_date_to" name="publish_date_to"
                                value="{{ Request::get('publish_date_to') }}"
                                class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                placeholder="date de publication de ..." data-class="flatpickr-right" required />

                            <div class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center">
                                <iconify-icon icon="lucide:calendar" width="24" height="24"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="relative">
                            <input id="publish_date_from" name="publish_date_from"
                                value="{{ Request::get('publish_date_from') }}"
                                class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                placeholder="date d'affichage de fin..." data-class="flatpickr-right" required />

                            <div class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center">
                                <iconify-icon icon="lucide:calendar" width="24" height="24"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-gray-100 dark:bg-form-input">
                            <select id="message_to" name="message_to"
                                class="relative z-20 w-full appearance-none rounded-lg border border-stroke bg-gray-100 px-5 py-2.5 outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                :class="isOptionSelected && 'text-black dark:text-white'" @change="isOptionSelected = true">
                                <option selected disabled value="" class="text-body">Choisissez le destinataire</option>
                                <option value="2" class="text-body" {{ (Request::get('message_to') == 2) ? 'selected' : '' }}>Professeurs</option>
                                <option value="3" class="text-body" {{ (Request::get('message_to') == 3) ? 'selected' : '' }}>Apprenants</option>
                                <option value="4" class="text-body" {{ (Request::get('message_to') == 4) ? 'selected' : '' }}>Parents</option>
                            </select>
                            <span class="absolute right-4 top-1/2 z-30 -translate-y-1/2">
                                <iconify-icon icon="lucide:chevron-down" width="24" height="24"></iconify-icon>
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
                        <a href="{{ url('admin/communicate/noticeboard/list') }}"
                            class="flex w-full justify-center rounded-lg bg-gray-500 px-3 py-2.5 font-medium text-gray hover:bg-opacity-90">
                            Réïnitialisez
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="mt-5">
            <div class="relative overflow rounded-lg z-10">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-white uppercase bg-violet-500 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Titre
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Date d'affichage
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Date de publication
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Message
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Envoyé aux
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Crée par
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Date de création
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($getNoticeBoard as $index => $noticeBoard)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $noticeBoard->title }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($noticeBoard->notice_date)->locale('fr')->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($noticeBoard->notice_date)->locale('fr')->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ \Illuminate\Support\Str::words(strip_tags($noticeBoard->message), 5, '...') }}
                                </td>
                                <td class="px-6 py-4">
                                    @foreach ($noticeBoard->getNoticeBoardMessage as $message)
                                        @php
                                            $label = '';
                                            $class = '';
                                            switch ($message->message_to) {
                                                case '2':
                                                    $label = 'Professeurs';
                                                    $class = 'text-center text-violet-700 dark:text-gray-200 bg-violet-100 dark:bg-violet-900 my-1';
                                                    break;
                                                case '3':
                                                    $label = 'Apprenants';
                                                    $class = 'text-center text-red-700 dark:text-gray-200 bg-red-100 dark:bg-red-900 my-1';
                                                    break;
                                                case '4':
                                                    $label = 'Parents';
                                                    $class = 'text-center text-pink-700 dark:text-gray-200 bg-pink-100 dark:bg-pink-900 my-1';
                                                    break;
                                                default:
                                                    $label = 'Autres';
                                                    $class = 'text-center text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 my-1';
                                                    break;
                                            }
                                        @endphp
                                        <p class="px-6 py-1 rounded-full {{ $class }}">
                                            {{ $label }}
                                        </p>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4">
                                    {{ $noticeBoard->created_by_name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($noticeBoard->created_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}
                                </td>
                                <td class="px-6 py-3">
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <div>
                                            <button type="button"
                                                class="group inline-flex w-full justify-center gap-x-1.5 rounded-lg shadow-md bg-white dark:bg-gray-800 border dark:border-gray-600 dark:hover:text-violet-600 px-3 py-2 text-sm font-semibold text-gray-700 hover:text-violet-600 dark:text-gray-200 hover:bg-gray-100"
                                                @click="open = !open" id="menu-button" aria-expanded="true"
                                                aria-haspopup="true">
                                                Actions
                                                <svg class="-mr-1 size-5 group-hover:text-violet-600 text-gray-400"
                                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-md bg-white dark:bg-gray-800 ring-1 shadow-lg ring-black/5 focus:outline-hidden"
                                            role="menu" aria-orientation="vertical" aria-labelledby="menu-button"
                                            tabindex="{{ $index + 1 }}" x-show="open" @click.away="open = false" x-transition>
                                            <div class="py-1">
                                                <a href="{{ url('admin/communicate/noticeboard/edit', $noticeBoard->id) }}"
                                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:text-emerald-400 dark:hover:text-emerald-400"
                                                    role="menuitem">Modifier</a>
                                                <form method="get" action="" role="none">
                                                    <button type="submit"
                                                        class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:text-red-400 dark:hover:text-red-400"
                                                        role="menuitem">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-gray-500">
                                    Aucun message de notification trouvé.
                                </td>
                            </tr>
                        @endforelse
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
