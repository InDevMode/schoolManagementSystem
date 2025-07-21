<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\Request;

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
        dd($data['getStudent']);
        return view('admin.feescollections.add', $data);
    }

    public function createFees(Request $request, $student_id)
    {
    }


}
