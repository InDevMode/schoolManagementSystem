<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ClassController extends Controller
{
    public function list()
    {
        return Inertia::render('Admin/Classes/Index', [
            'classes' => ClassModel::getAllClass(15),
        ]);
    }

    public function create(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $user = auth()->user();

            $existingClass = ClassModel::getNameSingle($request->name);

            if ($existingClass) {
                return redirect()->back()->with('error', 'Une classe avec ce nom existe déjà.');
            }

            $class = new ClassModel;
            $class->name       = trim($request->name);
            $class->status     = trim($request->status);
            $class->amount     = $request->amount;
            $class->created_by = $user->id;
            // Assigner l'école de l'utilisateur connecté (null pour super admin)
            $class->school_id  = ($user->user_type !== 0) ? $user->school_id : null;
            $class->save();

            return redirect('admin/class/list')->with('success', 'Cette classe a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'une classe : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function update(Request $request, $id): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $class = ClassModel::getSingle($id);
            $existingClass = ClassModel::checkNameSingle($request->name, $id);

            if ($existingClass) {
                return redirect()->back()->with('error', 'Une classe avec ce nom existe déjà.');
            }

            if (!$class) {
                return redirect()->back()->with('error', 'Cette classe est introuvable.');
            }

            $class->name = trim($request->name);
            $class->status = $request->status;
            $class->amount = $request->amount;
            $class->save();
            return redirect('admin/class/list')->with('success', 'Cette classe a été modifiée avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification de cette classe : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function delete($id)
    {
        $class = ClassModel::getSingle($id);
        if ($class) {
            $class->is_delete = 1;
            $class->save();
            return redirect('admin/class/list')->with('success', 'Cette classe a été supprimé avec succès.');
        } else {
            abort(404);
        }
    }

}
