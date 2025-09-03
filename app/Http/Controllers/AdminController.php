<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function list(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Liste des administrateurs";
        $data['getAdmin'] = User::getAllAdmin(5);
        return view('admin.admin.list', $data);
    }

    public function add(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Créer un administrateur";
        return view('admin.admin.add', $data);
    }

    public function create(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $adminMail = User::getEmailSingle($request->email);
            $passwordLength = strlen($request->password);
            $regex = '/^[a-z0-9]+@[a-z0-9]+\.(fr|com|org|bj|io)$/';

            if ($adminMail) {
                return redirect()->back()->with('error', 'Cet email a déjà été utilisé.');
            }
            if ($passwordLength < 6) {
                return redirect()->back()->with('error', 'Votre mot de passe ne doit pas être de moins de 6 caractères.');
            }

            if (!preg_match($regex, $request->email)) {
                return redirect()->back()->with('error', 'Cet email est invalide. Assurez-vous qu\'il se termine par .fr, .com, .org, .bj ou .io.');
            }

            $admin = new User;
            if (!empty($request->file('profile_picture'))) {
                $ext = $request->file('profile_picture')->getClientOriginalExtension();
                $file = $request->file('profile_picture');
                $randomStr = 'admin' . date('dmYhis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;
                $file->move('upload/profile/', $fileName);
                $admin->profile_picture = $fileName;
            }
            $admin->name = trim($request->name);
            $admin->last_name = trim($request->last_name);
            $admin->email = trim($request->email);
            $admin->status = trim($request->status);
            $admin->password = Hash::make($request->password);
            $admin->user_type = 1;
            $admin->save();

            return redirect('admin/admin/list')->with('success', 'Cet administrateur a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'un utilisateur : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function edit($id): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['getAdmin'] = User::getSingle($id);

        if (!empty($data['getAdmin'])) {
            $data['header_title'] = "Modifier un administrateur";
            $data['getAdmin']->profile_picture ? $data['profile_picture_url'] = $data['getAdmin']->getProfile() : $data['profile_picture_url'] = asset('upload/default.jpg');
            return view('admin.admin.edit', $data);
        } else {
            abort(404);
        }
    }

    public function update(Request $request, $id): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $admin = User::getSingle($id);
            $adminMail = User::checkEmailSingle($request->email, $id);
            $passwordLength = strlen($request->password);
            $regex = '/^[a-z0-9]+@[a-z0-9]+\.(fr|com|org|bj|io)$/';

            if (!$admin) {
                return redirect()->back()->with('error', 'Cet administrateur est introuvable.');
            }

            if ($adminMail) {
                return redirect()->back()->with('error', 'Cet email est déjà utilisé par un autre administrateur.');
            }

            if (!preg_match($regex, $request->email)) {
                return redirect()->back()->with('error', 'Cet email est invalide. Assurez-vous qu\'il se termine par .fr, .com, .org, .bj ou .io.');
            }

            if (!empty($request->password) && $passwordLength < 6) {
                return redirect()->back()->with('error', 'Votre mot de passe ne doit pas être de moins de 6 caractères.');
            }

            $admin->name = trim($request->name);
            $admin->last_name = trim($request->last_name);
            $admin->email = trim($request->email);
            $admin->status = trim($request->status);
            if (!empty($request->password)) {
                $admin->password = Hash::make($request->password);
            }
            if (!empty($request->file('profile_picture'))) {
                $adminProfilePicture = $admin->profile_picture;
                if (!empty($adminProfilePicture)) {
                    $profilePictureUrl = User::getProfile();
                    if (!empty($profilePictureUrl)) {
                        unlink('upload/profile/' . $adminProfilePicture);
                    }
                }
                $ext = $request->file('profile_picture')->getClientOriginalExtension();
                $file = $request->file('profile_picture');
                $randomStr = 'admin' . date('dmYhis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;
                $file->move('upload/profile/', $fileName);
                $admin->profile_picture = $fileName;
            }
            $admin->save();

            return redirect('admin/admin/list')->with('success', 'Les informations de cet administrateur ont été modifiées avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification des informations de cet utilisateur : " . $e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de la modification des informations utilisateur. Veuillez réessayer.');
        }

    }

    public function delete($id): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $admin = User::getSingle($id);
        if ($admin) {
            $admin->is_delete = 1;
            $admin->save();
            return redirect('admin/admin/list')->with('success', 'Cet administrateur a été supprimé avec succès.');
        } else {
            abort(404);
        }
    }

    public function test()
    {
        $data['header_title'] = "Liste des administrateurs";
        $data['getAdmin'] = User::getAllStudent(5);
        return view('admin.admin.test', $data);
    }

}
