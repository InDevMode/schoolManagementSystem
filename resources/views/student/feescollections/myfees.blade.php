@extends('layouts.app')
@section('content')
    <div class="container mx-auto px-4 py-5">
        @include('message')
        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-4"
            x-data="{ open: false, showConfirm: false }">
            <div>
                <h1 class=" text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-cash-register text-primary-600"></i>
                    Liste de mes contributions payées
                </h1>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Gérez la liste de vos contributions de frais de scolarité
                    de votre plateforme</p>
            </div>

            <nav class="flex items-center text-sm">
                <ol class="flex items-center space-x-2">
                    <li class="flex items-center">
                        <a href="{{ url('student/dashboard') }}"
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
                        <span class="text-primary-600 hover:text-violet-600 transition-colors">
                            <i class="fas fa-cash-register mr-1"></i>
                            Contributions
                        </span>
                    </li>
                </ol>
            </nav>
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
                                Status du paiement
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
                        @foreach ($getFees as $index => $fees)
                            @php
                                $statusMap = [
                                    'Pending' => [
                                        'label' => 'En attente',
                                        'classes' => 'bg-yellow-100 border-yellow-800 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    ],
                                    'Completed' => [
                                        'label' => 'Terminé',
                                        'classes' => 'bg-green-100 border-green-800 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    ],
                                    'Paid' => [
                                        'label' => 'Payé',
                                        'classes' => 'bg-blue-100 border-blue-800 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    ],
                                    'Cancelled' => [
                                        'label' => 'Annulé',
                                        'classes' => 'bg-red-100 border-red-800 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    ],
                                    'Failed' => [
                                        'label' => 'Échoué',
                                        'classes' => 'bg-gray-100 border-gray-800 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                                    ],
                                    'Refunded' => [
                                        'label' => 'Remboursé',
                                        'classes' => 'bg-purple-100 border-purple-800 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                    ],
                                    'Processing' => [
                                        'label' => 'En cours de traitement',
                                        'classes' => 'bg-indigo-100 border-indigo-800 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                                    ],
                                ];

                                $status = $fees->payment_status;
                                $label = $statusMap[$status]['label'] ?? 'Statut inconnu';
                                $classes = $statusMap[$status]['classes'] ?? 'bg-gray-100 border-gray-800 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
                            @endphp
                            <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-sm text-gray-500 dark:text-gray-400">
                                    {{ $fees->student_admission_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="">
                                            <span
                                                class="text-sm font-medium text-gray-900 dark:text-white">{{ $fees->student_name }}</span>
                                            <span
                                                class="text-sm font-medium text-gray-900 dark:text-white">{{ $fees->student_last_name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $fees->class_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ number_format($fees->total_amount, 0, ',', ' ') }}
                                                FCFA</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ number_format($fees->paid_amount, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ number_format($fees->remaning_amount, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-sm text-gray-500 dark:text-gray-400">
                                    {{ $fees->created_by_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-sm">
                                    <span class="px-3 py-1 rounded-full border-2 {{ $classes }}">{{ $label }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($fees->created_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}
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
                                                <div x-data="{ showConfirm: false, selectedStudent: null }">
                                                    <button type="button" @click="showConfirm = true"
                                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:text-violet-600 dark:hover:text-violet-600"
                                                        role="menuitem"><i class="fas fa-cash-register mr-2"></i>Ajouter un
                                                        frais</button>
                                                    <!-- MODAL de confirmation -->
                                                    <div x-show="showConfirm" x-transition x-cloak
                                                        class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
                                                        <div
                                                            class="bg-white dark:bg-gray-800 border-b border-gray-200 rounded-lg shadow-lg w-[40%] h-auto">
                                                            <div
                                                                class="flex items-center justify-between p-4 border-b border-gray-200 rounded-t bg-violet-500 dark:bg-violet-600">
                                                                <h3
                                                                    class="text-lg font-semibold text-white dark:text-white text-center">
                                                                    Ajouter une nouvelle
                                                                    contribution pour <span
                                                                        class="font-bold bg-white text-gray-900 py-1 rounded px-2 text-sm me-1">{{ $fees->student_name }}
                                                                        {{ $fees->student_last_name }} </span> de la
                                                                    classe de <span
                                                                        class="font-bold bg-white text-gray-900 py-1 rounded px-2 text-sm">{{ $fees->class_name }}</span>
                                                                </h3>
                                                                <button type="button" @click="showConfirm = false"
                                                                    class="text-white hover:text-gray-900 dark:hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                                                                    <iconify-icon icon="mdi:close" width="20"
                                                                        height="20"></iconify-icon>
                                                                </button>
                                                            </div>
                                                            <form action="{{ route('studentFeesCreate') }}" x-data="{
                                                                     totalAmount: {{ $classAmount }},
                                                                     totalPaid: {{ $totalPaid }},
                                                                    newAmount: '',
                                                                     studentName: '{{ $fees->student_name }}',
                                                                    studentLastName: '{{ $fees->student_last_name }}',
                                                                    studentEmail: '{{ $fees->student_email }}',
                                                                    studentPhone: '{{ $fees->student_phone }}',
                                                                    get remaning() {
                                                                   let remaning = this.totalAmount - this.totalPaid - this.newAmount;
                                                                   return remaning < 0 ? 0 : remaning;
                                                                 }
                                                             }" id="kkiapay-form" method="POST" class="m-5" enctype="multipart/form-data">
                                                                {{ csrf_field() }}
                                                                <input type="hidden" name="kkiapay_payment_id"
                                                                    id="kkiapay_payment_id">
                                                                <input type="hidden" name="student_first_name"
                                                                    value="{{ $fees->student_name }}">
                                                                <input type="hidden" name="student_last_name"
                                                                    value="{{ $fees->student_last_name }}">
                                                                <input type="hidden" name="student_email"
                                                                    value="{{ $fees->student_email }}">
                                                                <input type="hidden" name="student_phone"
                                                                    value="{{ $fees->student_phone }}">
                                                                <div class="mb-6">
                                                                    <label
                                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                                        Montant total <span class="text-red-500">* </span>
                                                                    </label>
                                                                    <div class="relative">
                                                                        <input type="number" id="total_amount"
                                                                            name="total_amount" x-model="totalAmount" required
                                                                            disabled readonly
                                                                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                                                            placeholder="Montant total par défaut">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-6">
                                                                    <label
                                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                                        Montant Payé <span class="text-red-500">*</span>
                                                                    </label>
                                                                    <div class="relative">
                                                                        <input type="number" id="paid_amount" name="paid_amount"
                                                                            x-model="totalPaid" required disabled readonly
                                                                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                                                            placeholder="Montant payé">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-6">
                                                                    <label
                                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                                        Montant Restant <span class="text-red-500">*</span>
                                                                    </label>
                                                                    <div class="relative">
                                                                        <input type="number" id="remaning_amount"
                                                                            name="remaning_amount" :value="remaning" required
                                                                            disabled readonly
                                                                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                                                            placeholder="Montant restant">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-6">
                                                                    <label
                                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                                        Montant <span class="text-red-500">*</span>
                                                                    </label>
                                                                    <div class="relative">
                                                                        <input type="number" id="amount" name="amount" value=""
                                                                            x-model="newAmount" required
                                                                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200"
                                                                            placeholder="Montant ">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label
                                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                                        Type de paiement <span class="text-red-500">*</span>
                                                                    </label>
                                                                    <div class="relative" x-data="paymentApp()">
                                                                        <select id="payment_type" name="payment_type"
                                                                            x-model="payment_type" required
                                                                            @change="paymentForm()"
                                                                            class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200">
                                                                            <option selected disabled value="">Veuillez
                                                                                choisir
                                                                                un type de paiement </option>
                                                                            <option value="paypal">Paypal</option>
                                                                            <option value="stripe">Stripe</option>
                                                                            <option value="kkiapay">Kkiapay</option>
                                                                        </select>
                                                                        <div
                                                                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                                            <iconify-icon icon="mdi:chevron-down"
                                                                                class="text-gray-400" width="20"
                                                                                height="20"></iconify-icon>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label
                                                                        class="mb-3 block text-sm font-medium text-black dark:text-white">
                                                                        Remarque <span class="text-meta-1">*</span>
                                                                    </label>
                                                                    <textarea name="remark"
                                                                        class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 dark:text-gray-200 dark:placeholder-gray-200 px-5 py-2.5 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">{{ old('remark') }}</textarea>
                                                                </div>
                                                                <!-- Boutons -->
                                                                <div class="flex justify-between py-3 rounded-b">
                                                                    <button type="button" @click="showConfirm = false"
                                                                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                                                                        Annuler
                                                                    </button>
                                                                    <button type="submit" id="submitBtn"
                                                                        class="block w-48 rounded-lg bg-violet-600 p-3 font-medium text-gray hover:bg-opacity-90">
                                                                        Valider
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if ($getFees->isEmpty())
                            <tr class="text-center text-gray-700 dark:text-bodydark1">
                                <td colspan="9" class="py-3"> Aucune contribution trouvée.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            @if (!empty($getFees))
                <div
                    class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Total de <span class="font-medium">{{ $getFees->count() }}</span> ligne<span
                            class="">{{ $getFees->count() > 1 ? 's' : '' }}</span> affiché<span
                            class="">{{ $getFees->count() > 1 ? 's' : '' }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

<script>
    function paymentApp() {

        return {
            payment_type: "",
            paymentForm() {
                if (this.payment_type === 'kkiapay') {
                    openKkiapayWidget({
                        amount: parseInt(this.newAmount),
                        api_key: "ae13e22072ae11f0a9bdb7f9a2ea3488", // clé publique
                        sandbox: true, // false en prod
                        theme: "#5d2e8e",
                        name: `${this.studentName} ${this.studentLastName}`,
                        phone: this.studentPhone,
                        email: this.studentEmail,
                        position: "center"
                    });
                }
            }
        }
    }
</script>
