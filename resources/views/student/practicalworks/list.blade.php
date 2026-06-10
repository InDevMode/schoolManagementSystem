@extends('layouts.app')
@section('content')

    <div class="container mx-auto p-4">
        @include('message')
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <iconify-icon icon="mdi:home-edit" width="24" height="24"></iconify-icon>
                    Liste des travaux de maison
                </h1>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Voir la liste des travaux de maion qui vous sont assignés
                </p>
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

        <!-- Filter Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-filter text-primary-600"></i>
                Filtres de recherche
            </h2>

            <form>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Classe Input -->
                    <div>
                        <label for="class_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Classe</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-book text-gray-400"></i>
                            </div>
                            <input type="text" id="class_name" name="class_name" value="{{ Request::get('class_name') }}"
                                placeholder="Entrez une classe..."
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>

                    <div>
                        <label for="subject_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Matière</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-book text-gray-400"></i>
                            </div>
                            <input type="text" id="subject_name" name="subject_name"
                                value="{{ Request::get('subject_name') }}" placeholder="Entrez une matière..."
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>

                    <!-- Date work Input -->
                    <div>
                        <label for="work_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date
                            de travail de maison</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-plus text-gray-400"></i>
                            </div>
                            <input type="date" id="work_date" name="work_date" value="{{ Request::get('work_date') }}"
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>

                    <!-- Date Updated Input -->
                    <div>
                        <label for="submission_date"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date
                            de soumission</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-check text-gray-400"></i>
                            </div>
                            <input type="date" id="submission_date" name="submission_date"
                                value="{{ Request::get('submission_date') }}"
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>

                    <!-- Date Created Input -->
                    <div>
                        <label for="created_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date
                            de création</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-check text-gray-400"></i>
                            </div>
                            <input type="date" id="created_at" name="created_at" value="{{ Request::get('created_at') }}"
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>

                    <!-- Date Updated Input -->
                    <div>
                        <label for="updated_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date
                            de modification</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-check text-gray-400"></i>
                            </div>
                            <input type="date" id="updated_at" name="updated_at" value="{{ Request::get('updated_at') }}"
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
                        <a href="{{ url('admin/practicalworks/homework/list') }}"
                            class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                            <i class="fas fa-sync-alt"></i>
                            Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="my-3">
            {{ $getWorks->links('vendor.pagination.tailwind') }}
        </div>
        <!-- Results Section -->
        <div class="bg-white rounded-lg dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <!-- Table -->
            <div class="relative overflow rounded-lg z-10">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="rounded-lg bg-violet-600 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Classe
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Matière
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Date du travail
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Date de soumission
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
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Crée par
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Crée le
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($getWorks as $index => $works)
                            @php
                                $status = $works->homework_status;
                                $badge = match ($status) {
                                    'submitted' => ['Soumis', 'border border-blue-500 bg-blue-100 text-blue-700'],
                                    'done' => ['Fait', 'border border-purple-500 bg-purple-100 text-purple-700'],
                                    'processed' => ['Traité', 'border border-orange-500 bg-orange-100 text-orange-700'],
                                    'resolved' => ['Résolu', 'border border-green-500 bg-green-100 text-green-700'],
                                    default => ['En attente', 'border border-amber-500 bg-amber-100 text-amber-700'],
                                };
                            @endphp
                            <tr class="hover:bg-violet-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $works->class_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $works->subject_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($works->work_date)->locale('fr')->translatedFormat('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($works->submission_date)->locale('fr')->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-2">
                                        @if ($works->document_file)
                                            <a href="{{ url('upload/practicalworks/' . $works->document_file) }}" target="_blank"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg bg-violet-600 text-white text-xs hover:bg-violet-700">
                                                <iconify-icon icon="mdi:file-download-outline" class="mr-2" width="18"
                                                    height="18"></iconify-icon>
                                                Document original
                                            </a>
                                        @endif
                                        @if ($works->homework_document_file)
                                            <a href="{{ url('upload/homeworks/' . $works->homework_document_file) }}"
                                                target="_blank"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs hover:bg-emerald-700">
                                                <iconify-icon icon="mdi:file-check-outline" class="mr-2" width="18"
                                                    height="18"></iconify-icon>
                                                Travail soumis
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {!! \Illuminate\Support\Str::words(strip_tags($works->description), 5, '...') !!}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 text-xs font-medium rounded-full {{ $badge[1] }}">{{ $badge[0] }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $works->created_by_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($works->created_at)->locale('fr')->translatedFormat('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right">
                                    <div x-data="{ open: false, showModal: false }" class="relative">
                                        <button @click="open = !open"
                                            class="flex items-center gap-1 px-3 py-2 rounded-lg shadow-md text-sm bg-white border dark:border-gray-600 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:text-violet-600">
                                            Actions
                                            <iconify-icon icon="mdi:chevron-down" class="text-xl"></iconify-icon>
                                        </button>

                                        <div x-show="open" @click.away="open = false" x-transition
                                            class="absolute right-0 mt-2 w-44 z-50 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-1">
                                            @if ($works->homework_status)
                                                <button @click="showModal = true; open = false"
                                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:text-amber-400 dark:hover:text-amber-400 flex items-center">
                                                    <iconify-icon icon="mdi:eye" class="mr-2" width="18"
                                                        height="18"></iconify-icon>Voir
                                                </button>
                                            @else
                                                <a href="{{ url('student/my_homework/submission', $works->id) }}"
                                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200  hover:text-emerald-400 dark:hover:text-emerald-400 flex items-center">
                                                    <iconify-icon icon="mdi:check-bold" class="mr-2"
                                                        width="20"></iconify-icon>Soumettre
                                                </a>
                                                <button @click="showModal = true; open = false"
                                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:text-amber-400 dark:hover:text-amber-400 flex items-center">
                                                    <iconify-icon icon="mdi:eye" class="mr-2" width="18"
                                                        height="18"></iconify-icon>Voir
                                                </button>
                                            @endif
                                        </div>

                                        <!-- Modal -->
                                        <div x-show="showModal" x-transition
                                            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
                                            <div
                                                class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-lg shadow-lg overflow-hidden">
                                                <div
                                                    class="flex justify-between items-center px-4 py-3 bg-violet-600 text-white">
                                                    <h3 class="text-lg font-semibold">Détails du travail</h3>
                                                    <button @click="showModal = false">
                                                        <iconify-icon icon="mdi:close" width="20"></iconify-icon>
                                                    </button>
                                                </div>
                                                <div class="p-5 space-y-4">
                                                    <div class="flex justify-between text-sm">
                                                        <span class="text-violet-600 font-medium">{{ $works->class_name }} –
                                                            {{ $works->subject_name }}</span>
                                                        <span class="text-gray-500 dark:text-gray-400">Créé par
                                                            {{ $works->created_by_name }}</span>
                                                    </div>
                                                    @if ($works->document_file)
                                                        <a href="{{ url('upload/practicalworks/' . $works->document_file) }}"
                                                            target="_blank"
                                                            class="inline-flex items-center px-3 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 text-sm">
                                                            <iconify-icon icon="mdi:file-download-outline"
                                                                class="mr-2"></iconify-icon>
                                                            Télécharger le document
                                                        </a>
                                                    @endif
                                                    @if ($works->homework_document_file)
                                                        <a href="{{ url('upload/practicalworks/' . $works->document_file) }}"
                                                            target="_blank"
                                                            class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm">
                                                            <iconify-icon icon="mdi:file-download-outline"
                                                                class="mr-2"></iconify-icon>
                                                            Télécharger le document de l'apprenant
                                                        </a>
                                                    @endif
                                                  <div class="space-y-6 max-h-[350px] overflow-y-auto prose dark:prose-invert">
                                                    <!-- Description du professeur -->
                                                    <div>
                                                        <h4 class="text-base font-semibold text-violet-600 dark:text-violet-400 mb-2">Description du professeur</h4>
                                                        <div class="text-start bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                                            {!! $works->description !!}
                                                        </div>
                                                    </div>

                                                    <!-- Description de l'apprenant (si disponible) -->
                                                    @if(!empty($works->homework_description))
                                                        <div>
                                                            <h4 class="text-base font-semibold text-emerald-600 dark:text-emerald-400 mb-2">Description de l'apprenant</h4>
                                                            <div class="text-start bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                                                {!! $works->homework_description !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                    <div
                                                        class="text-sm text-gray-500 dark:text-gray-400 flex justify-between pt-2 border-t">
                                                        <span>📌 Travail :
                                                            {{ \Carbon\Carbon::parse($works->work_date)->locale('fr')->translatedFormat('d M Y') }}</span>
                                                        <span>📤 Soumission :
                                                            {{ \Carbon\Carbon::parse($works->submission_date)->locale('fr')->translatedFormat('d M Y') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center px-6 py-4 text-gray-500 dark:text-gray-400">
                                    Aucun travail de maison trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <!-- Table Footer -->
            <div
                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Total de <span class="font-medium">{{ $getWorks->total() }}</span><span
                        class="">{{ $getWorks->total() > 1 ? ' travaux de maison' : ' travail de maison' }}</span>
                    affiché<span class="">{{ $getWorks->total() > 1 ? 's' : '' }}</span>
                </div>

                <!-- Pagination -->
                <nav class="flex items-center gap-5">
                    {{ $getWorks->links('vendor.pagination.tailwind') }}
                </nav>
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
