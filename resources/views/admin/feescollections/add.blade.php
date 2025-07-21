@extends('layouts.app')
@section('content')
    <div class="container mx-auto px-4 py-5">
        @include('message')
        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-4"
            x-data="{ open: false, showConfirm: false }">
            <div>
                <h1 class=" text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-cash-register text-primary-600"></i>
                    Percevoir les frais de contributions de cet apprenant
                </h1>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Gérez la perceptions des frais de scolarité de
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
                        <div x-data="{ showConfirm: false }">
                            <button type="button" @click="showConfirm = true"
                                class="text-primary-600 hover:text-violet-600 transition-colors">
                                <i class="fas fa-plus-circle mr-1"></i> Ajouter une contribution
                            </button>
                            <!-- MODAL de confirmation -->
                            <div x-show="showConfirm" x-transition x-cloak
                                class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border-b dark:border-gray-400 w-[40%] h-auto">
                                    <div
                                        class="flex items-center justify-between p-4 border-b border-gray-200 rounded-t bg-violet-500 dark:bg-gray-700">
                                        <h3 class="text-lg font-semibold text-white dark:text-white">Ajouter une nouvelle
                                            contribution</h3>
                                        <button type="button" @click="showConfirm = false"
                                            class="text-white hover:text-gray-900 dark:hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                                            <iconify-icon icon="mdi:close" width="20" height="20"></iconify-icon>
                                        </button>
                                    </div>

                                    <form action="{{ url('admin/feescollections/store') }}" method="POST" class="m-5"
                                        enctype="multipart/form-data">
                                        {{ csrf_field() }}

                                        <div class="mb-6">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Montant total <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="text" id="amount_total" name="amount_total"
                                                    value="{{ old('amount_total', $getStudent->class_amount) }}" required disabled
                                                    class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                                    placeholder="Montant total par défaut">
                                            </div>
                                        </div>
                                        <div class="mb-6">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Montant Payé <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="text" id="amount_total" name="amount_total"
                                                    value="{{ old('amount_total', $getStudent->class_amount) }}" required disabled
                                                    class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                                    placeholder="Montant total par défaut">
                                            </div>
                                        </div>
                                        <div class="mb-6">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Montant Restant <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="text" id="amount_total" name="amount_total"
                                                    value="{{ old('amount_total', $getStudent->class_amount) }}" required disabled
                                                    class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                                    placeholder="Montant total par défaut">
                                            </div>
                                        </div>
                                        <div class="mb-6">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Montant <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="text" id="amount_total" name="amount_total"
                                                    value="{{ old('amount_total', $getStudent->class_amount) }}" required disabled
                                                    class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                                    placeholder="Montant ">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Type de paiement <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <select id="class_id" name="class_id" required
                                                    class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200">
                                                    <option selected disabled value="">Veuillez choisir un type de paiement

                                                </select>
                                                <div
                                                    class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                    <iconify-icon icon="mdi:chevron-down" class="text-gray-400" width="20"
                                                        height="20"></iconify-icon>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex justify-between px-4 py-3 rounded-b">
                                            <button @click="showConfirm = false"
                                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                                                Annuler
                                            </button>
                                            <a href=""
                                                class="px-4 py-2 bg-violet-600 text-white rounded hover:bg-violet-700 text-sm">
                                                Valider cet  ajout contribution
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
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

                    <!-- Classe Name Input -->
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Classe <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="class_id" name="class_id" required
                                class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200">
                                <option selected disabled value="">Veuillez choisir une classe

                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <iconify-icon icon="mdi:chevron-down" class="text-gray-400" width="20"
                                    height="20"></iconify-icon>
                            </div>
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
                        <a href="{{ url('admin/feescollection/collections/list') }}"
                            class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                            <i class="fas fa-sync-alt"></i>
                            Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="my-5">
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
                                Créé par
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Créé le
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Modifiée le
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="z-20 bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Sample Row 1 -->
                        <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white"></span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            FCFA</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            FCFA</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">

                            </td>
                        </tr>


                        <tr class="text-center text-gray-700 dark:text-bodydark1">
                            <td colspan="9" class="py-3"> Aucune contribution trouvée.</td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->

            <div
                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Total de <span class="font-medium"></span> classe<span class=""></span> affiché<span class=""></span>
                </div>

                <!-- Pagination -->
                <nav class="flex items-center gap-5">

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
