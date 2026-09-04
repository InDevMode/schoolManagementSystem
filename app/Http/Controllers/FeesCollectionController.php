<?php

namespace App\Http\Controllers;

use App\Exports\ExportFeesCollection;
use App\Models\ClassModel;
use App\Models\FeesCollectionModel;
use App\Models\SettingModel;
use App\Models\User;
use App\Notifications\FeesPaymentNotification;
use App\Services\RefDataCache;
use FedaPay\FedaPay;
use FedaPay\Transaction as FedaTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class FeesCollectionController extends Controller
{
    public function list()
    {
        $perPage = min((int) request('per_page', 15), 100);
        return Inertia::render('Admin/Fees/Index', [
            'classes'          => RefDataCache::classes(),
            'feesCollections' => User::getFeesCollectionStudent($perPage),
        ]);
    }

    public function feesList()
    {
        $perPage = min((int) request('per_page', 15), 100);
        return Inertia::render('Admin/Fees/List', [
            'feesCollections' => FeesCollectionModel::getFeesCollections($perPage),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPER — Récupère les clés de paiement depuis la DB selon l'utilisateur
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Retourne les clés de paiement depuis la DB.
     * - Super admin (user_type=0) → table settings (id=1)
     * - Admin / autres → table schools (school_id de l'utilisateur connecté)
     */
    private function getPaymentConfig(): object
    {
        $user = auth()->user();

        if (!$user || (int) $user->user_type === 0) {
            // Super admin → settings globaux
            $setting = SettingModel::getSingle(1);
            return $setting ?? (object) [];
        }

        // Admin d'école et autres → école de l'utilisateur
        $school = \App\Models\School::find($user->school_id);
        if ($school) {
            return $school;
        }

        // Fallback sur les settings globaux si pas d'école trouvée
        return SettingModel::getSingle(1) ?? (object) [];
    }

    public function createFees(Request $request, $student_id)
    {
        try {
            // 🔍 Récupère les infos de l'apprenant
            $getStudent = User::getSingleClass($student_id);

            // 💰 Montant déjà payé par l'apprenant pour cette classe
            $getPaidAmount = FeesCollectionModel::getPaidAmount($student_id, $getStudent->class_id);

            // 💰 Nouveau montant à payer
            $newPayment = $request->amount;
            if ($newPayment <= 0) {
                return back()->with('error', 'Le montant doit être supérieur à 0.');
            }

            // 💰 Nouveau montant total après ce paiement
            $totalAfterPayment = $getPaidAmount + $newPayment;
            if ($totalAfterPayment > $getStudent->class_amount) {
                return back()->with('error', 'La contribution totale ne peut pas dépasser le montant requis pour la classe.');
            }

            // 🧾 Préparation des données communes
            $paymentData = [
                'class_id' => $getStudent->class_id,
                'student_id' => $student_id,
                'total_amount' => $getStudent->class_amount,
                'paid_amount' => $newPayment,
                'remaning_amount' => $getStudent->class_amount - $totalAfterPayment,
                'payment_type' => $request->payment_type,
                'remark' => $request->remark,
                'created_by' => auth()->user()->id,
            ];

            // 🔁 Traitement selon le type de paiement
            switch ($request->payment_type) {
                case 'cash':
                case 'check':
                case 'transfer':
                case 'virement':
                    $fees = new FeesCollectionModel($paymentData);
                    $fees->payment_status = 'Paid';
                    $fees->save();

                    // Notifier l'apprenant et son parent
                    $this->notifyPayment($fees, $getStudent);

                    return back()->with('success', 'Paiement enregistré avec succès.');

                case 'kkiapay':
                    $transactionId = $request->input('kkiapay_payment_id');
                    $payConfig = $this->getPaymentConfig();

                    $kkiapaySecret = $payConfig->kkiapay_secret_key ?? env('KKIAPAY_SECRET_KEY', '');
                    if (empty($kkiapaySecret)) {
                        return back()->with('error', 'Clé secrète Kkiapay non configurée. Allez dans Paramètres pour la configurer.');
                    }

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $kkiapaySecret,
                        'Accept' => 'application/json',
                    ])->get("https://api.kkiapay.me/api/v1/transactions/$transactionId");

                    if (!$response->ok() || $response['status'] !== 'SUCCESS') {
                        return back()->with('error', 'Paiement Kkiapay invalide ou échoué.');
                    }

                    $fees = new FeesCollectionModel($paymentData);
                    $fees->kkiapay_payment_id = $transactionId;
                    $fees->payment_status = 'Paid';
                    $fees->save();

                    // Notifier l'apprenant et son parent
                    $this->notifyPayment($fees, $getStudent);

                    return back()->with('success', 'Paiement Kkiapay validé et enregistré.');

                case 'paypal':
                    try {
                        $paypalUrl = $this->getAdminPaypalUrl($request, $getStudent, $student_id);
                    } catch (\RuntimeException $e) {
                        return response()->json(['error' => $e->getMessage()], 422);
                    }
                    return response()->json(['redirect_url' => $paypalUrl]);

                case 'stripe':
                    $fees = new FeesCollectionModel($paymentData);
                    try {
                        $stripeUrl = $this->redirectToAdminStripe($fees, $getStudent);
                    } catch (\RuntimeException $e) {
                        return response()->json(['error' => $e->getMessage()], 422);
                    }
                    // Retourner l'URL en JSON pour que le frontend fasse la redirection
                    return response()->json(['redirect_url' => $stripeUrl]);

                case 'fedapay':
                    try {
                        $fedapayUrl = $this->getFedapayUrl($request, $getStudent, $student_id, $paymentData);
                    } catch (\RuntimeException $e) {
                        return response()->json(['error' => $e->getMessage()], 422);
                    }
                    return response()->json(['redirect_url' => $fedapayUrl]);
            }

            return back()->with('error', 'Type de paiement non reconnu.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'ajout de contribution : " . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    private function getFedapayUrl(Request $request, $student, $student_id, array $paymentData): string
    {
        $payConfig = $this->getPaymentConfig();
        $secretKey = $payConfig->fedapay_secret_key ?? env('FEDAPAY_SECRET_KEY', '');

        if (empty($secretKey)) {
            throw new \RuntimeException('Clé secrète FedaPay non configurée. Allez dans Paramètres pour la configurer.');
        }

        FedaPay::setApiKey($secretKey);

        // Détecter l'environnement selon le préfixe de la clé
        $environment = str_starts_with($secretKey, 'sk_live') ? 'live' : 'sandbox';
        FedaPay::setEnvironment($environment);

        $transaction = FedaTransaction::create([
            'description' => 'Frais de scolarité — ' . $student->name . ' ' . $student->last_name,
            'amount'      => $request->amount,
            'currency'    => ['iso' => 'XOF'],
            'callback_url'=> url('admin/feescollections_fedapay/payment_success'),
            'customer'    => [
                'firstname' => $student->name,
                'lastname'  => $student->last_name,
                'email'     => $student->email,
            ],
        ]);

        // Stocker les données en session pour le callback
        session([
            'fedapay_pending' => array_merge($paymentData, [
                'transaction_id' => $transaction->id,
            ]),
        ]);

        return $transaction->generateToken()->url;
    }

    public function fedapayAdminSuccess(Request $request)
    {
        $pending = session('fedapay_pending');

        if ($pending) {
            $payConfig = $this->getPaymentConfig();
            $secretKey = $payConfig->fedapay_secret_key ?? env('FEDAPAY_SECRET_KEY', '');

            FedaPay::setApiKey($secretKey);
            $environment = str_starts_with($secretKey, 'sk_live') ? 'live' : 'sandbox';
            FedaPay::setEnvironment($environment);

            try {
                $transaction = FedaTransaction::retrieve($pending['transaction_id']);

                if ($transaction->status === 'approved') {
                    $fees = new FeesCollectionModel($pending);
                    $fees->payment_status     = 'Paid';
                    $fees->fedapay_transaction_id = $pending['transaction_id'];
                    $fees->save();

                    // Notifier l'apprenant et son parent
                    $student = User::find($fees->student_id);
                    if ($student) $this->notifyPayment($fees, $student);

                    session()->forget('fedapay_pending');
                    return redirect('admin/feescollections/collections/list')
                        ->with('success', 'Paiement FedaPay validé et enregistré.');
                }
            } catch (\Exception $e) {
                Log::error('FedaPay callback error: ' . $e->getMessage());
            }
        }

        return redirect('admin/feescollections/collections/list')
            ->with('error', 'Paiement FedaPay non confirmé.');
    
    
        return redirect()->away($this->getAdminPaypalUrl($request, $student, $student_id));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS PayPal — API REST Orders v2
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Retourne un access_token OAuth2 PayPal.
     */
    private function getPaypalAccessToken(string $clientId, string $secret, string $mode): string
    {
        $baseUrl = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $response = Http::withBasicAuth($clientId, $secret)
            ->asForm()
            ->post("{$baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

        if (!$response->ok()) {
            throw new \RuntimeException('Impossible d\'obtenir un token PayPal. Vérifiez vos identifiants.');
        }

        return $response->json('access_token');
    }

    /**
     * Crée un ordre PayPal (API v2) et retourne l'URL d'approbation.
     */
    private function createPaypalOrder(array $paymentData, $student, int $studentId, float $amount): string
    {
        $payConfig   = $this->getPaymentConfig();
        $clientId    = $payConfig->paypal_client_id ?? '';
        $secret      = $payConfig->paypal_secret    ?? '';
        $mode        = $payConfig->paypal_mode      ?? 'sandbox';

        if (empty($clientId) || empty($secret)) {
            throw new \RuntimeException('Identifiants PayPal non configurés. Allez dans Paramètres pour les configurer.');
        }

        $baseUrl     = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $accessToken = $this->getPaypalAccessToken($clientId, $secret, $mode);

        // Stocker les données du paiement en session pour le callback
        session(['paypal_pending' => array_merge($paymentData, [
            'student_id_ref' => $studentId,
        ])]);

        $response = Http::withToken($accessToken)
            ->post("{$baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'USD', // PayPal ne supporte pas XOF nativement
                        'value'         => number_format($amount / 655.957, 2, '.', ''), // CFA → USD approx.
                    ],
                    'description' => 'Frais scolaires — ' . $student->last_name . ' ' . $student->name,
                ]],
                'application_context' => [
                    'return_url' => url('admin/feescollections_paypal/payment_success'),
                    'cancel_url' => url('admin/feescollections_paypal/payment_error'),
                    'brand_name' => config('app.name', 'School Management'),
                    'user_action' => 'PAY_NOW',
                ],
            ]);

        if (!$response->ok()) {
            Log::error('PayPal order creation failed: ' . $response->body());
            throw new \RuntimeException('Erreur lors de la création de l\'ordre PayPal. Réessayez.');
        }

        // Récupérer l'URL d'approbation
        $approveUrl = collect($response->json('links'))
            ->firstWhere('rel', 'approve')['href'] ?? null;

        if (!$approveUrl) {
            throw new \RuntimeException('URL d\'approbation PayPal introuvable.');
        }

        return $approveUrl;
    }

    private function getAdminPaypalUrl(Request $request, $student, $student_id): string
    {
        $payConfig  = $this->getPaymentConfig();
        $clientId   = $payConfig->paypal_client_id ?? '';
        $secret     = $payConfig->paypal_secret    ?? '';

        if (empty($clientId) || empty($secret)) {
            throw new \RuntimeException('Identifiants PayPal non configurés. Allez dans Paramètres pour les configurer.');
        }

        $getPaidAmount = FeesCollectionModel::getPaidAmount($student_id, $student->class_id);

        $paymentData = [
            'class_id'        => $student->class_id,
            'student_id'      => $student_id,
            'total_amount'    => $student->class_amount,
            'paid_amount'     => $request->amount,
            'remaning_amount' => $student->class_amount - ($getPaidAmount + $request->amount),
            'payment_type'    => 'paypal',
            'remark'          => $request->remark,
            'created_by'      => auth()->user()->id,
        ];

        return $this->createPaypalOrder($paymentData, $student, $student_id, (float) $request->amount);
    }

    private function redirectToAdminStripe(FeesCollectionModel $fees, $student)
    {
        $payConfig  = $this->getPaymentConfig();
        $stripeKey  = $payConfig->stripe_secret_key ?? env('STRIPE_SECRET', '');

        if (empty($stripeKey)) {
            throw new \RuntimeException('Clé secrète Stripe non configurée. Allez dans Paramètres pour la configurer.');
        }

        Stripe::setApiKey($stripeKey);

        $session = StripeSession::create([
            'customer_email' => $student->email,
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'xof',
                        'product_data' => [
                            'name' => 'Paiement frais scolaire - ' . $student->name,
                        ],
                        'unit_amount' => $fees->paid_amount,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => url('admin/feescollections_stripe/payment_success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('admin/feescollections_stripe/payment_error'),
            'metadata' => [
                'student_id' => $fees->student_id,
                'class_id' => $fees->class_id,
                'amount' => $fees->paid_amount,
                'created_by' => $fees->created_by,
            ],
        ]);

        return $session->url;
    }

    public function paypalAdminSuccess(Request $request)
    {
        $pending = session('paypal_pending');
        $orderId = $request->get('token'); // PayPal envoie le token = order_id dans l'URL de retour

        if (!$pending || !$orderId) {
            return redirect('admin/feescollections/collections/list')
                ->with('error', 'Données de paiement PayPal introuvables.');
        }

        try {
            $payConfig   = $this->getPaymentConfig();
            $clientId    = $payConfig->paypal_client_id ?? '';
            $secret      = $payConfig->paypal_secret    ?? '';
            $mode        = $payConfig->paypal_mode      ?? 'sandbox';
            $baseUrl     = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

            $accessToken = $this->getPaypalAccessToken($clientId, $secret, $mode);

            // Capturer l'ordre PayPal
            $capture = Http::withToken($accessToken)
                ->post("{$baseUrl}/v2/checkout/orders/{$orderId}/capture");

            if (!$capture->ok() || $capture->json('status') !== 'COMPLETED') {
                Log::error('PayPal capture failed: ' . $capture->body());
                return redirect('admin/feescollections/collections/list')
                    ->with('error', 'Capture PayPal échouée. Veuillez contacter le support.');
            }

            $fees = new FeesCollectionModel($pending);
            $fees->payment_status    = 'Paid';
            $fees->payment_data      = json_encode($capture->json());
            $fees->save();

            session()->forget('paypal_pending');

            $student = User::find($fees->student_id);
            if ($student) $this->notifyPayment($fees, $student);

            return redirect('admin/feescollections/collections/list')
                ->with('success', 'Paiement PayPal validé et contribution enregistrée.');

        } catch (\Exception $e) {
            Log::error('PayPal admin success error: ' . $e->getMessage());
            return redirect('admin/feescollections/collections/list')
                ->with('error', 'Erreur lors de la validation PayPal : ' . $e->getMessage());
        }
    }

    public function stripeAdminSuccess(Request $request)
    {
        $payConfig = $this->getPaymentConfig();
        $stripeKey = $payConfig->stripe_secret_key ?? env('STRIPE_SECRET', '');
        Stripe::setApiKey($stripeKey);

        $session = StripeSession::retrieve($request->get('session_id'));

        if ($session->payment_status === 'paid') {
            $metadata = $session->metadata;

            $fees = new FeesCollectionModel;
            $fees->class_id = $metadata->class_id;
            $fees->student_id = $metadata->student_id;
            $fees->total_amount = $metadata->total_amount;
            $fees->paid_amount = $metadata->paid_amount;
            $fees->remaning_amount = $metadata->remaning_amount;
            $fees->payment_type = 'stripe';
            $fees->remark = $metadata->remark;
            $fees->created_by = $metadata->created_by;
            $fees->stripe_session_id = $session->id;
            $fees->save();

            // Notifier l'apprenant et son parent
            $student = User::find($fees->student_id);
            if ($student) $this->notifyPayment($fees, $student);

            return redirect('admin/feescollections/collections/list')
                ->with('success', 'Paiement Stripe validé et contribution enregistrée.');
        }

        return redirect()->back()->with('error', 'Paiement non validé.');
    }

    public function paypalAdminError()
    {
        return redirect('admin/feescollections/collections/list')->with('error', 'Paiement annulé ou échoué.');
    }

    public function stripeAdminError()
    {
        return redirect('admin/feescollections/collections/list')->with('error', 'Paiement annulé ou échoué.');
    }

    public function deleteFees($id)
    {
        try {
            $feecollections = FeesCollectionModel::getSingle($id);
            $feecollections->is_delete = 1;
            $feecollections->save();
            return redirect()->back()->with('success', 'Cette contribution a été supprimée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression de contribution : " . $e->getMessage());
            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }

    }

    public function myFees()
    {
        $getStudent = User::getSingleClass(Auth::user()->id);
        return Inertia::render('Student/Fees/Index', [
            'student' => $getStudent,
            'classAmount' => $getStudent->class_amount,
            'totalPaid' => FeesCollectionModel::getPaidAmount(Auth::user()->id, Auth::user()->class_id),
            'feesCollections' => FeesCollectionModel::getFees(Auth::user()->id, 15),
        ]);
    }

    public function myFeesCreate(Request $request)
    {
        try {
            // 🔍 Récupère les infos de l'apprenant, y compris sa classe et le montant total à payer
            $getStudent = User::getSingleClass(Auth::user()->id);
            // 💰 Montant déjà payé par l'apprenant pour cette classe
            $getPaidAmount = FeesCollectionModel::getPaidAmount(Auth::user()->id, Auth::user()->class_id);

            // 💰 Nouveau montant à payer
            $newPayment = $request->amount;

            // Vérifie que le montant à payer est supérieur à 0
            if ($newPayment <= 0) {
                return redirect()->back()->with('error', 'Le montant doit être supérieur à 0.');
            }

            // 💰 Nouveau montant total après ce paiement
            $totalAfterPayment = $getPaidAmount + $newPayment;
            // ✅ Vérifie que le paiement ne dépasse pas le montant total requis
            if ($totalAfterPayment > $getStudent->class_amount) {
                return redirect()->back()->with('error', 'La contribution totale ne peut pas dépasser le montant requis pour la classe.');
            }

            // Préparation des données communes
            $paymentData = [
                'class_id' => Auth::user()->class_id,
                'student_id' => Auth::user()->id,
                'total_amount' => $getStudent->class_amount,
                'paid_amount' => $newPayment,
                'remaning_amount' => $getStudent->class_amount - $totalAfterPayment,
                'payment_type' => $request->payment_type,
                'remark' => $request->remark,
                'created_by' => auth()->user()->id,
            ];

            // 🔁 Traitement selon le type de paiement
            switch ($request->payment_type) {
                case 'kkiapay':
                    $transactionId = $request->input('kkiapay_payment_id');
                    $payConfig = $this->getPaymentConfig();
                    $kkiapaySecret = $payConfig->kkiapay_secret_key ?? env('KKIAPAY_SECRET_KEY', '');

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $kkiapaySecret,
                        'Accept' => 'application/json',
                    ])->get("https://api.kkiapay.me/api/v1/transactions/$transactionId");

                    if (!$response->ok() || $response['status'] !== 'SUCCESS') {
                        return redirect()->back()->with('error', 'Paiement Kkiapay invalide ou échoué.');
                    }

                    // ✅ Paiement validé, on peut enregistrer
                    $fees = new FeesCollectionModel($paymentData);
                    $fees->kkiapay_payment_id = $transactionId;
                    $fees->payment_status = 'Paid';
                    $fees->save();

                    // Notifier l'apprenant et son parent
                    $this->notifyPayment($fees, $getStudent);

                    return redirect('student/my_fees')
                        ->with('success', 'Paiement Kkiapay validé et contribution enregistrée.');

                case 'paypal':
                    // 🔁 Redirection vers PayPal REST v2
                    $paymentData['payment_type'] = 'paypal';
                    try {
                        $paypalUrl = $this->createPaypalOrder($paymentData, $getStudent, Auth::user()->id, (float) $newPayment);
                        // Mettre à jour les URLs de retour pour l'étudiant
                        // (on recrée l'ordre avec les bonnes URLs via un helper dédié)
                        $paypalUrl = $this->createStudentPaypalOrder($paymentData, $getStudent, Auth::user()->id, (float) $newPayment);
                    } catch (\RuntimeException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    }
                    return redirect()->away($paypalUrl);

                case 'stripe':
                    // 🔁 Redirection vers Stripe avec les données en `metadata`
                    $fees = new FeesCollectionModel($paymentData); // utilisé uniquement pour passer les infos
                    return redirect()->away($this->redirectToStudentStripe($fees, $getStudent));
            }

            return redirect()->back()->with('error', 'Type de paiement non reconnu.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'ajout de contribution : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    private function createStudentPaypalOrder(array $paymentData, $student, int $studentId, float $amount): string
    {
        $payConfig   = $this->getPaymentConfig();
        $clientId    = $payConfig->paypal_client_id ?? '';
        $secret      = $payConfig->paypal_secret    ?? '';
        $mode        = $payConfig->paypal_mode      ?? 'sandbox';

        if (empty($clientId) || empty($secret)) {
            throw new \RuntimeException('Identifiants PayPal non configurés. Allez dans Paramètres pour les configurer.');
        }

        $baseUrl     = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
        $accessToken = $this->getPaypalAccessToken($clientId, $secret, $mode);

        session(['paypal_student_pending' => $paymentData]);

        $response = Http::withToken($accessToken)
            ->post("{$baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'USD',
                        'value'         => number_format($amount / 655.957, 2, '.', ''),
                    ],
                    'description' => 'Frais scolaires — ' . $student->last_name . ' ' . $student->name,
                ]],
                'application_context' => [
                    'return_url' => url('student/my_fees_paypal/payment_success'),
                    'cancel_url' => url('student/my_fees_paypal/payment_error'),
                    'brand_name' => config('app.name', 'School Management'),
                    'user_action' => 'PAY_NOW',
                ],
            ]);

        if (!$response->ok()) {
            throw new \RuntimeException('Erreur lors de la création de l\'ordre PayPal.');
        }

        return collect($response->json('links'))->firstWhere('rel', 'approve')['href']
            ?? throw new \RuntimeException('URL d\'approbation PayPal introuvable.');
    }

    private function redirectToStudentStripe(FeesCollectionModel $fees, $student): string
    {
        $payConfig = $this->getPaymentConfig();
        $stripeKey = $payConfig->stripe_secret_key ?? env('STRIPE_SECRET', '');

        if (empty($stripeKey)) {
            throw new \RuntimeException('Clé secrète Stripe non configurée. Allez dans Paramètres pour la configurer.');
        }

        Stripe::setApiKey($stripeKey);

        $session = StripeSession::create([
            'customer_email' => $student->email,
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'xof',
                        'product_data' => [
                            'name' => 'Paiement frais scolaire - ' . $student->name,
                        ],
                        'unit_amount' => $fees->paid_amount,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => url('student/my_fees_stripe/payment_success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('student/my_fees_stripe/payment_error'),
            'metadata' => [
                'student_id' => $fees->student_id,
                'class_id' => $fees->class_id,
                'total_amount' => $fees->total_amount,
                'paid_amount' => $fees->paid_amount,
                'remaning_amount' => $fees->remaning_amount,
                'remark' => $fees->remark,
                'created_by' => auth()->user()->id,
            ],
        ]);

        return $session->url;
    }

    public function paypalIPN(Request $request)
    {
        // IPN obsolète — l'API REST PayPal v2 n'utilise pas IPN.
        // Conservé pour compatibilité ascendante, ne fait rien.
        return response()->noContent();
    }

    public function paypalStudentError()
    {
        return redirect('student/my_fees')->with('error', 'Paiement annulé ou échoué.');
    }

    public function paypalStudentSuccess(Request $request)
    {
        $pending = session('paypal_student_pending');
        $orderId = $request->get('token');

        if (!$pending || !$orderId) {
            return redirect('student/my_fees')->with('error', 'Données de paiement PayPal introuvables.');
        }

        try {
            $payConfig   = $this->getPaymentConfig();
            $clientId    = $payConfig->paypal_client_id ?? '';
            $secret      = $payConfig->paypal_secret    ?? '';
            $mode        = $payConfig->paypal_mode      ?? 'sandbox';
            $baseUrl     = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

            $accessToken = $this->getPaypalAccessToken($clientId, $secret, $mode);

            $capture = Http::withToken($accessToken)
                ->post("{$baseUrl}/v2/checkout/orders/{$orderId}/capture");

            if (!$capture->ok() || $capture->json('status') !== 'COMPLETED') {
                return redirect('student/my_fees')->with('error', 'Capture PayPal échouée.');
            }

            $fees = new FeesCollectionModel($pending);
            $fees->payment_status = 'Paid';
            $fees->payment_data   = json_encode($capture->json());
            $fees->save();

            session()->forget('paypal_student_pending');

            $student = User::find($fees->student_id);
            if ($student) $this->notifyPayment($fees, $student);

            return redirect('student/my_fees')->with('success', 'Paiement PayPal validé avec succès.');

        } catch (\Exception $e) {
            Log::error('PayPal student success error: ' . $e->getMessage());
            return redirect('student/my_fees')->with('error', 'Erreur PayPal : ' . $e->getMessage());
        }
    }

    public function stripeStudentSuccess(Request $request)
    {
        $payConfig = $this->getPaymentConfig();
        $stripeKey = $payConfig->stripe_secret_key ?? env('STRIPE_SECRET', '');
        Stripe::setApiKey($stripeKey);

        $session = StripeSession::retrieve($request->get('session_id'));

        if ($session->payment_status === 'paid' && $session->status === 'complete') {
            $fees = FeesCollectionModel::find($session->metadata['fees_id']);
            if ($fees) {
                $fees->is_payment = 1;
                $fees->payment_status = 'Paid';
                $fees->payment_data = json_encode($session);
                $fees->save();

                // Notifier l'apprenant et son parent
                $student = User::find($fees->student_id);
                if ($student) $this->notifyPayment($fees, $student);
            }
            return redirect('student/my_fees')->with('success', 'Paiement validé avec succès.');
        }

        return redirect('student/my_fees')->with('error', 'Paiement non confirmé.');
    }

    public function stripeStudentError()
    {
        return redirect('student/my_fees')->with('error', 'Paiement annulé ou échoué.');
    }

    public function parentStudentFees($student_id)
    {
        $getStudent = User::getSingleClass($student_id);
        return Inertia::render('Parent/Fees/Index', [
            'student' => $getStudent,
            'classAmount' => $getStudent->class_amount,
            'totalPaid' => FeesCollectionModel::getPaidAmount($student_id, $getStudent->class_id),
        ]);
    }

    public function parentStudentFeesCreate(Request $request, $student_id)
    {

        try {
            // Récupération des informations de l'apprenant
            $getStudent = User::getSingleClass($student_id);
            $getPaidAmount = FeesCollectionModel::getPaidAmount($student_id, $getStudent->class_id);

            // Vérification si le montant est valide
            $newPayment = $request->amount;
            if ($newPayment <= 0) {
                return redirect()->back()->with('error', 'Le montant doit être supérieur à 0.');
            }

            // Calcul du montant total après paiement
            $totalAfterPayment = $getPaidAmount + $newPayment;
            // Vérification si le montant total dépasse le montant requis pour la classe
            if ($totalAfterPayment > $getStudent->class_amount) {
                return redirect()->back()->with('error', 'La contribution totale ne peut pas dépasser le montant requis pour la classe.');
            }

            // Préparation des données communes
            $paymentData = [
                'class_id' => $getStudent->class_id,
                'student_id' => $student_id,
                'total_amount' => $getStudent->class_amount,
                'paid_amount' => $newPayment,
                'remaning_amount' => $getStudent->class_amount - $totalAfterPayment,
                'payment_type' => $request->payment_type,
                'remark' => $request->remark,
                'created_by' => auth()->user()->id,
            ];

            // 🔁 Traitement selon le type de paiement
            switch ($request->payment_type) {
                case 'kkiapay':
                    $transactionId = $request->input('kkiapay_payment_id');
                    $payConfig = $this->getPaymentConfig();
                    $kkiapaySecret = $payConfig->kkiapay_secret_key ?? env('KKIAPAY_SECRET_KEY', '');

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $kkiapaySecret,
                        'Accept' => 'application/json',
                    ])->get("https://api.kkiapay.me/api/v1/transactions/$transactionId");

                    if (!$response->ok() || $response['status'] !== 'SUCCESS') {
                        return redirect()->back()->with('error', 'Paiement Kkiapay invalide ou échoué.');
                    }

                    // ✅ Paiement validé, on peut enregistrer
                    $fees = new FeesCollectionModel($paymentData);
                    $fees->kkiapay_payment_id = $transactionId;
                    $fees->payment_status = 'Paid';
                    $fees->save();

                    // Notifier l'apprenant et son parent
                    $this->notifyPayment($fees, $getStudent);

                    return redirect('parent/my_student/feescollections/' . $student_id)
                        ->with('success', 'Paiement Kkiapay validé et contribution enregistrée.');

                case 'paypal':
                    // 🔁 Redirection vers PayPal REST v2
                    $paymentData['payment_type'] = 'paypal';
                    try {
                        $paypalUrl = $this->createParentPaypalOrder($paymentData, $getStudent, $student_id, (float) $newPayment);
                    } catch (\RuntimeException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    }
                    return redirect()->away($paypalUrl);

                case 'stripe':
                    // 🔁 Redirection vers Stripe avec les données en `metadata`
                    $fees = new FeesCollectionModel($paymentData); // utilisé uniquement pour passer les infos
                    return redirect()->away($this->redirectToParentStripe($fees, $getStudent));
            }

            return redirect()->back()->with('error', 'Type de paiement non reconnu.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'ajout de contribution : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function paypalParentError()
    {
        return redirect()->back()->with('error', 'Paiement annulé ou échoué.');
    }

    public function paypalParentSuccess(Request $request)
    {
        $pending = session('paypal_parent_pending');
        $orderId = $request->get('token');

        if (!$pending || !$orderId) {
            return redirect()->back()->with('error', 'Données de paiement PayPal introuvables.');
        }

        try {
            $payConfig   = $this->getPaymentConfig();
            $clientId    = $payConfig->paypal_client_id ?? '';
            $secret      = $payConfig->paypal_secret    ?? '';
            $mode        = $payConfig->paypal_mode      ?? 'sandbox';
            $baseUrl     = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

            $accessToken = $this->getPaypalAccessToken($clientId, $secret, $mode);

            $capture = Http::withToken($accessToken)
                ->post("{$baseUrl}/v2/checkout/orders/{$orderId}/capture");

            if (!$capture->ok() || $capture->json('status') !== 'COMPLETED') {
                return redirect()->back()->with('error', 'Capture PayPal échouée.');
            }

            $fees = new FeesCollectionModel($pending);
            $fees->payment_status = 'Paid';
            $fees->payment_data   = json_encode($capture->json());
            $fees->save();

            session()->forget('paypal_parent_pending');

            $student = User::find($fees->student_id);
            if ($student) $this->notifyPayment($fees, $student);

            return redirect('parent/my_student/feescollections/' . $fees->student_id)
                ->with('success', 'Paiement PayPal validé et contribution enregistrée.');

        } catch (\Exception $e) {
            Log::error('PayPal parent success error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur PayPal : ' . $e->getMessage());
        }
    }

    public function stripeParentSuccess(Request $request)
    {
        $payConfig = $this->getPaymentConfig();
        $stripeKey = $payConfig->stripe_secret_key ?? env('STRIPE_SECRET', '');
        Stripe::setApiKey($stripeKey);

        $session = StripeSession::retrieve($request->get('session_id'));

        if ($session->payment_status === 'paid') {
            $metadata = $session->metadata;

            $fees = new FeesCollectionModel;
            $fees->class_id = $metadata->class_id;
            $fees->student_id = $metadata->student_id;
            $fees->total_amount = $metadata->total_amount;
            $fees->paid_amount = $metadata->paid_amount;
            $fees->remaning_amount = $metadata->remaning_amount;
            $fees->payment_type = 'stripe';
            $fees->remark = $metadata->remark;
            $fees->created_by = $metadata->created_by;
            $fees->stripe_session_id = $session->id;
            $fees->save();

            // Notifier l'apprenant et son parent
            $student = User::find($fees->student_id);
            if ($student) $this->notifyPayment($fees, $student);

            return redirect('parent/my_student/feescollections/' . $metadata->student_id)
                ->with('success', 'Paiement Stripe validé et contribution enregistrée.');
        }

        return redirect()->back()->with('error', 'Paiement non validé.');
    }

    public function stripeParentError()
    {
        return redirect()->back()->with('error', 'Paiement annulé ou échoué.');
    }

    private function createParentPaypalOrder(array $paymentData, $student, int $studentId, float $amount): string
    {
        $payConfig   = $this->getPaymentConfig();
        $clientId    = $payConfig->paypal_client_id ?? '';
        $secret      = $payConfig->paypal_secret    ?? '';
        $mode        = $payConfig->paypal_mode      ?? 'sandbox';

        if (empty($clientId) || empty($secret)) {
            throw new \RuntimeException('Identifiants PayPal non configurés. Allez dans Paramètres pour les configurer.');
        }

        $baseUrl     = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
        $accessToken = $this->getPaypalAccessToken($clientId, $secret, $mode);

        session(['paypal_parent_pending' => $paymentData]);

        $response = Http::withToken($accessToken)
            ->post("{$baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'USD',
                        'value'         => number_format($amount / 655.957, 2, '.', ''),
                    ],
                    'description' => 'Frais scolaires — ' . $student->last_name . ' ' . $student->name,
                ]],
                'application_context' => [
                    'return_url' => url('parent/my_fees_paypal/payment_success'),
                    'cancel_url' => url('parent/my_fees_paypal/payment_error'),
                    'brand_name' => config('app.name', 'School Management'),
                    'user_action' => 'PAY_NOW',
                ],
            ]);

        if (!$response->ok()) {
            throw new \RuntimeException('Erreur lors de la création de l\'ordre PayPal.');
        }

        return collect($response->json('links'))->firstWhere('rel', 'approve')['href']
            ?? throw new \RuntimeException('URL d\'approbation PayPal introuvable.');
    }

    private function redirectToParentStripe(FeesCollectionModel $fees, $student): string
    {
        $payConfig = $this->getPaymentConfig();
        $stripeKey = $payConfig->stripe_secret_key ?? env('STRIPE_SECRET', '');

        if (empty($stripeKey)) {
            throw new \RuntimeException('Clé secrète Stripe non configurée. Allez dans Paramètres pour la configurer.');
        }

        Stripe::setApiKey($stripeKey);

        $session = StripeSession::create([
            'customer_email' => $student->email,
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'xof',
                        'product_data' => [
                            'name' => 'Paiement frais scolaire - ' . $student->name,
                        ],
                        'unit_amount' => $fees->paid_amount,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => url('parent/my_fees_stripe/payment_success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('parent/my_fees_stripe/payment_error'),
            'metadata' => [
                'student_id' => $fees->student_id,
                'class_id' => $fees->class_id,
                'total_amount' => $fees->total_amount,
                'paid_amount' => $fees->paid_amount,
                'remaning_amount' => $fees->remaning_amount,
                'remark' => $fees->remark,
                'created_by' => auth()->user()->id,
            ],
        ]);

        return $session->url;
    }

    public function exportFeesCollects()
    {
        return Excel::download(new ExportFeesCollection, 'fees_collects_' . date('d_m_Y') . '.xlsx');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPER — Notification paiement
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Envoie une notification de paiement à l'apprenant et à son parent (si défini).
     */
    private function notifyPayment(FeesCollectionModel $fees, $student): void
    {
        try {
            $studentUser = User::find($fees->student_id);
            if (!$studentUser) return;

            $studentName = trim(($studentUser->last_name ?? '') . ' ' . ($studentUser->name ?? ''));

            $notification = new FeesPaymentNotification(
                $studentName,
                (int) $fees->paid_amount,
                (int) $fees->remaning_amount,
                $fees->payment_type ?? 'cash'
            );

            // Notifier l'apprenant
            $studentUser->notify($notification);

            // Notifier le parent une seule fois
            if ($studentUser->parent_id) {
                $parent = User::find($studentUser->parent_id);
                $parent?->notify($notification);
            }
        } catch (\Exception $e) {
            Log::warning('Fees payment notification failed: ' . $e->getMessage());
        }
    }

}
