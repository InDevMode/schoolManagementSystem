@extends('layouts.app')
@section('content')
    <div class="container mx-auto px-4 py-5">
        @include('message')
        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-4"
            x-data="{ open: false, showConfirm: false }">
            <div>
                <h1 class=" text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-cash-register text-primary-600"></i>
                    Liste des contributions reçues
                </h1>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Gérez la listes de perceptions des frais de scolarité de
                    votre plateforme</p>
            </div>

            <nav class="flex items-center text-sm">
                <ol class="flex items-center space-x-2">
                    <li class="flex items-center">
                        <a href="{{ url('admin/dashboard') }}"
                            class="text-primary-600 hover:text-violet-600 transition-colors">
                            <i class="fas fa-home mr-1"></i>
                            Dashboard
                        </a>
                        <span class="mx-2 text-gray-400">
                            <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                height="16"></iconify-icon>
                        </span>
                    </li>
                    <li class="flex items-center">
                        <a href="{{ url('admin/feescollections/collections/list') }}"
                            class="text-primary-600 hover:text-violet-600 transition-colors">
                            <i class="fas fa-home mr-1"></i>
                            Ajouter une contribution
                        </a>
                        <span class="mx-2 text-gray-400">
                            <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                height="16"></iconify-icon>
                        </span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-primary-600 hover:text-violet-600 transition-colors">
                            <i class="fas fa-cash-register mr-1"></i>
                            Contributions reçues
                        </span>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Filter Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-5">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-filter text-primary-600"></i>
                Filtres de recherche
            </h2>

            <form>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                    <!-- Admission number -->
                    <div>
                        <label for="student_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">N°
                            Matricule
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-id-card text-gray-400"></i>
                            </div>
                            <input type="text" id="admission_number" name="admission_number"
                                value="{{ Request::get('admission_number') }}" placeholder="N° Matricule"
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>

                    <!-- Classe Name Input -->
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Classe <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-landmark text-gray-400"></i>
                            </div>
                            <input type="text" id="class_name" name="class_name" value="{{ Request::get('class_name') }}"
                                placeholder="Nom de la classe"
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>

                    </div>

                    <!-- Student Name Input -->
                    <div>
                        <label for="student_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom de
                            l'Apprenant
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="student_name" name="student_name"
                                value="{{ Request::get('student_name') }}" placeholder="Nom"
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>

                    <!-- Student last Name Input -->
                    <div>
                        <label for="student_last_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prénoms de l'Apprenant
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="student_last_name" name="student_last_name"
                                value="{{ Request::get('student_last_name') }}" placeholder="Prénoms"
                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                        </div>
                    </div>

                    <div>
                        <label for="payment_type"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statut</label>
                        <div class="relative">
                            <select id="payment_type" name="payment_type"
                                class="appearance-none w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5 pr-8">
                                <option disabled selected>Tous les types de paiement</option>
                                <option value="check" {{ Request::get('payment_type') === 'check' ? 'selected' : '' }}>Chèque
                                </option>
                                <option value="transfer" {{ Request::get('payment_type') === 'transfer' ? 'selected' : '' }}>
                                    Virement</option>
                                <option value="cash" {{ Request::get('payment_type') === 'cash' ? 'selected' : '' }}>En
                                    espèces</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Date Created Input -->
                    <div>
                        <label for="created_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date
                            de création</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-plus text-gray-400"></i>
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
                        <a href="{{ url('admin/feescollections/feescollects/feesList') }}"
                            class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                            <i class="fas fa-sync-alt"></i>
                            Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="my-5">
            {{ $getFeesCollections->links('vendor.pagination.tailwind') }}
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
                                N° Matricule
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Nom & Prénoms
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Classe
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Montant Total
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Montant Payé
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Montant Restant
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Type de paiement
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Créé par
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Créé le
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="z-20 bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Sample Row 1 -->
                        @foreach ($getFeesCollections as $index => $feescollections)
                            @php
                                $types = [
                                    'check' => ['label' => 'Chèque', 'bg' => 'bg-violet-100', 'border' => 'border-violet-800', 'text' => 'text-violet-800', 'dark_bg' => 'dark:bg-violet-900', 'dark_text' => 'dark:text-violet-200'],
                                    'transfer' => ['label' => 'Virement', 'bg' => 'bg-green-100', 'border' => 'border-green-800', 'text' => 'text-green-800', 'dark_bg' => 'dark:bg-green-900', 'dark_text' => 'dark:text-green-200'],
                                    'cash' => ['label' => 'Espèce', 'bg' => 'bg-yellow-200', 'border' => 'border-yellow-800', 'text' => 'text-yellow-800', 'dark_bg' => 'dark:bg-yellow-900', 'dark_text' => 'dark:text-yellow-200'],
                                    'paypal' => ['label' => 'PayPal', 'bg' => 'bg-blue-100', 'border' => 'border-blue-800', 'text' => 'text-blue-800', 'dark_bg' => 'dark:bg-blue-900', 'dark_text' => 'dark:text-blue-200'],
                                    'stripe' => ['label' => 'Stripe', 'bg' => 'bg-indigo-100', 'border' => 'border-indigo-800', 'text' => 'text-indigo-800', 'dark_bg' => 'dark:bg-indigo-900', 'dark_text' => 'dark:text-indigo-200'],
                                    'kkiapay' => ['label' => 'Kkiapay', 'bg' => 'bg-pink-100', 'border' => 'border-pink-800', 'text' => 'text-pink-800', 'dark_bg' => 'dark:bg-pink-900', 'dark_text' => 'dark:text-pink-200'],
                                ];

                                $type = $types[$feescollections->payment_type] ?? ['label' => 'Inconnu', 'bg' => 'bg-gray-100', 'border' => 'border-gray-800', 'text' => 'text-gray-800', 'dark_bg' => 'dark:bg-gray-900', 'dark_text' => 'dark:text-gray-200'];
                            @endphp
                                        <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap font-medium text-sm text-gray-500 dark:text-gray-400">
                                                {{ $feescollections->student_admission_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="">
                                                        <span
                                                            class="text-sm font-medium text-gray-900 dark:text-white">{{ $feescollections->student_name }}</span>
                                                        <span
                                                            class="text-sm font-medium text-gray-900 dark:text-white">{{ $feescollections->student_last_name }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $feescollections->class_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="">
                                                        <span
                                                            class="text-sm font-medium text-gray-900 dark:text-white">  {{ number_format($feescollections->class_amount, 0, ',', ' ') }} FCFA</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="">
                                                        <span
                                                            class="text-sm font-medium text-gray-900 dark:text-white">  {{ number_format($feescollections->paid_amount, 0, ',', ' ') }} FCFA</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="">
                                                        <span
                                                            class="text-sm font-medium text-gray-900 dark:text-white">  {{ number_format($feescollections->remaning_amount, 0, ',', ' ') }} FCFA</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 border w-24 inline-flex justify-center text-xs leading-5 font-semibold rounded-full
                                                        {{ $type['bg'] }} {{ $type['border'] }} {{ $type['text'] }} {{ $type['dark_bg'] }} {{ $type['dark_text'] }}">
                                                        {{ $type['label'] }}
                                                    </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap font-medium text-sm text-gray-500 dark:text-gray-400">
                                                {{ $feescollections->created_by_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($feescollections->created_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="relative inline-block text-left" x-data="{ open: false, showConfirm: false }">
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

                                                            <div x-data="{ showConfirm: false }">
                                                                <button type="button" @click="showConfirm = true"
                                                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:text-violet-600 dark:hover:text-violet-600"
                                                                    role="menuitem"><i class="fas fa-eye mr-2"></i>Voir</button>
                                                                <!-- MODAL de confirmation -->
                                                                <div x-show="showConfirm" x-transition x-cloak
                                                                    class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
                                                                    <div
                                                                        class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden">
                                                                        <!-- Header -->
                                                                        <div
                                                                            class="flex items-center justify-between px-6 py-4 bg-violet-600 dark:bg-violet-700">
                                                                            <h3 class="text-xl font-bold text-white">📄 Détails de la
                                                                                contribution</h3>
                                                                            <button @click="showConfirm = false"
                                                                                class="text-white hover:text-gray-300 transition duration-200">
                                                                                <iconify-icon icon="mdi:close" width="24"
                                                                                    height="24"></iconify-icon>
                                                                            </button>
                                                                        </div>

                                                                        <!-- Body -->
                                                                        <div class="px-6 py-6 space-y-6 text-gray-700 dark:text-gray-200">
                                                                            <!-- Élève -->
                                                                            <div class="grid grid-cols-2 gap-4">
                                                                                <div>
                                                                                    <p
                                                                                        class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                                                                        Nom</p>
                                                                                    <p class="text-base font-medium">
                                                                                        {{ $feescollections->student_name }}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p
                                                                                        class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                                                                        Prénoms</p>
                                                                                    <p class="text-base font-medium">
                                                                                        {{ $feescollections->student_last_name }}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p
                                                                                        class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                                                                        Matricule</p>
                                                                                    <p class="text-base font-medium">
                                                                                        {{ $feescollections->student_admission_number }}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p
                                                                                        class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                                                                        Classe</p>
                                                                                    <p class="text-base font-medium">
                                                                                        {{ $feescollections->class_name }}</p>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Paiement -->
                                                                            <div class="grid grid-cols-3 gap-4">
                                                                                <div>
                                                                                    <p
                                                                                        class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                                                                        Montant total</p>
                                                                                    <p
                                                                                        class="text-base font-medium text-blue-600 dark:text-blue-400">
                                                                                        {{ number_format($feescollections->class_amount, 0, ',', ' ') }}
                                                                                        FCFA
                                                                                    </p>
                                                                                </div>
                                                                                <div>
                                                                                    <p
                                                                                        class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                                                                        Montant payé</p>
                                                                                    <p
                                                                                        class="text-base font-medium text-green-600 dark:text-green-400">
                                                                                        {{ number_format($feescollections->paid_amount, 0, ',', ' ') }}
                                                                                        FCFA
                                                                                    </p>
                                                                                </div>
                                                                                <div>
                                                                                    <p
                                                                                        class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                                                                        Montant restant</p>
                                                                                    <p
                                                                                        class="text-base font-medium text-red-600 dark:text-red-400">
                                                                                        {{ number_format($feescollections->remaning_amount, 0, ',', ' ') }}
                                                                                        FCFA
                                                                                    </p>
                                                                                </div>
                                                                            </div>

                                                                            @php
                                                                                $types = [
                                                                                    'check' => ['label' => 'Chèque', 'bg' => 'bg-violet-100', 'border' => 'border-violet-800', 'text' => 'text-violet-800', 'dark_bg' => 'dark:bg-violet-900', 'dark_text' => 'dark:text-violet-200'],
                                                                                    'transfer' => ['label' => 'Virement', 'bg' => 'bg-green-100', 'border' => 'border-green-800', 'text' => 'text-green-800', 'dark_bg' => 'dark:bg-green-900', 'dark_text' => 'dark:text-green-200'],
                                                                                    'cash' => ['label' => 'Espèce', 'bg' => 'bg-yellow-200', 'border' => 'border-yellow-800', 'text' => 'text-yellow-800', 'dark_bg' => 'dark:bg-yellow-900', 'dark_text' => 'dark:text-yellow-200'],
                                                                                    'paypal' => ['label' => 'PayPal', 'bg' => 'bg-blue-100', 'border' => 'border-blue-800', 'text' => 'text-blue-800', 'dark_bg' => 'dark:bg-blue-900', 'dark_text' => 'dark:text-blue-200'],
                                                                                    'stripe' => ['label' => 'Stripe', 'bg' => 'bg-indigo-100', 'border' => 'border-indigo-800', 'text' => 'text-indigo-800', 'dark_bg' => 'dark:bg-indigo-900', 'dark_text' => 'dark:text-indigo-200'],
                                                                                    'kkiapay' => ['label' => 'Kkiapay', 'bg' => 'bg-pink-100', 'border' => 'border-pink-800', 'text' => 'text-pink-800', 'dark_bg' => 'dark:bg-pink-900', 'dark_text' => 'dark:text-pink-200'],
                                                                                ];

                                                                                $type = $types[$feescollections->payment_type] ?? ['label' => 'Inconnu', 'bg' => 'bg-gray-100', 'border' => 'border-gray-800', 'text' => 'text-gray-800', 'dark_bg' => 'dark:bg-gray-900', 'dark_text' => 'dark:text-gray-200'];
                                                                            @endphp

                                                                            <!-- Type de paiement & Métadonnées -->
                                                                            <div class="grid grid-cols-2 gap-4 items-center">
                                                                                <div>
                                                                                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Type de paiement</p>
                                                                                        <span class="px-2 py-1 border w-24 inline-flex justify-center text-xs leading-5 font-semibold rounded-full
                                                                                            {{ $type['bg'] }} {{ $type['border'] }} {{ $type['text'] }} {{ $type['dark_bg'] }} {{ $type['dark_text'] }}">
                                                                                            {{ $type['label'] }}
                                                                                        </span>
                                                                                </div>
                                                                                <div>
                                                                                    <p
                                                                                        class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                                                                        Saisi par</p>
                                                                                    <p class="text-base font-medium">
                                                                                        {{ $feescollections->created_by_name }}</p>
                                                                                </div>
                                                                                <div>
                                                                                    <p
                                                                                        class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                                                                        Date de création</p>
                                                                                    <p class="text-base font-medium">
                                                                                        {{ \Carbon\Carbon::parse($feescollections->created_at)->format('d/m/Y à H:i') }}
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div x-data="{ showDelete: false }">
                                                                <button type="button" @click="showDelete = true"
                                                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:text-red-600 dark:hover:text-red-600"
                                                                    role="menuitem"><i class="fas fa-trash-alt mr-2"></i>Supprimer</button>
                                                                <!-- MODAL de confirmation -->
                                                                <template x-if="showDelete">
                                                                    <div
                                                                        class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
                                                                        <div
                                                                            class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden">
                                                                            <div
                                                                                class="flex items-center justify-between p-4 border-b dakr:border-gray-600 border-gray-200 rounded-t bg-violet-500 dark:bg-gray-700">
                                                                                <h3
                                                                                    class="text-lg font-semibold text-white dark:text-white">
                                                                                    Supprimer la contribution
                                                                                </h3>
                                                                                <button type="button" @click="showDelete = false"
                                                                                    class="text-white hover:text-gray-900 dark:hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                                                                                    <iconify-icon icon="mdi:close" width="20"
                                                                                        height="20"></iconify-icon>
                                                                                </button>
                                                                            </div>

                                                                            <!-- Message -->
                                                                            <div class="p-4">
                                                                                <div
                                                                                    class="text-center text-lg text-gray-800 dark:text-gray-200">
                                                                                    <p> Êtes-vous sûr de vouloir supprimer
                                                                                        la contribution
                                                                                    </p>
                                                                                    <p>de l'apprenant ayant le matricule</p>
                                                                                    <p class="font-bold">
                                                                                        {{ $feescollections->student_admission_number }}
                                                                                        ?
                                                                                    </p>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Pied du modal -->
                                                                            <div class="flex justify-between px-4 py-3 rounded-b">
                                                                                <button @click="showDelete = false"
                                                                                    class="block px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                                                                                    Annuler
                                                                                </button>
                                                                                <a href="{{ url('admin/feescollections/delete', $feescollections->id) }}"
                                                                                    class="block px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm">
                                                                                    Oui supprimer
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
                        @endforeach
                        @if ($getFeesCollections->isEmpty())
                            <tr class="text-center text-gray-700 dark:text-bodydark1">
                                <td colspan="9" class="py-3"> Aucune contribution trouvée.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div
                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Total de <span class="font-medium">{{ $getFeesCollections->total() }}</span>
                    contribution{{ $getFeesCollections->total() > 1 ? 's' : '' }}<span
                        class="">{{ $getFeesCollections->total() > 1 ? 's' : '' }}</span> affiché<span
                        class="">{{ $getFeesCollections->total() > 1 ? 's' : '' }}</span>
                </div>

                <!-- Pagination -->
                <nav class="flex items-center gap-5">
                    {{ $getFeesCollections->links('vendor.pagination.tailwind') }}
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
