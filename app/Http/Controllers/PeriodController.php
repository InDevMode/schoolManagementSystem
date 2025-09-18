<?php

namespace App\Http\Controllers;

use App\Models\PeriodModel;
use App\Models\SettingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PeriodController extends Controller
{
    public function list()
    {
        $data['header_title'] = 'Liste des Periodes';
        $data['getPeriods'] = PeriodModel::getPeriods(5);
        return view('admin.period.list', $data);
    }
    public function add()
    {
        $data['header_title'] = 'Ajouter une Periode';
         $getSettings = SettingModel::getSingle(1);
         $data['getSettings'] = $getSettings ? $getSettings : null;
        return view('admin.period.add', $data);
    }
    public function create(Request $request)
    {
        try {
            $period = new PeriodModel();
            $period->name = trim($request->name);
            $period->start_date = $request->start_date;
            $period->end_date = $request->end_date;
            $period->status = intval($request->status);
            $period->settings_id = intval($request->settings_id);
            $period->created_by = auth()->user()->id;
            $period->save();

            return redirect('admin/examinations/period/list')->with('success', 'Periode créée avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'une periode : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }
    public function edit($id)
    {
        $data['header_title'] = 'Editer une Periode';
        $data['getPeriod'] = PeriodModel::getSingle($id);
        return view('admin.period.edit', $data);
    }
    public function update($id)
    {
        try {

        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification d'une periode : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }
    public function delete($id)
    {
    }
}
