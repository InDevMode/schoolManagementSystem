<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\FeesCollectionModel;
use App\Models\SettingModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class FeesCollectionController extends Controller
{
    public function list()
    {
        $data['header_title'] = "Perceptions des contributions";
        $data['getClass'] = ClassModel::getClass();
        $data['getFeesCollects'] = User::getFeesCollectsStudent();
        $data['classAmount'] = $data['getFeesCollects']->class_amount;
        $data['totalPaid'] = FeesCollectionModel::getPaidAmount($data['getFeesCollects']->student_id, $data['getFeesCollects']->class_id);
        $data['getFeesCollections'] = User::getFeesCollectionStudent(5);
        return view('admin.feescollections.list', $data);
    }


    public function feesList()
    {
        $data['header_title'] = "Liste des contributions reçues";
        $data['getFeesCollections'] = FeesCollectionModel::getFeesCollections(5);
        return view('admin.feescollections.feeslist', $data);
    }

    public function createFees(Request $request, $student_id)
    {

        try {
            // 🔍 Récupère les infos de l'apprenant, y compris sa classe et le montant total à payer
            $getStudent = User::getSingleClass($student_id);

            // 💰 Montant déjà payé par l'apprenant pour cette classe
            $getPaidAmount = FeesCollectionModel::getPaidAmount($student_id, $getStudent->class_id);

            // 💰 Nouveau montant total après ce paiement
            $newPayment = intval($request->amount);
            if ($newPayment <= 0) {
                return redirect()->back()->with('error', 'Le montant doit être supérieur à 0.');
            }

            $totalAfterPayment = intval($getPaidAmount) + $newPayment;

            // ✅ Vérifie que le paiement ne dépasse pas le montant total requis
            if ($totalAfterPayment > intval($getStudent->class_amount)) {
                return redirect()->back()->with('error', 'La contribution totale ne peut pas dépasser le montant requis pour la classe.');
            }

            // 🆕 Toujours créer une nouvelle ligne de paiement
            $feecollections = new FeesCollectionModel;
            $feecollections->class_id = intval($getStudent->class_id);
            $feecollections->student_id = intval($student_id);
            $feecollections->total_amount = intval($getStudent->class_amount);
            $feecollections->paid_amount = $newPayment;
            $feecollections->remaning_amount = $feecollections->total_amount - $totalAfterPayment;
            $feecollections->payment_type = $request->payment_type;
            $feecollections->remark = $request->remark;
            $feecollections->created_by = auth()->user()->id;
            $feecollections->save();

            return redirect('admin/feescollections/collections/list')->with('success', 'La contribution a été enregistrée avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'ajout de contribution : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
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
        $data['header_title'] = "Mes Contributions";
        $data['getFees'] = FeesCollectionModel::getFees(Auth::user()->id, 5);
        $data['getStudent'] = User::getSingleClass(Auth::user()->id);
        $data['classAmount'] = $data['getStudent']->class_amount;
        $data['totalPaid'] = FeesCollectionModel::getPaidAmount(Auth::user()->id, Auth::user()->class_id);
        return view('student.feescollections.myfees', $data);
    }

    public function myFeesCreate(Request $request)
    {
        // dd($request->all());
        try {
            $getStudent = User::getSingleClass(Auth::user()->id);
            $getPaidAmount = FeesCollectionModel::getPaidAmount(Auth::user()->id, Auth::user()->class_id);

            $newPayment = intval($request->amount);
            if ($newPayment <= 0) {
                return redirect()->back()->with('error', 'Le montant doit être supérieur à 0.');
            }

            $totalAfterPayment = $getPaidAmount + $newPayment;
            if ($totalAfterPayment > intval($getStudent->class_amount)) {
                return redirect()->back()->with('error', 'La contribution totale ne peut pas dépasser le montant requis pour la classe.');
            }

            $fees = new FeesCollectionModel;
            $fees->class_id = intval(Auth::user()->class_id);
            $fees->student_id = intval(Auth::user()->id);
            $fees->total_amount = intval($getStudent->class_amount);
            $fees->paid_amount = $newPayment;
            $fees->remaning_amount = $fees->total_amount - $totalAfterPayment;
            $fees->payment_type = $request->payment_type;
            $fees->remark = $request->remark;
            $fees->created_by = auth()->user()->id;

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

                    $fees->kkiapay_payment_id = $transactionId;
                    break;

                case 'paypal':
                    $fees->save();
                    return $this->redirectToPaypal($fees);

                case 'stripe':
                    $fees->save();
                    return redirect()->away($this->redirectToStripe($fees));
            }


            $fees->save();
            return redirect('student/myfees')->with('success', 'Contribution enregistrée avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'ajout de contribution : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

    }

    private function redirectToPaypal($fees)
    {
        $getSetting = SettingModel::getSingle(1);

        $query = [
            'business' => $getSetting->paypal_email,
            'cmd' => '_xclick',
            'item_name' => "Frais de scolarité",
            'no_shipping' => '1',
            'item_number' => $fees->id,
            'amount' => $fees->paid_amount,
            'currency_code' => 'XOF',
            'notify_url' => url('paypal/ipn'), // URL pour IPN
            'cancel_return' => url('student/my_fees_paypal/payment_error'),
            'return' => url('student/my_fees_paypal/payment_success'),
        ];

        $query_string = http_build_query($query);
        $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?' . $query_string;

        return redirect()->away($url);
    }


    private function redirectToStripe(FeesCollectionModel $fees): string
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = StripeSession::create([
            'customer_email' => Auth::user()->email,
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'xof',
                        'product_data' => ['name' => 'Frais de scolarité'],
                        'unit_amount' => $fees->paid_amount,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => url('student/my_fees_stripe/payment_success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('student/my_fees_stripe/payment_error'),
            'metadata' => ['fees_id' => $fees->id],
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


    public function paypalError()
    {
         return redirect()->route('student/my_fees')->with('error', 'Paiement annulé ou échoué.');
    }

    public function paypalSuccess(Request $request)
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

    public function stripeSuccess(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = StripeSession::retrieve($request->get('session_id'));

        if ($session->payment_status === 'paid') {
            $fees = FeesCollectionModel::find($session->metadata['fees_id']);
            if ($fees) {
                $fees->is_payment = 1;
                $fees->payment_status = 'Paid';
                $fees->payment_data = json_encode($session);
                $fees->save();
            }
            return redirect('student/myfees')->with('success', 'Paiement validé avec succès.');
        }

        return redirect()->route('student/my_fees')->with('error', 'Paiement non confirmé.');
    }

    public function stripeError()
{
    return redirect()->route('student/my_fees')->with('error', 'Paiement annulé ou échoué.');
}




}
