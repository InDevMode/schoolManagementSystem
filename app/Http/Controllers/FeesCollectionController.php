<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\FeesCollectionModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeesCollectionController extends Controller
{
    public function list()
    {
        $data['header_title'] = "Perceptions des contributions";
        $data['getClass'] = ClassModel::getClass();
        $data['getFeesCollections'] = User::getFeesCollectionStudent(5);
        return view('admin.feescollections.list', $data);
    }


    public function feesList()
    {
        $data['header_title'] = "Liste des contributions reçues";
        $data['getFeesCollections'] = FeesCollectionModel::getFeesCollections(5);
        return view('admin.feescollections.feeslist', $data);
    }

    public function addFees($student_id)
    {
        $data['header_title'] = "Ajouter des contributions";
        $data['getStudent'] = User::getSingleClass($student_id);
        return view('admin.feescollections.add', $data);
    }

    public function createFees(Request $request, $student_id)
    {
        try {
            // 🔍 Récupère les infos de l'élève, y compris sa classe et le montant total à payer
            $getStudent = User::getSingleClass($student_id);

            // 🔍 Vérifie s'il existe déjà une ligne de paiement pour cet élève et cette classe
            $existingFeesCollections = FeesCollectionModel::getFeesByStudentIdAndClassId($student_id, $getStudent->class_id);

            // 💰 Montant déjà payé par l'élève pour cette classe
            $getPaidAmount = FeesCollectionModel::getPaidAmount($student_id, $getStudent->class_id);

            // 💰 Nouveau montant total après ce paiement
            $newPayment = intval($request->amount);
            $totalAfterPayment = intval($getPaidAmount) + $newPayment;

            // ✅ Vérifie que le paiement ne dépasse pas le montant total requis
            if ($totalAfterPayment > intval($getStudent->class_amount)) {
                return redirect()->back()->with('error', 'La contribution totale ne peut pas dépasser le montant requis pour la classe.');
            }

            // 🧾 Si une ligne de paiement existe déjà, on la met à jour
            if ($existingFeesCollections) {
                $existingFeesCollections->paid_amount += $newPayment;
                $existingFeesCollections->remaning_amount = intval($existingFeesCollections->total_amount) - intval($existingFeesCollections->paid_amount);
                $existingFeesCollections->payment_type = $request->payment_type;
                $existingFeesCollections->remark = $request->remark;
                $existingFeesCollections->save();

                return redirect('admin/feescollections/collections/list')->with('success', 'La contribution a été mise à jour avec succès.');
            }

            // 🆕 Sinon, on crée une nouvelle ligne de paiement
            $feecollections = new FeesCollectionModel;
            $feecollections->class_id = intval($getStudent->class_id);
            $feecollections->student_id = intval($student_id);
            $feecollections->total_amount = intval($getStudent->class_amount); // ✅ Utilise le montant réel de la classe
            $feecollections->paid_amount = $newPayment;
            $feecollections->remaning_amount = $feecollections->total_amount - $feecollections->paid_amount;
            $feecollections->payment_type = $request->payment_type;
            $feecollections->remark = $request->remark;
            $feecollections->created_by = auth()->user()->id;
            $feecollections->save();

            return redirect('admin/feescollections/collections/list')->with('success', 'La contribution a été ajoutée avec succès.');

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


}
