<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function list(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Liste des admins";
        $data['getAdmin'] = User::getAllAdmin();
        return view('admin.admin.list', $data);
    }

    public function add(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Créer un admin";
        return view('admin.admin.add', $data);
    }

    public function create(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $user = new User;
            $user->name = trim($request->name);
            $user->email = trim($request->email);
            $user->password = Hash::make($request->password);
            $user->user_type = 1;
            $user->save();
            return redirect('admin/admin/list')->with('success', 'Cet administrateur a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'un utilisateur : " . $e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de la création d\'un utilisateur. Veuillez réessayer.');
        }
    }

    public function edit($id): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['getAdmin'] = User::getSingle($id);
        if (!empty($data['getAdmin'])) {
            $data['header_title'] = "Modifier un admin";
            return view('admin.admin.edit', $data);
        } else {
            abort(404);
        }
    }

    public function update(Request $request, $id): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $user = User::getSingle($id);
            $user->name = trim($request->name);
            $user->email = trim($request->email);
            if (!empty($request->password)) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
            return redirect('admin/admin/list')->with('success', 'Les informations de cet administrateur ont été modifié avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification des informations de cet utilisateur : " . $e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de la de la modification des informations utilisateur. Veuillez réessayer.');
        }
    }

    public function delete($id): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $user = User::getSingle($id);
        if ($user) {
            $user->is_delete = 1;
            $user->save();
            return redirect('admin/admin/list')->with('success', 'Cet administrateur a été supprimé avec succès.');
        } else {
            abort(404);
        }
    }
}
