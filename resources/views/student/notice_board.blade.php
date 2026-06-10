@extends('layouts.app')
@section('content')
    <div class="container mx-auto p-4">
        @include('message')
        <!-- Breadcrumb Start -->

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                   <iconify-icon icon="mdi:bell" width="24" height="24"></iconify-icon>
                    Liste de vos notifications
                </h1>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Voir la liste des notifications qui vous sont assignés</p>
            </div>

            <nav class="flex items-center text-sm">
                <ol class="flex items-center space-x-2">
                    <li class="flex items-center">
                        <a href="{{ url('student/dashboard') }}"
                            class="text-primary-600 hover:text-violet-600 transition-colors">
                            <i class="fas fa-home mr-1"></i>
                            Dashboard
                        </a>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="my-4">
            {{ $getStudentNoticeboard->links('vendor.pagination.tailwind') }}
        </div>

        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Notifications
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ $getStudentNoticeboard->count() }}
                    notification{{ $getStudentNoticeboard->count() > 1 ? 's' : '' }}
                    trouvée{{ $getStudentNoticeboard->count() > 1 ? 's' : '' }}
                </p>
            </div>
        </div>

         <!-- Filter Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-filter text-primary-600"></i>
                Filtres de recherche
            </h2>

            <form>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Titre Input -->
                    <div>
                        <label for="title"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titre</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="title" name="title" value="{{ Request::get('title') }}"
                                placeholder="Entrez un titre..."
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>

                    <!-- Date notice from Input -->
                    <div>
                        <label for="date_notice_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date de notification de</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-plus text-gray-400"></i>
                            </div>
                            <input type="date" id="date_notice_from" name="date_notice_from" value="{{ Request::get('date_notice_from') }}"
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>

                    <!-- Date notice to Input -->
                    <div>
                        <label for="date_notice_to"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date de notification à</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-check text-gray-400"></i>
                            </div>
                            <input type="date" id="date_notice_to" name="date_notice_to"
                                value="{{ Request::get('date_notice_to') }}"
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>

                    <!-- Date publish from Input -->
                    <div>
                        <label for="date_publish_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date de publication de</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-plus text-gray-400"></i>
                            </div>
                            <input type="date" id="date_publish_from" name="date_publish_from" value="{{ Request::get('date_publish_from') }}"
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>

                    <!-- Date publish Input -->
                    <div>
                        <label for="date_publish_to"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date de publication à</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-check text-gray-400"></i>
                            </div>
                            <input type="date" id="date_publish_to" name="date_publish_to"
                                value="{{ Request::get('date_publish_to') }}"
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="w-full bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                            <i class="fas fa-search"></i>
                            Rechercher
                        </button>
                        <a href="{{ url('student/my_noticeboard') }}"
                            class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                            <i class="fas fa-sync-alt"></i>
                            Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Notifications Grid -->
        <div class="space-y-6 fade-in" style="animation-delay: 0.3s;">
            <!-- Grid or Empty State -->
            @if($getStudentNoticeboard->isEmpty())
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
                    @foreach($getStudentNoticeboard as $studentNoticeboard)
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
                            class="notification-card flex flex-col h-full group hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
                            <!-- Header -->
                            <div class="pb-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3
                                            class="bg-violet-600 text-lg font-semibold py-4 rounded-t-lg px-6 text-white dark:text-white duration-200">
                                            {{ $studentNoticeboard->title }}
                                        </h3>
                                        <div
                                            class="px-6 flex items-center justify-between gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-950 p-3 rounded-lg shadow-lg">
                                                <span>Affiché le {{ $formatDate($studentNoticeboard->notice_date) }}</span>
                                            </div>
                                            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-950 p-3 rounded-lg shadow-lg">
                                                <span>Publié le {{ $formatDate($studentNoticeboard->publish_date) }}</span>
                                            </div>
                                        </div>
                                        <div class="border-t border-gray-100 dark:border-gray-700 my-2"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="px-6 space-y-4 flex-grow">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-white">Message</span>
                                    </div>
                                    <div x-data="{ expanded: false }">
                                        <div class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                            <template x-if="!expanded" x-transition.duration.300ms x-cloak>
                                                <div>{!! $truncateMessage($studentNoticeboard->message, 15) !!}</div>
                                            </template>
                                            <template x-if="expanded">
                                                <div class="prose prose-sm dark:prose-invert max-w-none">
                                                    {!! $studentNoticeboard->message !!}
                                                </div>
                                            </template>
                                        </div>
                                        <button @click="expanded = !expanded" x-transition.duration.300ms x-cloak
                                            class="my-3 border-4 border-violet-600 py-2 px-4 text-violet-600 hover:text-white transition-all duration-300 rounded-lg shadow-lg dark:text-white hover:bg-violet-600 text-sm font-medium">
                                            <span x-text="expanded ? 'Voir moins' : 'Voir plus'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center gap-1">
                                        <span>Créé par <span
                                                class="font-medium text-gray-700 dark:text-gray-300">{{ $studentNoticeboard->created_by_name }}</span></span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span>{{ $formatDate($studentNoticeboard->created_at, true) }}</span>
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
