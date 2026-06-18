<?php

namespace App\Http\Controllers;

use App\Models\PeriodModel;
use App\Models\SettingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PeriodController extends Controller
{
    public function list()
    {
        return Inertia::render('Admin/Periods/Index', [
            'periods'  => PeriodModel::getPeriods(15),
            'settings' => SettingModel::getSingle(1),
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'type'         => 'required|in:semestre,trimestre',
            'order_number' => 'required|integer|min:1|max:6',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after:start_date',
            'status'       => 'required|in:0,1',
        ]);

        try {
            $period               = new PeriodModel();
            $period->name         = trim($request->name);
            $period->type         = $request->type;
            $period->order_number = intval($request->order_number);
            $period->school_year  = trim($request->school_year ?? '');
            $period->start_date   = $request->start_date;
            $period->end_date     = $request->end_date;
            $period->status       = intval($request->status);
            $period->settings_id  = intval($request->settings_id ?? 1);
            $period->created_by   = auth()->user()->id;
            $period->save();

            return redirect('admin/examinations/period/list')
                ->with('success', 'Période créée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur création période : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'type'         => 'required|in:semestre,trimestre',
            'order_number' => 'required|integer|min:1|max:6',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after:start_date',
            'status'       => 'required|in:0,1',
        ]);

        try {
            $period = PeriodModel::getSingle($id);
            if (!$period) abort(404);

            $period->name         = trim($request->name);
            $period->type         = $request->type;
            $period->order_number = intval($request->order_number);
            $period->school_year  = trim($request->school_year ?? '');
            $period->start_date   = $request->start_date;
            $period->end_date     = $request->end_date;
            $period->status       = intval($request->status);
            $period->settings_id  = intval($request->settings_id ?? $period->settings_id ?? 1);
            $period->save();

            return redirect('admin/examinations/period/list')
                ->with('success', 'Période modifiée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur modification période : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function delete($id)
    {
        $period = PeriodModel::getSingle($id);
        if (!$period) abort(404);

        $period->is_delete = 1;
        $period->save();

        return redirect()->back()->with('success', 'Période supprimée avec succès.');
    }

    /**
     * Marquer une période comme courante
     */
    public function setCurrent($id)
    {
        try {
            // Désactiver toutes les autres périodes courantes
            PeriodModel::where('is_current', true)->update(['is_current' => false]);

            $period = PeriodModel::getSingle($id);
            if (!$period) abort(404);

            $period->is_current = true;
            $period->save();

            return redirect()->back()->with('success', "Période «{$period->name}» définie comme courante.");
        } catch (\Exception $e) {
            Log::error("Erreur set current période : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }
}
