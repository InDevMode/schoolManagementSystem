<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClassController extends Controller
{
    public function list(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Liste des classes";
        $data['getClass'] = ClassModel::getAllClass(5);
        return view('admin.class.list', $data);
    }

    public function add(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Créer une classe";
        return view('admin.class.add', $data);
    }

    public function create(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {

            $existingClass = ClassModel::getNameSingle($request->name);

            if ($existingClass) {
                return redirect()->back()->with('error', 'Une classe avec ce nom existe déjà.');
            }

            $class = new ClassModel;
            $class->name = trim($request->name);
            $class->status = trim($request->status);
            $class->created_by = auth()->user()->id;
            $class->save();

            return redirect('admin/class/list')->with('success', 'Cette classe a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'une classe : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function edit($id)
    {
        $data['getClass'] = ClassModel::getSingle($id);
        if (!empty($data['getClass'])) {
            $data['header_title'] = "Modifier une classe";
            return view('admin.class.edit', $data);
        } else {
            abort(404);
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
            $class->status = intval($request->status);
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
