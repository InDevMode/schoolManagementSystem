@extends('layouts.app')
@section('content')

    <div class="container mx-auto p-4">
        @include('message')
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <iconify-icon icon="mdi:home-edit" width="24" height="24"></iconify-icon>
                    Liste des travaux de maison soumis par l'apprenant
                </h1>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Gérez la liste des travaux de maion soumis par l'apprenant
                    de votre plateforme</p>
            </div>

            <nav class="flex items-center text-sm">
                <ol class="flex items-center space-x-2">
                    <li class="flex items-center">
                        <a href="{{ url('teacher/dashboard') }}"
                            class="text-primary-600 hover:text-violet-600 transition-colors">
                            <i class="fas fa-home mr-1"></i>
                            <i class="fas fa-home-edit mr-1"></i>
                            Dashboard
                        </a>
                        <span class="mx-2 text-gray-400">
                            <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                height="16"></iconify-icon>
                        </span>
                    </li>
                    <li class="flex items-center">
                        <a href="{{ url('teacher/practicalworks/homework/list') }}"
                            class="flex items-center text-primary-600 hover:text-violet-600 transition-colors">
                            <iconify-icon icon="mdi:home-edit" class="mr-1" width="18" height="18"></iconify-icon>
                            Traux de maison
                        </a>
                        <span class="mx-2 text-gray-400">
                            <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                height="16"></iconify-icon>
                        </span>
                    </li>
                    <li class="flex items-center">
                        <a href="{{ url('teacher/practicalworks/homework/add') }}"
                            class="text-primary-600 hover:text-violet-600 transition-colors">
                            <i class="fas fa-plus-circle mr-1"></i>
                            Créer un travail de maison
                        </a>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Filter Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-filter text-primary-600"></i>
                Filtres de recherche
            </h2>

            <form>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="student_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom de l'apprenant</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="student_name" name="student_name" value="{{ Request::get('student_name') }}"
                                placeholder="Entrez un nom..."
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>

                    <div>
                        <label for="student_last_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prénom de l'apprenant</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="student_last_name" name="student_last_name"
                                value="{{ Request::get('student_last_name') }}" placeholder="Entrez un prénom..."
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>
                    <!-- Date work Input -->
                    <div>
                        <label for="submission_date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date
                            de soumission de</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-plus text-gray-400"></i>
                            </div>
                            <input type="date" id="submission_date_from" name="submission_date_from" value="{{ Request::get('submission_date_from') }}"
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>

                    <!-- Date cretated at Input -->
                    <div>
                        <label for="submission_date_to"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date
                            de soumission à</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-check text-gray-400"></i>
                            </div>
                            <input type="date" id="submission_date_to" name="submission_date_to"
                                value="{{ Request::get('submission_date_to') }}"
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
                        <a href="{{ url('teacher/practicalworks/homework/submission/' . $work_id) }}"
                            class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                            <i class="fas fa-sync-alt"></i>
                            Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="my-3">
            {{ $getHomeworks->links('vendor.pagination.tailwind') }}
        </div>
        <!-- Results Section -->
        <div class="bg-white rounded-lg dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <!-- Table -->
            <div class="relative overflow rounded-lg z-10">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="rounded-lg bg-violet-600 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Nom & Prénoms de l'apprenant
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Document
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Crée le
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="z-20 bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Sample Row 1 -->
                        @foreach($getHomeworks as $index => $homeworks)
                            @php
                                $status = $homeworks->homework_status;
                                $badge = match ($status) {
                                    'submitted' => ['Soumis', 'border border-blue-500 bg-blue-100 text-blue-700'],
                                    'done' => ['Fait', 'border border-purple-500 bg-purple-100 text-purple-700'],
                                    'processed' => ['Traité', 'border border-orange-500 bg-orange-100 text-orange-700'],
                                    'resolved' => ['Résolu', 'border border-green-500 bg-green-100 text-green-700'],
                                    default => ['En attente', 'border border-amber-500 bg-amber-100 text-amber-700'],
                                };
                            @endphp
                            <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <span
                                                class="text-sm font-medium text-gray-900 dark:text-white">{{ $homeworks->student_name }}</span>
                                            <span
                                                class="text-sm text-gray-500 dark:text-gray-400">{{ $homeworks->student_last_name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        @if (!empty($homeworks->homework_document_file))
                                            <a href="{{ url('upload/practicalworks/' . $homeworks->homework_document_file) }}"
                                                target="_blank"
                                                class="flex items-center justify-center bg-violet-600 text-white px-2.5 py-1.5 rounded-md text-sm font-medium"><iconify-icon
                                                    icon="mdi:file-download-outline" width="24" height="24"
                                                    class="text-white"></iconify-icon>
                                                Télécharger le document
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ \Illuminate\Support\Str::words(strip_tags($homeworks->homework_description), 3, '...') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($homeworks->created_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="relative inline-block text-left" x-data="{ open: false, showModal: false }">
                                        <div>
                                            <button type="button"
                                                class="group inline-flex w-full justify-center gap-x-1.5 rounded-lg shadow-md bg-white dark:bg-gray-800 border dark:border-gray-600 dark:hover:text-violet-600 px-3 py-2 text-sm font-semibold text-gray-700 hover:text-violet-600 dark:text-gray-200 hover:bg-gray-100"
                                                @click="open = !open" id="menu-button" aria-expanded="true"
                                                aria-haspopup="true">
                                                Actions
                                                <span
                                                    class="-mr-1 size-5 group-hover:text-violet-600 text-gray-400"><iconify-icon
                                                        icon="mdi:chevron-down" width="22" height="22"></iconify-icon></span>
                                            </button>
                                        </div>
                                        <div class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-md bg-white dark:bg-gray-800 ring-1 shadow-lg ring-black/5 focus:outline-hidden"
                                            role="menu" aria-orientation="vertical" aria-labelledby="menu-button"
                                            tabindex="{{ $index + 1 }}" x-show="open" @click.away="open = false" x-transition>
                                            <div class="py-1">
                                                <div x-data="{ showModal: false }">
                                                    <button @click="showModal = true; "
                                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:text-amber-400 dark:hover:text-amber-400 flex items-center">
                                                        <iconify-icon icon="mdi:eye" class="mr-2" width="18"
                                                            height="18"></iconify-icon>Voir
                                                    </button>
                                                    <!-- Ton modal est en dehors du tableau -->
                                                    <div x-show="showModal" x-transition x-cloak <div
                                                        class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
                                                        <div
                                                            class="bg-white dark:bg-gray-800 max-w-2xl w-full rounded-lg shadow-lg overflow-hidden">
                                                            <div
                                                                class="flex justify-between items-center px-4 py-3 bg-violet-600 text-white">
                                                                <h3 class="text-lg font-semibold">Détails du travail</h3>
                                                                <button @click="showModal = false">
                                                                    <iconify-icon icon="mdi:close" width="20"></iconify-icon>
                                                                </button>
                                                            </div>
                                                            <div class="p-5 space-y-4">
                                                                <div class="flex justify-between text-sm">
                                                                    <span
                                                                        class="text-violet-600 font-medium">{{ $homeworks->student_name }}
                                                                        –
                                                                        {{ $homeworks->student_last_name }}</span>
                                                                    <span
                                                                        class="px-3 py-1 text-xs font-medium rounded-full {{ $badge[1] }}">{{ $badge[0] }}</span>
                                                                </div>
                                                                @if ($homeworks->document_file)
                                                                    <a href="{{ url('upload/practicalworks/' . $homeworks->homework_document_file) }}"
                                                                        target="_blank"
                                                                        class="inline-flex items-center px-3 py-2 bg-violet-600 text-white rounded-md hover:bg-violet-700 text-sm">
                                                                        <iconify-icon icon="mdi:file-download-outline"
                                                                            class="mr-2"></iconify-icon>
                                                                        Télécharger le document
                                                                    </a>
                                                                @endif

                                                                <div
                                                                    class="space-y-6 max-h-[350px] overflow-y-auto prose dark:prose-invert">

                                                                    <!-- Description de l'apprenant (si disponible) -->
                                                                    @if(!empty($homeworks->homework_description))
                                                                        <div>
                                                                            <h4
                                                                                class="text-base font-semibold text-emerald-600 dark:text-emerald-400 mb-2">
                                                                                Description de l'apprenant</h4>
                                                                            <div
                                                                                class="text-start bg-gray-50 dark:bg-gray-700 p-3 rounded-md">
                                                                                {!! $homeworks->homework_description !!}
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div
                                                                    class="text-sm text-gray-500 dark:text-gray-400 flex justify-between pt-2 border-t">
                                                                    <span>📌 Soumis le :
                                                                        {{ \Carbon\Carbon::parse($homeworks->created_at)->locale('fr')->translatedFormat('d M Y') }}</span>
                                                                    <span>📤 Date du travail :
                                                                        {{ \Carbon\Carbon::parse($homeworks->work_date)->locale('fr')->translatedFormat('d M Y') }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if($getHomeworks->isEmpty())
                            <tr class="text-center text-gray-700 dark:text-bodydark1">
                                <td colspan="8" class="py-3"> Aucun travail de maison soumis trouvé.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div
                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Total de <span class="font-medium">{{ $getHomeworks->total() }}</span><span
                        class="">{{ $getHomeworks->total() > 1 ? ' travaux de maison' : ' travail de maison' }}</span>
                    affiché<span class="">{{ $getHomeworks->total() > 1 ? 's' : '' }}</span>
                </div>

                <!-- Pagination -->
                <nav class="flex items-center gap-5">
                    {{ $getHomeworks->links('vendor.pagination.tailwind') }}
                </nav>
            </div>
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
