<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\Request;

class FeesCollectionController extends Controller
{
    //
    public function list(Request $request)
    {
        $data['header_title'] = "Perceptions de la liste des contributions";
        $data['getClass'] = ClassModel::getClass();
        if(!empty($request->all())){
            dd($request->all());
            $data['getFeesCollections'] = User::getFeesCollectionStudent(5);
        }
        return view('admin.feescollections.list', $data);
    }
}
