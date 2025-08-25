@extends('layouts.app')
@section('content')
      <div class="m-2">
            @include('message')
            <div class="container mx-auto px-4 py-8 max-w-6xl">
                  <!-- Header Section -->
                  <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <div class="mb-4 md:mb-0">
                              <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                                    <iconify-icon icon="mdi:cash-register" class="text-violet-600 mr-2" width="28"
                                          height="28"></iconify-icon>
                                    Enregistrer les informations de paramètres
                              </h1>
                              <p class="text-gray-600 dark:text-gray-300 mt-1">Remplissez les détails pour enregistrer les
                                    informations de paramètres</p>
                        </div>

                        <nav class="flex" aria-label="Breadcrumb">
                              <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                    <li class="inline-flex items-center">
                                          <a href="{{ url('admin/dashboard') }}"
                                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-violet-600 dark:text-gray-400 dark:hover:text-white">
                                                <iconify-icon icon="mdi:home" class="mr-2" width="16"
                                                      height="16"></iconify-icon>
                                                Tableau de bord
                                          </a>
                                    </li>
                                    <li>
                                          <div class="flex items-center">
                                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                                      height="16"></iconify-icon>
                                                <span
                                                      class="ml-1 text-sm font-medium text-gray-700 hover:text-violet-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Informations
                                                      de paiement</span>
                                          </div>
                                    </li>
                                    <li aria-current="page">
                                          <div class="flex items-center">
                                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                                      height="16"></iconify-icon>
                                                <span
                                                      class="ml-1 text-sm font-medium text-violet-600 dark:text-violet-600 md:ml-2">Modifier</span>
                                          </div>
                                    </li>
                              </ol>
                        </nav>
                  </div>

                  <!-- Main Form Section -->
                  <div class="bg-white rounded-xl shadow-md overflow-hidden dark:bg-gray-800 transition-colors duration-300">
                        <div class="p-6 md:p-8">
                              <form action="{{ url('admin/settings/setting_data') }}" method="post"
                                    enctype="multipart/form-data">
                                    {{ csrf_field() }}

                                    <div class="lg:flex items-center justify-between lg:space-x-3">
                                          <!-- paypal Email  -->
                                          <div class="mb-4 w-full">
                                                <div>
                                                      <label
                                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            Email Paypal <span class="text-red-500">*</span>
                                                      </label>
                                                      <div class="relative">
                                                            <input type="email" id="paypal_email" name="paypal_email"
                                                                  value="{{ old('paypal_email', $getSetting->paypal_email) }}"
                                                                  required
                                                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                                                  placeholder="Entrez votre email paypal">
                                                      </div>
                                                </div>
                                          </div>

                                          <!-- kkiapay Public Key  -->
                                          <div class="mb-4 w-full">
                                                <div>
                                                      <label
                                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            Kkiapay Public Key <span class="text-red-500">*</span>
                                                      </label>
                                                      <div class="relative">
                                                            <input type="text" id="kkiapay_public_key"
                                                                  name="kkiapay_public_key"
                                                                  value="{{ old('kkiapay_public_key', $getSetting->kkiapay_public_key) }}"
                                                                  required
                                                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                                                  placeholder="Entrez votre kkiapay public key">
                                                      </div>
                                                </div>
                                          </div>
                                    </div>

                                    <!-- kkiapay Private Key  -->
                                    <div class="mb-4">
                                          <div>
                                                <label
                                                      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                      Kkiapay Private Key <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                      <input type="text" id="kkiapay_private_key"
                                                            name="kkiapay_private_key"
                                                            value="{{ old('kkiapay_private_key', $getSetting->kkiapay_private_key) }}"
                                                            required
                                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                                            placeholder="Entrez votre kkiapay private key">
                                                </div>
                                          </div>
                                    </div>

                                    <!-- kkiapay Secret Key  -->
                                    <div class="mb-4">
                                          <div>
                                                <label
                                                      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                      Kkiapay Secret Key <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                      <input type="text" id="kkiapay_secret_key" name="kkiapay_secret_key"
                                                            value="{{ old('kkiapay_secret_key', $getSetting->kkiapay_secret_key) }}"
                                                            required
                                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                                            placeholder="Entrez votre kkiapay secret key">
                                                </div>
                                          </div>
                                    </div>

                                    <!-- stripe Public Key  -->
                                    <div class="mb-4">
                                          <div>
                                                <label
                                                      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                      Stripe Public Key <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                      <input type="text" id="stripe_public_key" name="stripe_public_key"
                                                            value="{{ old('stripe_public_key', $getSetting->stripe_public_key) }}"
                                                            required
                                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                                            placeholder="Entrez votre stripe public key">
                                                </div>
                                          </div>
                                    </div>

                                    <!-- stripe Secret Key  -->
                                    <div class="mb-4">
                                          <div>
                                                <label
                                                      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                      Stripe Secret Key <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                      <input type="text" id="stripe_secret_key" name="stripe_secret_key"
                                                            value="{{ old('stripe_secret_key', $getSetting->stripe_secret_key) }}"
                                                            required
                                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                                            placeholder="Entrez votre stripe secret key">
                                                </div>
                                          </div>
                                    </div>

                                    <!--Favicon-->
                                    <div class="lg:flex items-center justify-between lg:space-x-4">
                                          <div class="mb-6 w-full">
                                                <label
                                                      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                      Favicon
                                                </label>
                                                <div class="relative">
                                                      <input type="file" id="favicon" name="favicon"
                                                            value="{{ old('favicon') }}"
                                                            class="w-full cursor-pointer rounded-lg border-[1.5px] border-stroke bg-gray-50 font-normal outline-none transition file:mr-5 file:border-collapse file:cursor-pointer file:border-0 file:border-r file:border-solid file:border-stroke file:bg-whiter file:px-5 file:py-3 file:hover:bg-violet-400 file:hover:bg-opacity-10 focus:border-violet-400 active:border-violet-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:file:border-form-strokedark dark:file:bg-white/30 dark:file:text-white dark:focus:border-violet-400">
                                                </div>
                                          </div>

                                          <img src="{{ $favicon_url }}" alt="favicon"
                                                class="w-18 block my-3 rounded-full object-cover object-center" />
                                    </div>

                                    <!--Logo-->
                                   <div class="lg:flex items-center justify-between lg:space-x-4">
                                          <div class="mb-6 w-full">
                                                <label
                                                      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                      Logo
                                                </label>
                                                <div class="relative">
                                                      <input type="file" id="logo" name="logo"
                                                            value="{{ old('logo') }}"
                                                            class="w-full cursor-pointer rounded-lg border-[1.5px] border-stroke bg-gray-50 font-normal outline-none transition file:mr-5 file:border-collapse file:cursor-pointer file:border-0 file:border-r file:border-solid file:border-stroke file:bg-whiter file:px-5 file:py-3 file:hover:bg-violet-400 file:hover:bg-opacity-10 focus:border-violet-400 active:border-violet-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:file:border-form-strokedark dark:file:bg-white/30 dark:file:text-white dark:focus:border-violet-400">
                                                </div>
                                          </div>

                                          <img src="{{ $logo_url }}" alt="logo"
                                                class="w-18 block my-3 rounded-full border-2 object-cover object-center" />
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="mt-8">
                                          <button type="submit" id="submit-button"
                                                class="w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-700 hover:to-violet-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-opacity-50 transition-all duration-300">
                                                <iconify-icon icon="mdi:content-save-check-outline" class="mr-2"
                                                      width="20" height="20"></iconify-icon>
                                                Enregistrer les informations
                                          </button>
                                    </div>
                              </form>
                        </div>
                  </div>
            </div>
      @endsection

      <script></script>
