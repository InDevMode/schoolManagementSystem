@extends('layouts.app')
@section('content')
    <div class="m-2">
        @include('message')
        <div class="container mx-auto px-4 py-8 max-w-6xl">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                        <iconify-icon icon="fa-solid:user-shield" class="text-violet-600 mr-2" width="28"
                            height="28"></iconify-icon>
                        Créer un nouvel administrateur
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">Remplissez les détails pour créer un nouvel
                        administrateur
                    </p>
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
                                <a href="{{ url('admin/admin/list') }}"
                                    class="ml-1 text-sm font-medium text-gray-700 hover:text-violet-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Liste
                                    des administrateurs</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                    height="16"></iconify-icon>
                                <span
                                    class="ml-1 text-sm font-medium text-violet-600 md:ml-2 dark:text-violet-600">Nouveau</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Main Form Section -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden dark:bg-gray-800 transition-colors duration-300">
                <div class="p-6 md:p-8">
                    <form action="{{ url('admin/admin/add') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <!--Picture Profile -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Photo de profile
                            </label>
                            <div class="relative">
                                <input type="file" id="profile_picture" name="profile_picture"
                                    value="{{ old('profile_picture') }}"
                                    class="w-full cursor-pointer rounded-lg border-[1.5px] border-stroke bg-gray-50 font-normal outline-none transition file:mr-5 file:border-collapse file:cursor-pointer file:border-0 file:border-r file:border-solid file:border-stroke file:bg-whiter file:px-5 file:py-3 file:hover:bg-violet-400 file:hover:bg-opacity-10 focus:border-violet-400 active:border-violet-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:file:border-form-strokedark dark:file:bg-white/30 dark:file:text-white dark:focus:border-violet-400">
                            </div>
                        </div>

                        <div class="lg:flex items-center justify-between lg:space-x-3">
                            <!-- Name -->
                            <div class="mb-6 w-full">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nom <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                        class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                        placeholder="Entrez un nom">
                                </div>
                            </div>

                            <!-- Last Name -->
                            <div class="mb-6 w-full">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Prénoms <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                        required
                                        class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                        placeholder="Entrez un prénom">
                                </div>
                            </div>
                        </div>

                        <div class="lg:flex items-center justify-between lg:space-x-3">
                            <!-- Email -->
                            <div class="mb-6 w-full">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                        class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                        placeholder="Entrez un email">
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-6 w-full">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Mot de passe <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="password" name="password" value="{{ old('password') }}" required
                                        class="form-password w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                        placeholder="Entrez un mot de passe">
                                    <span class="absolute right-4 top-4 cursor-pointer"
                                        onclick="togglePasswordVisibility()">
                                        <span class="text-[24px] text-violet-600"><iconify-icon icon="mdi:lock"
                                                id="togglePasswordIcon"></iconify-icon></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Statut <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="status" name="status" required
                                    class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200">
                                    <option selected disabled value="">Veuillez choisir un status pour cet administrateur
                                    <option value="1" {{ (old('status') == '1') ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ (old('status') == '0') ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <iconify-icon icon="mdi:chevron-down" class="text-gray-400" width="20"
                                        height="20"></iconify-icon>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button type="submit"
                                class="w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-700 hover:to-violet-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-opacity-50 transition-all duration-300">
                                <iconify-icon icon="mdi:content-save-check-outline" class="mr-2" width="20"
                                    height="20"></iconify-icon>
                                Créer une nouvel administrateur
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
@endsection

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.setAttribute('icon', 'mdi:lock-open');
            } else {
                passwordInput.type = 'password';
                icon.setAttribute('icon', 'mdi:lock');
            }
        }
    </script>
