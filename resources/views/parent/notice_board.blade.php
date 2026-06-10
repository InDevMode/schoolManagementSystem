@extends('layouts.app')
@section('content')
    <div class="m-5">
        <!-- Breadcrumb Start -->
        <div class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between">
            <h2 class="text-3xl font-bold text-gray-900  dark:text-bodydark">
                Tableau de bord des notifications
            </h2>
            <nav>
                <ol class="flex items-center gap-2">
                    <li>
                        <span class="font-medium text-violet-600"><iconify-icon icon="mdi:bell-outline" width="24" height="24"></iconify-icon>
                    </li>
                    <li>
                        /<a class="text-xl font-bold text-gray-900 hover:text-violet-600 transition duration-300 dark:text-bodydark"
                            href="{{ url('parent/dashboard') }}"> Dashboard</a>
                    </li>
                </ol>
            </nav>
        </div>
        @include('message')
        <div class="my-4">
            {{ $getParentNoticeboard->links('vendor.pagination.tailwind') }}
        </div>

        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Notifications
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ $getParentNoticeboard->count() }}
                    notification{{ $getParentNoticeboard->count() > 1 ? 's' : '' }}
                    trouvée{{ $getParentNoticeboard->count() > 1 ? 's' : '' }}
                </p>
            </div>
        </div>

        <div
            class="rounded-lg border border-stroke bg-white px-5 pb-2.5 pt-6 mb-4 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
            <form action="" method="get">
                <div class="mb-4.5 grid grid-cols-2 xl:grid-cols-5 gap-3 items-center">
                    <div class="w-full">
                        <input type="text" id="title" name="title" value="{{ Request::get('title') }}"
                            placeholder="titre..."
                            class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600" />
                    </div>
                    <div class="w-full">
                        <div class="relative">
                            <input id="date_notice_from" name="date_notice_from"
                                value="{{ Request::get('date_notice_from') }}"
                                class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                placeholder="date d'affichage de..." data-class="flatpickr-right" required />

                            <div class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center">
                                <iconify-icon icon="lucide:calendar" width="24" height="24"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="relative">
                            <input id="date_notice_to" name="date_notice_to" value="{{ Request::get('date_notice_to') }}"
                                class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                placeholder=" à ..." data-class="flatpickr-right" required />

                            <div class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center">
                                <iconify-icon icon="lucide:calendar" width="24" height="24"></iconify-icon>
                            </div>
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
                        <a href="{{ url('parent/my_noticeboard') }}"
                            class="flex w-full justify-center rounded-lg bg-gray-500 px-3 py-2.5 font-medium text-gray hover:bg-opacity-90">
                            Réïnitialisez
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Notifications Grid -->
        <div class="space-y-6 fade-in" style="animation-delay: 0.3s;">

            <!-- Grid or Empty State -->
            @if($getParentNoticeboard->isEmpty())
                <div class="text-center py-12">
                    <div
                        class="mx-auto h-24 w-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        Aucune notification trouvée
                    </h3>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($getParentNoticeboard as $parentNoticeboard)
                        @php
                            $formatDate = function ($dateString, $includeTime = false) {
                                $date = \Carbon\Carbon::parse($dateString)->locale('fr');
                                return $includeTime
                                    ? $date->translatedFormat('d M Y H:i:s')
                                    : $date->translatedFormat('d M Y');
                            };

                            $truncateMessage = function ($message, $words = 15) {
                                $stripped = $message;
                                $wordArray = explode(' ', $stripped);
                                if (count($wordArray) <= $words)
                                    return $stripped;
                                return implode(' ', array_slice($wordArray, 0, $words)) . '...';
                            };
                        @endphp

                        <div
                            class="notification-card group hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
                            <!-- Header -->
                            <div class="pb-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3
                                            class="bg-violet-600 text-lg font-semibold py-4 rounded-t-lg px-6 text-white dark:text-white duration-200">
                                            {{ $parentNoticeboard->title }}
                                        </h3>
                                        <div
                                            class="px-6 flex items-center justify-between gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-950 p-3 rounded-lg shadow-lg">
                                                <span>Affiché le {{ $formatDate($parentNoticeboard->notice_date) }}</span>
                                            </div>
                                            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-950 p-3 rounded-lg shadow-lg">
                                                <span>Publié le {{ $formatDate($parentNoticeboard->publish_date) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="px-6 space-y-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-white">Message</span>
                                    </div>
                                    <div x-data="{ expanded: false }">
                                        <div class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                            <template x-if="!expanded" x-transition.duration.300ms x-cloak>
                                                <div>{!! $truncateMessage($parentNoticeboard->message, 15) !!}</div>
                                            </template>
                                            <template x-if="expanded">
                                                <div class="prose prose-sm dark:prose-invert max-w-none">
                                                    {!! $parentNoticeboard->message !!}
                                                </div>
                                            </template>
                                        </div>
                                        <button @click="expanded = !expanded" x-transition.duration.300ms x-cloak
                                            class="my-3 border-4 border-violet-600 py-2 px-4 text-violet-600 hover:text-white transition-all duration-300 rounded-lg shadow-lg dark:text-white hover:bg-violet-600 text-sm font-medium">
                                            <span x-text="expanded ? 'Voir moins' : 'Voir plus'"></span>
                                        </button>
                                    </div>
                                </div>

                                <div class="border-t border-gray-100 dark:border-gray-700"></div>
                            </div>

                            <!-- Footer -->
                            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center gap-1">
                                        <span>Créé par <span
                                                class="font-medium text-gray-700 dark:text-gray-300">{{ $parentNoticeboard->created_by_name }}</span></span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span>{{ $formatDate($parentNoticeboard->created_at, true) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    </div>
@endsection

<script>
    // Animation d'apparition progressive des éléments
    document.addEventListener('DOMContentLoaded', function () {
        // Animer les cartes de statistiques
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            card.style.animationDelay = `${0.1 + (index * 0.1)}s`;
            card.classList.add('fade-in');
        });

        // Animer les cartes de notification
        const notificationCards = document.querySelectorAll('.notification-card');
        notificationCards.forEach((card, index) => {
            card.style.animationDelay = `${0.4 + (index * 0.1)}s`;
            card.classList.add('fade-in');
        });
    });

    // Animation au scroll (optionnel)
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    // Observer les éléments qui ne sont pas encore visibles
    document.querySelectorAll('.notification-card:not(.fade-in)').forEach(card => {
        observer.observe(card);
    });
</script>
