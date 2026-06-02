@extends('layouts.app')
@section('content')
@php
    $isSuperAdmin = Auth::user()->user_type === 0;
    $inlineEditUrl = $isSuperAdmin ? url('superadmin/users/inline-edit') : '';
    $perPage = Request::get('per_page', 15);
@endphp

<div class="container mx-auto px-4 py-5">
    @include('message')

    {{-- ── En-tête page ── --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-user-shield text-violet-600"></i>
                Liste des administrateurs
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Gérez les comptes administrateurs de votre plateforme</p>
        </div>
        <nav class="flex items-center text-sm">
            <ol class="flex items-center space-x-2">
                <li>
                    <a href="{{ url('admin/dashboard') }}" class="text-violet-600 hover:text-violet-700 transition-colors">
                        <i class="fas fa-home mr-1"></i>Tableau de bord
                    </a>
                    <span class="mx-2 text-gray-400">/</span>
                </li>
                <li>
                    <a href="{{ url('admin/admin/add') }}" class="text-violet-600 hover:text-violet-700 transition-colors">
                        <i class="fas fa-plus-circle mr-1"></i>Créer un administrateur
                    </a>
                </li>
            </ol>
        </nav>
    </div>

    {{-- ── Filtres ── --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-5">
        <h2 class="text-base font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-filter text-violet-600"></i>Filtres de recherche
        </h2>
        <form method="GET" action="{{ url('admin/admin/list') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Nom</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="name" value="{{ Request::get('name') }}" placeholder="Nom..."
                            class="pl-8 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-violet-500 focus:border-violet-500 p-2.5">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Prénoms</label>
                    <div class="relative">
                        <i class="fas fa-user-tag absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="last_name" value="{{ Request::get('last_name') }}" placeholder="Prénoms..."
                            class="pl-8 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-violet-500 focus:border-violet-500 p-2.5">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="email" name="email" value="{{ Request::get('email') }}" placeholder="Email..."
                            class="pl-8 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-violet-500 focus:border-violet-500 p-2.5">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Statut</label>
                    <div class="relative">
                        <select name="status" class="appearance-none w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-violet-500 focus:border-violet-500 p-2.5 pr-8">
                            <option value="">Tous les statuts</option>
                            <option value="1" {{ Request::get('status') === '1' ? 'selected' : '' }}>Actif</option>
                            <option value="0" {{ Request::get('status') === '0' ? 'selected' : '' }}>Inactif</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Date de création</label>
                    <div class="relative">
                        <i class="fas fa-calendar-plus absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="date" name="created_at" value="{{ Request::get('created_at') }}"
                            class="pl-8 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-violet-500 focus:border-violet-500 p-2.5">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Date de modification</label>
                    <div class="relative">
                        <i class="fas fa-calendar-check absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="date" name="updated_at" value="{{ Request::get('updated_at') }}"
                            class="pl-8 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-violet-500 focus:border-violet-500 p-2.5">
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-all duration-200">
                        <i class="fas fa-search"></i>Rechercher
                    </button>
                    <a href="{{ url('admin/admin/list') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white text-sm font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-all duration-200">
                        <i class="fas fa-sync-alt"></i>Réinitialiser
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ── Tableau principal ── --}}
    <div id="adv-admin-table"
         data-advanced-table
         data-user-type="{{ $isSuperAdmin ? 'super_admin' : 'admin' }}"
         data-edit-endpoint="{{ $inlineEditUrl }}"
         class="adv-table-wrapper">

        {{-- Barre d'outils ──────────────────────────────────────────── --}}
        <div class="adv-toolbar">
            <div class="flex items-center gap-3 flex-wrap">
                {{-- Recherche instantanée --}}
                <div class="adv-search-bar">
                    <i class="fas fa-search adv-search-icon"></i>
                    <input data-search-input type="text" placeholder="Recherche rapide... (ex: Jean, actif, jean@mail.com)"
                        class="text-sm" autocomplete="off">
                </div>
                <div>
                    <div class="adv-search-hint">Séparez plusieurs termes par des virgules : <em>Jean, actif</em></div>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                {{-- Compteur --}}
                <span data-row-counter class="adv-counter-badge">
                    {{ $getAdmin->total() }} résultat(s)
                </span>
                {{-- Items par page --}}
                <div class="adv-per-page flex items-center gap-2">
                    <label class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Par page</label>
                    <select data-per-page>
                        <option value="10">10</option>
                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                {{-- Export Excel (serveur) --}}
                <form action="{{ url('admin/admin/export') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="name" value="{{ Request::get('name') }}">
                    <input type="hidden" name="last_name" value="{{ Request::get('last_name') }}">
                    <input type="hidden" name="email" value="{{ Request::get('email') }}">
                    <input type="hidden" name="status" value="{{ Request::get('status') }}">
                    <input type="hidden" name="created_at" value="{{ Request::get('created_at') }}">
                    <input type="hidden" name="updated_at" value="{{ Request::get('updated_at') }}">
                    <button type="submit" class="adv-export-btn excel">
                        <i class="fas fa-file-excel"></i>Excel
                    </button>
                </form>
                {{-- Export CSV côté client --}}
                <button type="button" onclick="advTableExportCSV('adv-admin-table', 'administrateurs')" class="adv-export-btn csv">
                    <i class="fas fa-file-csv"></i>CSV
                </button>
            </div>
        </div>

        @if($isSuperAdmin)
        <div class="adv-edit-permission-tip px-5 py-1.5 text-xs text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-900/20 border-b border-violet-100 dark:border-violet-800">
            <i class="fas fa-shield-alt mr-1"></i>
            Mode super admin : <strong>double-cliquez</strong> sur une cellule pour l'éditer directement.
        </div>
        @endif

        {{-- Table ──────────────────────────────────────────────────── --}}
        <div class="adv-table-scroll">
            <table class="adv-table">
                <thead>
                    <tr>
                        <th style="width:40px">
                            <input type="checkbox" data-check-all class="adv-check" title="Tout sélectionner">
                        </th>
                        <th>Photo</th>
                        <th data-sortable="2" data-label="Nom & Prénoms">Nom &amp; Prénoms</th>
                        <th data-sortable="3" data-label="Email">Email</th>
                        <th data-sortable="4" data-label="Statut">Statut</th>
                        <th data-sortable="5" data-label="En ligne">En ligne</th>
                        <th data-sortable="6" data-label="Créé le">Créé le</th>
                        <th data-sortable="7" data-label="Modifié le">Modifié le</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($getAdmin as $index => $admin)
                    @php
                        $isOnline = \Illuminate\Support\Facades\Cache::has('OnlineUser.' . $admin->id);
                        $profilePic = !empty($admin->profile_picture) ? asset('upload/profile/' . $admin->profile_picture) : asset('upload/default.jpg');
                        $editUrl = url('admin/admin/edit', $admin->id);
                        $detailUrl = url('admin/account');
                        $messageUrl = url('chat?receiver_id=' . base64_encode($admin->id));
                    @endphp
                    <tr data-row
                        data-row-id="{{ $admin->id }}"
                        data-edit-url="{{ $editUrl }}"
                        data-detail-url="{{ $detailUrl }}"
                        data-message-url="{{ $messageUrl }}"
                        title="Clic droit pour actions rapides">

                        {{-- Checkbox --}}
                        <td data-col="0">
                            <input type="checkbox" data-check-row class="adv-check" value="{{ $admin->id }}">
                        </td>

                        {{-- Photo --}}
                        <td data-col="1">
                            <img src="{{ $profilePic }}" alt="Photo" class="adv-avatar">
                        </td>

                        {{-- Nom --}}
                        <td data-col="2"
                            data-search-value="{{ strtolower($admin->name . ' ' . $admin->last_name) }}"
                            @if($isSuperAdmin) data-editable data-field-name="name" data-edit-value="{{ $admin->name }}" data-endpoint="{{ $inlineEditUrl }}" @endif>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-800 dark:text-white" data-highlight data-original="{{ $admin->name }}">{{ $admin->name }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400" data-highlight data-original="{{ $admin->last_name }}">{{ $admin->last_name }}</span>
                            </div>
                        </td>

                        {{-- Email --}}
                        <td data-col="3"
                            data-search-value="{{ strtolower($admin->email) }}"
                            @if($isSuperAdmin) data-editable data-field-name="email" data-edit-value="{{ $admin->email }}" data-endpoint="{{ $inlineEditUrl }}" @endif>
                            <span class="text-sm text-gray-700 dark:text-gray-300" data-highlight data-original="{{ $admin->email }}">{{ $admin->email }}</span>
                        </td>

                        {{-- Statut --}}
                        <td data-col="4"
                            data-search-value="{{ $admin->status == 1 ? 'actif' : 'inactif' }}"
                            @if($isSuperAdmin) data-editable data-field-name="status" data-field-type="select" data-edit-value="{{ $admin->status }}" data-endpoint="{{ $inlineEditUrl }}" @endif>
                            <span class="adv-badge {{ $admin->status == 1 ? 'bg-green-100 border-green-700 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 border-red-700 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                {{ $admin->status == 1 ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>

                        {{-- En ligne --}}
                        <td data-col="5" data-search-value="{{ $isOnline ? 'en ligne' : 'hors ligne' }}">
                            <span class="adv-online-badge {{ $isOnline ? 'online' : 'offline' }}">
                                <span class="adv-online-dot {{ $isOnline ? 'online' : 'offline' }}"></span>
                                {{ $isOnline ? 'En ligne' : 'Hors ligne' }}
                            </span>
                        </td>

                        {{-- Créé le --}}
                        <td data-col="6"
                            data-sort-value="{{ $admin->created_at }}"
                            data-search-value="{{ \Carbon\Carbon::parse($admin->created_at)->format('d/m/Y') }}">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($admin->created_at)->locale('fr')->translatedFormat('d M Y') }}
                                <span class="block text-gray-400">{{ \Carbon\Carbon::parse($admin->created_at)->format('H:i') }}</span>
                            </span>
                        </td>

                        {{-- Modifié le --}}
                        <td data-col="7"
                            data-sort-value="{{ $admin->updated_at }}"
                            data-search-value="{{ \Carbon\Carbon::parse($admin->updated_at)->format('d/m/Y') }}">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($admin->updated_at)->locale('fr')->translatedFormat('d M Y') }}
                                <span class="block text-gray-400">{{ \Carbon\Carbon::parse($admin->updated_at)->format('H:i') }}</span>
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td data-col="8" style="text-align:right">
                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                <button type="button"
                                    @click="open = !open"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-violet-50 hover:text-violet-700 hover:border-violet-300 transition-all duration-150 shadow-sm">
                                    Actions
                                    <iconify-icon icon="mdi:chevron-down" width="16" height="16"></iconify-icon>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                    class="absolute right-0 z-50 mt-1.5 w-52 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
                                    <div class="py-1">
                                        <a href="{{ $detailUrl }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-violet-50 dark:hover:bg-gray-700 hover:text-violet-700 transition-colors">
                                            <i class="fas fa-eye text-violet-500 w-4"></i>Voir le profil
                                        </a>
                                        <a href="{{ $editUrl }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-gray-700 hover:text-emerald-600 transition-colors">
                                            <i class="fas fa-edit text-emerald-500 w-4"></i>Modifier
                                        </a>
                                        <a href="{{ $messageUrl }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 transition-colors">
                                            <i class="fas fa-comment text-blue-500 w-4"></i>Message
                                        </a>
                                        <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>
                                        <div x-data="{ showConfirm: false }">
                                            <button type="button" @click="showConfirm = true"
                                                class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-red-50 dark:hover:bg-gray-700 hover:text-red-600 transition-colors">
                                                <i class="fas fa-trash text-red-500 w-4"></i>Supprimer
                                            </button>
                                            <template x-if="showConfirm">
                                                <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 backdrop-blur-sm">
                                                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-[90%] max-w-md overflow-hidden">
                                                        <div class="flex items-center justify-between p-5 bg-violet-600 dark:bg-gray-700">
                                                            <h3 class="text-lg font-semibold text-white">Confirmer la suppression</h3>
                                                            <button @click="showConfirm = false" class="text-white/80 hover:text-white">
                                                                <iconify-icon icon="mdi:close" width="22" height="22"></iconify-icon>
                                                            </button>
                                                        </div>
                                                        <div class="p-6 text-center text-gray-700 dark:text-gray-300">
                                                            <i class="fas fa-exclamation-triangle text-4xl text-amber-400 mb-3 block"></i>
                                                            Supprimer l'administrateur <strong class="text-gray-900 dark:text-white">{{ $admin->name }} {{ $admin->last_name }}</strong> ?
                                                        </div>
                                                        <div class="flex justify-between px-6 py-4 bg-gray-50 dark:bg-gray-700/50">
                                                            <button @click="showConfirm = false" class="px-5 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white text-sm rounded-lg hover:bg-gray-300 transition-colors">
                                                                Annuler
                                                            </button>
                                                            <a href="{{ url('admin/admin/delete', $admin->id) }}" class="px-5 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                                                Oui, supprimer
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr data-no-result class="adv-empty-row">
                        <td colspan="9">
                            <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                                <i class="fas fa-search text-3xl mb-3 opacity-40"></i>
                                <p class="font-medium">Aucun administrateur trouvé</p>
                                <p class="text-xs mt-1">Modifiez vos critères de recherche</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pied de tableau ── --}}
        <div class="adv-table-footer">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Total : <strong class="text-gray-700 dark:text-gray-200">{{ $getAdmin->total() }}</strong>
                administrateur{{ $getAdmin->total() > 1 ? 's' : '' }}
                — Page <strong>{{ $getAdmin->currentPage() }}</strong> / {{ $getAdmin->lastPage() }}
            </div>
            <nav>
                {{ $getAdmin->appends(request()->query())->links('vendor.pagination.tailwind') }}
            </nav>
        </div>
    </div>
</div>
@endsection
