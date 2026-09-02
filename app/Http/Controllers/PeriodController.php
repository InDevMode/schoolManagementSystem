<?php

namespace App\Http\Controllers;

use App\Models\PeriodModel;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PeriodController extends Controller
{
    public function list()
    {
        return Inertia::render('Admin/Periods/Index', [
            'periods'  => PeriodModel::getPeriods(15),
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

        $user = Auth::user();

        // Le super admin doit fournir un school_id explicite, l'admin utilise le sien
        $schoolId = (int) $user->user_type === 0
            ? ($request->school_id ?? null)
            : $user->school_id;

        if (! $schoolId) {
            return redirect()->back()->with('error', 'Impossible de déterminer l\'école pour cette période.');
        }

        try {
            $period               = new PeriodModel();
            $period->school_id    = $schoolId;
            $period->name         = trim($request->name);
            $period->type         = $request->type;
            $period->order_number = $request->order_number;
            $period->school_year  = trim($request->school_year ?? '');
            $period->start_date   = $request->start_date;
            $period->end_date     = $request->end_date;
            $period->status       = $request->status;
            $period->created_by   = $user->id;
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

            // Vérifier que l'admin ne modifie que les périodes de son école
            $user = Auth::user();
            if ((int) $user->user_type !== 0 && $period->school_id !== $user->school_id) {
                return redirect()->back()->with('error', 'Accès refusé à cette période.');
            }

            $period->name         = trim($request->name);
            $period->type         = $request->type;
            $period->order_number = $request->order_number;
            $period->school_year  = trim($request->school_year ?? '');
            $period->start_date   = $request->start_date;
            $period->end_date     = $request->end_date;
            $period->status       = $request->status;
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

        // Vérifier que l'admin ne supprime que les périodes de son école
        $user = Auth::user();
        if ((int) $user->user_type !== 0 && $period->school_id !== $user->school_id) {
            return redirect()->back()->with('error', 'Accès refusé à cette période.');
        }

        $period->is_delete = 1;
        $period->save();

        return redirect()->back()->with('success', 'Période supprimée avec succès.');
    }

    /**
     * Marquer une période comme courante — uniquement dans l'école de l'admin.
     */
    public function setCurrent($id)
    {
        try {
            $period = PeriodModel::getSingle($id);
            if (!$period) abort(404);

            $user = Auth::user();

            // Vérifier que la période appartient à l'école de l'admin
            if ((int) $user->user_type !== 0 && $period->school_id !== $user->school_id) {
                return redirect()->back()->with('error', 'Accès refusé à cette période.');
            }

            // Désactiver uniquement les périodes courantes de LA MÊME ÉCOLE
            $schoolId = (int) $user->user_type === 0 ? $period->school_id : $user->school_id;
            PeriodModel::where('school_id', $schoolId)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            $period->is_current = true;
            $period->save();

            return redirect()->back()->with('success', "Période «{$period->name}» définie comme courante.");
        } catch (\Exception $e) {
            Log::error("Erreur set current période : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }
}
