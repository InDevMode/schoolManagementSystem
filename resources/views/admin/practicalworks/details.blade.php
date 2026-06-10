@extends('layouts.app')
@section('content')
    <div class="m-2">
        <div class="container mx-auto px-4 py-8 max-w-6xl">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                        <iconify-icon icon="mdi:notebook-edit" class="text-violet-600 mr-2" width="28"
                            height="28"></iconify-icon>
                        Détails d'un travail de maison
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">Voir les détails pour d'un travail de maison</p>
                </div>

                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ url('admin/dashboard') }}"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-violet-600 dark:text-gray-400 dark:hover:text-white">
                                <iconify-icon icon="mdi:home" class="mr-2" width="16" height="16"></iconify-icon>
                                Tableau de bord
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                    height="16"></iconify-icon>
                                <a href="{{ url('admin/practicalworks/homework/list ') }}"
                                    class="ml-1 text-sm font-medium text-gray-700 hover:text-violet-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Travaux
                                    de maison</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                    height="16"></iconify-icon>
                                <span
                                    class="ml-1 text-sm font-medium text-violet-600 md:ml-2 dark:text-gray-400">Détails</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
            <!-- Main Content -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden transition-colors duration-300">
                <!-- Class and Subject Info -->
                <div class="bg-gradient-to-r from-violet-600 to-violet-700 text-white p-6">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-4">
                            <div class="bg-gradient-to-r from-violet-600 to-violet-700/20 p-3 rounded-lg">
                                <iconify-icon icon="mdi:school" width="24" height="24"></iconify-icon>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold">{{ $getWorks->class_name }}</h2>
                                <p class="text-violet-100">{{ $getWorks->subject_name }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 text-violet-100">
                            <iconify-icon icon="mdi:account" width="16" height="16"></iconify-icon>
                            <span class="text-sm">Créé par {{ $getWorks->created_by_name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-8">
                    <!-- Dates Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800 transition-colors">
                            <div class="flex items-center gap-3 mb-2">
                                <iconify-icon icon="mdi:calendar" class="text-blue-600" width="20"
                                    height="20"></iconify-icon>
                                <h3 class="font-semibold text-blue-900 dark:text-blue-300">Date de travail</h3>
                            </div>
                            <p class="text-blue-800 dark:text-blue-200 font-medium">
                                {{ \Carbon\Carbon::parse($getWorks->work_date)->locale('fr')->isoFormat('D MMMM YYYY') }}
                            </p>
                        </div>

                        <div
                            class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-800 transition-colors">
                            <div class="flex items-center gap-3 mb-2">
                                <iconify-icon icon="mdi:clock-outline" class="text-purple-600" width="20"
                                    height="20"></iconify-icon>
                                <h3 class="font-semibold text-purple-900 dark:text-purple-300">Date de soumission</h3>
                            </div>
                            <p class="text-purple-800 dark:text-purple-200 font-medium">
                                {{ \Carbon\Carbon::parse($getWorks->submission_date)->locale('fr')->isoFormat('D MMMM YYYY') }}
                            </p>
                        </div>
                    </div>

                    <!-- Document Section -->
                    @if($getWorks->document_file)
                        <div
                            class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 transition-colors">
                            <div class="flex items-center gap-3 mb-3">
                                <iconify-icon icon="mdi:file-document" class="text-gray-600 dark:text-gray-400" width="20"
                                    height="20"></iconify-icon>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Document joint</h3>
                            </div>
                            <div
                                class="flex items-center gap-4 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 transition-colors">
                                <span class="text-2xl">
                                    @php
                                        $extension = strtolower(pathinfo($getWorks->document_file, PATHINFO_EXTENSION));
                                        $fileIcon = match ($extension) {
                                            'pdf' => '📄',
                                            'doc', 'docx' => '📘',
                                            'xls', 'xlsx' => '📊',
                                            'ppt', 'pptx' => '📽️',
                                            'jpg', 'jpeg', 'png', 'gif' => '🖼️',
                                            default => '📎'
                                        };
                                    @endphp
                                    {{ $fileIcon }}
                                </span>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $getWorks->document_file }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Document du professeur</p>
                                </div>
                                <a href="{{ url('/upload/practicalworks/' . $getWorks->document_file) }}" target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-opacity-50">
                                    <iconify-icon icon="mdi:download" width="16" height="16"></iconify-icon>
                                    Télécharger
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Teacher Description -->
                    <div
                        class="bg-violet-50 dark:bg-violet-900/20 p-6 rounded-lg border border-violet-200 dark:border-violet-800 transition-colors">
                        <div class="flex items-center gap-3 mb-4">
                            <iconify-icon icon="mdi:account-tie" class="text-violet-600" width="20"
                                height="20"></iconify-icon>
                            <h3 class="text-lg font-semibold text-violet-900 dark:text-violet-300">
                                Description du professeur
                            </h3>
                        </div>
                        <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                            {!! $getWorks->description !!}
                        </div>
                    </div>

                    <!-- Student Description -->
                    @if($getWorks->homework_description)
                        <div
                            class="bg-emerald-50 dark:bg-emerald-900/20 p-6 rounded-lg border border-emerald-200 dark:border-emerald-800 transition-colors">
                            <div class="flex items-center gap-3 mb-4">
                                <iconify-icon icon="mdi:school" class="text-emerald-600" width="20" height="20"></iconify-icon>
                                <h3 class="text-lg font-semibold text-emerald-900 dark:text-emerald-300">
                                    Description de l'apprenant
                                </h3>
                            </div>
                            <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                                {!! $getWorks->homework_description !!}
                            </div>
                        </div>
                    @endif

                    <!-- Metadata -->
                    <div
                        class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 transition-colors">
                        <div
                            class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 flex-wrap gap-2">
                            <span class="flex items-center gap-2">
                                <iconify-icon icon="mdi:calendar-plus" width="16" height="16"></iconify-icon>
                                Créé le
                                {{ \Carbon\Carbon::parse($getWorks->created_at)->locale('fr')->isoFormat('D MMMM YYYY') }}
                            </span>
                            <span class="flex items-center gap-2">
                                <iconify-icon icon="mdi:identifier" width="16" height="16"></iconify-icon>
                                ID: {{ $getWorks->id }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    </div>
@endsection

<script>
    // Animation d'entrée pour les cartes
    document.addEventListener('DOMContentLoaded', function () {
        const cards = document.querySelectorAll('.bg-white, .bg-gray-50, .bg-violet-50, .bg-emerald-50, .bg-blue-50, .bg-purple-50');

    });
</script>
