<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\FeesCollectionModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeesCollectionController extends Controller
{
    public function list(Request $request)
    {
        $data['header_title'] = "Perceptions des contributions";
        $data['getClass'] = ClassModel::getClass();

        // Récupérer les paramètres de filtre depuis la requête
        $filters = $request->only(['class_id', 'student_name', 'student_last_name']);

        // Charger la liste filtrée des apprenants
        $data['getFeesCollections'] = User::getFeesCollectionStudent(5, $filters);

        return view('admin.feescollections.list', $data);
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

            $feecollections = new FeesCollectionModel;
            $getStudent = User::getSingleClass($student_id);
            $feecollections->class_id = intval($getStudent->class_id);
            $feecollections->student_id = intval($student_id);
            $feecollections->total_amount = intval($getStudent->total_amount);
            $feecollections->paid_amount = intval($request->paid_amount);
            $feecollections->remaning_amount = intval($getStudent->total_amount) - intval($request->paid_amount);
            $feecollections->payment_type = $request->payment_type;
            $feecollections->remark = $request->remark;
            $feecollections->created_by = auth()->user()->id;
            dd($feecollections);
            $feecollections->save();

            return redirect('admin/feescollections/collections/list')->with('success', 'Cette contribution a été ajoutée avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'ajout de contribution : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }

    }


}
