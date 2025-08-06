<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\FeesCollectionModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $data['getFees'] = FeesCollectionModel::getFees(Auth::user()->id);
        $data['getStudent'] = User::getSingleClass(Auth::user()->id);
        $data['getFeesCollections'] = FeesCollectionModel::getPaidAmount(Auth::user()->id, $data['getStudent']->class_id);
        return view('student.feescollections.myfees', $data);
    }

    public function studentFeesCreate(Request $request)
    {
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

            // 💳 Enregistrement des identifiants de transaction selon le moyen de paiement
            match ($request->payment_type) {
                'paypal' => $fees->paypal_payment_id = $request->paypal_payment_id,
                'kkiapay' => $fees->kkiapay_payment_id = $request->kkiapay_payment_id,
                'stripe' => $fees->stripe_payment_id = $request->stripe_payment_id,
                default => null, // cash ou autre
            };

            $fees->save();

            return redirect('student/myfees')->with('success', 'La contribution a été enregistrée avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'ajout de contribution : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }



}
