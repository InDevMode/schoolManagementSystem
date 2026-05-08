<?php

namespace App\Http\Controllers;

use App\Exports\ExportFeesCollection;
use App\Models\ClassModel;
use App\Models\FeesCollectionModel;
use App\Models\SettingModel;
use App\Models\User;
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
        return Inertia::render('Admin/Fees/Index', [
            'classes' => ClassModel::getClass(),
            'feesCollections' => User::getFeesCollectionStudent(15),
        ]);
    }

    public function feesList()
    {
        return Inertia::render('Admin/Fees/List', [
            'feesCollections' => FeesCollectionModel::getFeesCollections(15),
        ]);
    }

    public function createFees(Request $request, $student_id)
    {
        try {
            // 🔍 Récupère les infos de l'apprenant
            $getStudent = User::getSingleClass($student_id);

            // 💰 Montant déjà payé par l'apprenant pour cette classe
            $getPaidAmount = FeesCollectionModel::getPaidAmount($student_id, $getStudent->class_id);

            // 💰 Nouveau montant à payer
            $newPayment = intval($request->amount);
            // ✅ Vérifie que le montant à payer est supérieur à 0
            if ($newPayment <= 0) {
                return redirect()->back()->with('error', 'Le montant doit être supérieur à 0.');
            }

            // 💰 Nouveau montant total après ce paiement
            $totalAfterPayment = $getPaidAmount + $newPayment;
            // ✅ Vérifie que le paiement ne dépasse pas le montant total requis
            if ($totalAfterPayment > intval($getStudent->class_amount)) {
                return redirect()->back()->with('error', 'La contribution totale ne peut pas dépasser le montant requis pour la classe.');
            }

            // 🧾 Préparation des données communes
            $paymentData = [
                'class_id' => intval($getStudent->class_id),
                'student_id' => intval($student_id),
                'total_amount' => intval($getStudent->class_amount),
                'paid_amount' => $newPayment,
                'remaning_amount' => intval($getStudent->class_amount) - $totalAfterPayment,
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
                    // ✅ Paiement manuel, on enregistre directement
                    $fees = new FeesCollectionModel($paymentData);
                    $fees->payment_status = 'Paid';
                    $fees->save();

                    return redirect('admin/feescollections/collections/list')
                        ->with('success', 'Paiement enregistré avec succès.');

                case 'kkiapay':
                    $transactionId = $request->input('kkiapay_payment_id');

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . env('KKIAPAY_SECRET'),
                        'Accept' => 'application/json',
                    ])->get("https://api.kkiapay.me/api/v1/transactions/$transactionId");

                    if (!$response->ok() || $response['status'] !== 'SUCCESS') {
                        return redirect()->back()->with('error', 'Paiement Kkiapay invalide ou échoué.');
                    }

                    $fees = new FeesCollectionModel($paymentData);
                    $fees->kkiapay_payment_id = $transactionId;
                    $fees->payment_status = 'Paid';
                    $fees->save();

                    return redirect('admin/feescollections/collections/list')
                        ->with('success', 'Paiement Kkiapay validé et enregistré.');

                case 'paypal':
                    // 🔁 Redirection vers PayPal avec les données en `custom`
                    return $this->redirectToAdminPaypal($request, $getStudent, $student_id);

                case 'stripe':
                    // 🔁 Redirection vers Stripe avec les données en `metadata`
                    $fees = new FeesCollectionModel($paymentData); // utilisé uniquement pour transmettre les infos
                    return redirect()->away($this->redirectToAdminStripe($fees, $getStudent));
            }

            return redirect()->back()->with('error', 'Type de paiement non reconnu.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'ajout de contribution : " . $e->getMessage());
            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    private function redirectToAdminPaypal(Request $request, $student, $student_id)
    {
        $getSetting = SettingModel::getSingle(1);

        $query = [
            'business' => $getSetting->paypal_email,
            'cmd' => '_xclick',
            'item_name' => "Frais de scolarité",
            'no_shipping' => '1',
            'amount' => $request->amount,
            'currency_code' => 'XOF',
            'custom' => json_encode([
                'student_id' => $student_id,
                'class_id' => $student->class_id,
                'total_amount' => $student->class_amount,
                'paid_amount' => $request->amount,
                'remaning_amount' => $student->class_amount - (FeesCollectionModel::getPaidAmount($student_id, $student->class_id) + $request->amount),
                'remark' => $request->remark,
                'created_by' => auth()->user()->id,
            ]),
            'notify_url' => url('paypal/ipn/admin'),
            'cancel_return' => url('admin/feescollections_paypal/payment_error'),
            'return' => url('admin/feescollections_paypal/payment_success'),
        ];

        $query_string = http_build_query($query);
        $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?' . $query_string;

        return redirect()->away($url);
    }

    private function redirectToAdminStripe(FeesCollectionModel $fees, $student)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

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
        if (!empty($request->custom) && $request->st === 'Completed') {
            $metadata = json_decode($request->custom);

            $fees = new FeesCollectionModel;
            $fees->class_id = intval($metadata->class_id);
            $fees->student_id = intval($metadata->student_id);
            $fees->total_amount = intval($metadata->total_amount);
            $fees->paid_amount = intval($metadata->paid_amount);
            $fees->remaning_amount = intval($metadata->remaning_amount);
            $fees->payment_type = 'paypal';
            $fees->remark = $metadata->remark;
            $fees->created_by = intval($metadata->created_by);
            $fees->payment_status = $request->st;
            $fees->payment_data = json_encode($request->all());

            $fees->save();

            return redirect('admin/feescollections/collections/list')
                ->with('success', 'Paiement PayPal validé et contribution enregistrée.');
        }

        return redirect()->back()->with('error', 'Paiement non reconnu ou incomplet.');
    }

    public function stripeAdminSuccess(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = StripeSession::retrieve($request->get('session_id'));

        if ($session->payment_status === 'paid') {
            $metadata = $session->metadata;

            $fees = new FeesCollectionModel;
            $fees->class_id = intval($metadata->class_id);
            $fees->student_id = intval($metadata->student_id);
            $fees->total_amount = intval($metadata->total_amount);
            $fees->paid_amount = intval($metadata->paid_amount);
            $fees->remaning_amount = intval($metadata->remaning_amount);
            $fees->payment_type = 'stripe';
            $fees->remark = $metadata->remark;
            $fees->created_by = intval($metadata->created_by);
            $fees->stripe_session_id = $session->id;

            $fees->save();

            return redirect('admin/feescollections/collections/list')
                ->with('success', 'Paiement Stripe validé et contribution enregistrée.');
        }

        return redirect()->back()->with('error', 'Paiement non validé.');
    }

    public function paypalAdminError()
    {
        return redirect()->route('admin/feescollections/collections/list')->with('error', 'Paiement annulé ou échoué.');
    }

    public function stripeAdminError()
    {
        return redirect()->route('admin/feescollections/collections/list')->with('error', 'Paiement annulé ou échoué.');
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
            $newPayment = intval($request->amount);

            // Vérifie que le montant à payer est supérieur à 0
            if ($newPayment <= 0) {
                return redirect()->back()->with('error', 'Le montant doit être supérieur à 0.');
            }

            // 💰 Nouveau montant total après ce paiement
            $totalAfterPayment = $getPaidAmount + $newPayment;
            // ✅ Vérifie que le paiement ne dépasse pas le montant total requis
            if ($totalAfterPayment > intval($getStudent->class_amount)) {
                return redirect()->back()->with('error', 'La contribution totale ne peut pas dépasser le montant requis pour la classe.');
            }

            // Préparation des données communes
            $paymentData = [
                'class_id' => intval(Auth::user()->class_id),
                'student_id' => intval(Auth::user()->id),
                'total_amount' => intval($getStudent->class_amount),
                'paid_amount' => $newPayment,
                'remaning_amount' => intval($getStudent->class_amount) - $totalAfterPayment,
                'payment_type' => $request->payment_type,
                'remark' => $request->remark,
                'created_by' => auth()->user()->id,
            ];

            // 🔁 Traitement selon le type de paiement
            switch ($request->payment_type) {
                case 'kkiapay':
                    $transactionId = $request->input('kkiapay_payment_id');

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . env('KKIAPAY_SECRET'),
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

                    return redirect('student/my_fees')
                        ->with('success', 'Paiement Kkiapay validé et contribution enregistrée.');

                case 'paypal':
                    // 🔁 Redirection vers PayPal avec les données en `custom`
                    return $this->redirectToStudentPaypal($request, $getStudent, Auth::user()->id);

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

    private function redirectToStudentPaypal($request, $student, $student_id)
    {
        $getSetting = SettingModel::getSingle(1);

        $query = [
            'business' => $getSetting->paypal_email,
            'cmd' => '_xclick',
            'item_name' => "Frais de scolarité",
            'no_shipping' => '1',
            'amount' => $request->amount,
            'currency_code' => 'XOF',
            'custom' => json_encode([
                'student_id' => $student_id,
                'class_id' => $student->class_id,
                'total_amount' => $student->class_amount,
                'paid_amount' => $request->amount,
                'remaning_amount' => $student->class_amount - (FeesCollectionModel::getPaidAmount($student_id, $student->class_id) + $request->amount),
                'remark' => $request->remark,
                'created_by' => auth()->user()->id,
            ]),
            'notify_url' => url('paypal/ipn'),
            'cancel_return' => url('student/my_fees_paypal/payment_error'),
            'return' => url('student/my_fees_paypal/payment_success'),
        ];

        $query_string = http_build_query($query);
        $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?' . $query_string;

        return redirect()->away($url);
    }

    private function redirectToStudentStripe(FeesCollectionModel $fees, $student): string
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

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
        $raw_post_data = file_get_contents('php://input');
        $req = 'cmd=_notify-validate&' . $raw_post_data;

        // Vérification côté PayPal
        $paypal_url = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
        $ch = curl_init($paypal_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/cert/cacert.pem'); // Certificat racine
        $res = curl_exec($ch);
        curl_close($ch);

        if (strcmp($res, "VERIFIED") === 0) {
            $item_number = $request->item_number;
            $payment_status = $request->payment_status;

            if ($payment_status === 'Completed') {
                $fees = FeesCollectionModel::find($item_number);
                if ($fees) {
                    $fees->is_payment = 1;
                    $fees->payment_status = $payment_status;
                    $fees->payment_data = json_encode($request->all());
                    $fees->save();
                }
            }
        }
    }

    public function paypalStudentError()
    {
        return redirect()->route('student/my_fees')->with('error', 'Paiement annulé ou échoué.');
    }

    public function paypalStudentSuccess(Request $request)
    {
        if (!empty($request->item_number) && $request->st == 'Completed') {
            $fees = FeesCollectionModel::getSingle($request->item_number);
            if ($fees) {
                $fees->is_payment = 1;
                $fees->payment_status = $request->st;
                $fees->payment_data = json_encode($request->all());
                $fees->save();

                return redirect('student/myfees')->with('success', 'Paiement validé avec succès.');
            }
        }
        return redirect()->back()->with('error', 'Paiement non reconnu.');
    }

    public function stripeStudentSuccess(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = StripeSession::retrieve($request->get('session_id'));

        if ($session->payment_status === 'paid' && $session->status === 'complete') {
            $fees = FeesCollectionModel::find($session->metadata['fees_id']);
            if ($fees) {
                $fees->is_payment = 1;
                $fees->payment_status = 'Paid';
                $fees->payment_data = json_encode($session);
                $fees->save();
            }
            return redirect('student/my_fees')->with('success', 'Paiement validé avec succès.');
        }

        return redirect()->route('student/my_fees')->with('error', 'Paiement non confirmé.');
    }

    public function stripeStudentError()
    {
        return redirect()->route('student/my_fees')->with('error', 'Paiement annulé ou échoué.');
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
            $newPayment = intval($request->amount);
            if ($newPayment <= 0) {
                return redirect()->back()->with('error', 'Le montant doit être supérieur à 0.');
            }

            // Calcul du montant total après paiement
            $totalAfterPayment = $getPaidAmount + $newPayment;
            // Vérification si le montant total dépasse le montant requis pour la classe
            if ($totalAfterPayment > intval($getStudent->class_amount)) {
                return redirect()->back()->with('error', 'La contribution totale ne peut pas dépasser le montant requis pour la classe.');
            }

            // Préparation des données communes
            $paymentData = [
                'class_id' => intval($getStudent->class_id),
                'student_id' => intval($student_id),
                'total_amount' => intval($getStudent->class_amount),
                'paid_amount' => $newPayment,
                'remaning_amount' => intval($getStudent->class_amount) - $totalAfterPayment,
                'payment_type' => $request->payment_type,
                'remark' => $request->remark,
                'created_by' => auth()->user()->id,
            ];

            // 🔁 Traitement selon le type de paiement
            switch ($request->payment_type) {
                case 'kkiapay':
                    $transactionId = $request->input('kkiapay_payment_id');

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . env('KKIAPAY_SECRET'),
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

                    return redirect('parent/my_student/feescollections/' . $student_id)
                        ->with('success', 'Paiement Kkiapay validé et contribution enregistrée.');

                case 'paypal':
                    // 🔁 Redirection vers PayPal avec les données en `custom`
                    return $this->redirectToParentPaypal($request, $getStudent, $student_id);

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
        if (!empty($request->custom) && $request->st === 'Completed') {
            $metadata = json_decode($request->custom);

            $fees = new FeesCollectionModel;
            $fees->class_id = intval($metadata->class_id);
            $fees->student_id = intval($metadata->student_id);
            $fees->total_amount = intval($metadata->total_amount);
            $fees->paid_amount = intval($metadata->paid_amount);
            $fees->remaning_amount = intval($metadata->remaning_amount);
            $fees->payment_type = 'paypal';
            $fees->remark = $metadata->remark;
            $fees->created_by = intval($metadata->created_by);
            $fees->payment_status = $request->st;
            $fees->payment_data = json_encode($request->all());

            $fees->save();

            return redirect('parent/my_student/feescollections/' . $metadata->student_id)
                ->with('success', 'Paiement PayPal validé et contribution enregistrée.');
        }

        return redirect()->back()->with('error', 'Paiement non reconnu ou incomplet.');
    }

    public function stripeParentSuccess(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = StripeSession::retrieve($request->get('session_id'));

        if ($session->payment_status === 'paid') {
            $metadata = $session->metadata;

            $fees = new FeesCollectionModel;
            $fees->class_id = intval($metadata->class_id);
            $fees->student_id = intval($metadata->student_id);
            $fees->total_amount = intval($metadata->total_amount);
            $fees->paid_amount = intval($metadata->paid_amount);
            $fees->remaning_amount = intval($metadata->remaning_amount);
            $fees->payment_type = 'stripe';
            $fees->remark = $metadata->remark;
            $fees->created_by = intval($metadata->created_by);
            $fees->stripe_session_id = $session->id;

            $fees->save();

            return redirect('parent/my_student/feescollections/' . $metadata->student_id)
                ->with('success', 'Paiement Stripe validé et contribution enregistrée.');
        }

        return redirect()->back()->with('error', 'Paiement non validé.');
    }

    public function stripeParentError()
    {
        return redirect()->back()->with('error', 'Paiement annulé ou échoué.');
    }

    private function redirectToParentPaypal($request, $student, $student_id)
    {
        $getSetting = SettingModel::getSingle(1);

        $query = [
            'business' => $getSetting->paypal_email,
            'cmd' => '_xclick',
            'item_name' => "Frais de scolarité",
            'no_shipping' => '1',
            'amount' => $request->amount,
            'currency_code' => 'XOF',
            'custom' => json_encode([
                'student_id' => $student_id,
                'class_id' => $student->class_id,
                'total_amount' => $student->class_amount,
                'paid_amount' => $request->amount,
                'remaning_amount' => $student->class_amount - (FeesCollectionModel::getPaidAmount($student_id, $student->class_id) + $request->amount),
                'remark' => $request->remark,
                'created_by' => auth()->user()->id,
            ]),
            'notify_url' => url('paypal/ipn'),
            'cancel_return' => url('parent/my_fees_paypal/payment_error'),
            'return' => url('parent/my_fees_paypal/payment_success'),
        ];

        $query_string = http_build_query($query);
        $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?' . $query_string;

        return redirect()->away($url);
    }

    private function redirectToParentStripe(FeesCollectionModel $fees, $student): string
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

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

}
