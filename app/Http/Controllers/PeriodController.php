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
        $data['header_title'] = 'Liste des Périodes';
        $data['getPeriods'] = PeriodModel::getPeriods(5);
        return view('admin.examinations.period.list', $data);
    }

    public function add()
    {
        $data['header_title'] = 'Ajouter une Période';
        $getSettings = SettingModel::getSingle(1);
        $data['getSettings'] = $getSettings ?: null;
        return view('admin.examinations.period.add', $data);
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

            return redirect('admin/examinations/period/list')->with('success', 'Période créée avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'une période : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function edit($id)
    {
        $data['header_title'] = 'Editer une Periode';
        $data['getPeriod'] = PeriodModel::getSingle($id);
        $getSettings = SettingModel::getSingle(1);
        $data['getSettings'] = $getSettings ?: null;
        return view('admin.examinations.period.edit', $data);
    }

    public function update(Request $request, $id)
    {
        try {

            $existingPeriod = PeriodModel::getSingle($id);
            $existingPeriod->name = trim($request->name);
            $existingPeriod->start_date = $request->start_date;
            $existingPeriod->end_date = $request->end_date;
            $existingPeriod->status = intval($request->status);
            $existingPeriod->settings_id = intval($request->settings_id);
            $existingPeriod->save();

            return redirect('admin/examinations/period/list')->with('success', 'Période créée avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification d'une période : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }
    public function delete($id)
    {
        $period = PeriodModel::getSingle($id);
        if ($period) {
            $period->is_delete = 1;
            $period->save();
            return redirect()->back()->with('success', 'Période supprimé avec succès.');
        } else {
            abort(404);
        }
    }



}
