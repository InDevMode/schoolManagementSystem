<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ParentController extends Controller
{
    public function list(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Liste des Parents";
        $data['getParent'] = User::getAllParent(10);
        return view('admin.parent.list', $data);
    }

    public function add(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Créer un nouveau Parent";
        return view('admin.parent.add', $data);
    }

    public function create(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $parentMail = User::getEmailSingle($request->email);
            $passwordLength = strlen($request->password);
            $regex = '/^[a-z0-9]+@[a-z0-9]+\.(fr|com|org|bj|io)$/';

            if ($parentMail) {
                return redirect()->back()->with('error', 'Cet email a déjà été utilisé par un autre parent');
            }
            if (!empty($request->password) && $passwordLength < 6) {
                return redirect()->back()->with('error', 'Votre mot de passe ne doit pas être de moins de 6 caractères.');
            }

            if (!preg_match($regex, $request->email)) {
                return redirect()->back()->with('error', 'Cet email est invalide. Assurez-vous qu\'il se termine par .fr, .com, .org, .bj ou .io.');
            }

            $parent = new User;
            $parent->name = trim($request->name);
            $parent->last_name = trim($request->last_name);
            $parent->email = trim($request->email);
            $parent->occupation = trim($request->occupation);
            $parent->address = trim($request->address);
            $parent->gender = trim($request->gender);
            if (!empty($request->mobile_number)) {
                $mobileNumber = trim($request->mobile_number);
                if (!preg_match('/^\d{8,15}$/', $mobileNumber)) {
                    return redirect()->back()->with('error', 'Le numéro de téléphone doit contenir uniquement des chiffres et être compris entre 8 et 15 chiffres.');
                }
                $parent->mobile_number = $mobileNumber;
            }
            if (!empty($request->file('profile_picture'))) {
                $ext = $request->file('profile_picture')->getClientOriginalExtension();
                $file = $request->file('profile_picture');
                $randomStr = 'parent' . date('dmYhis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;
                $file->move('upload/profile/', $fileName);
                $parent->profile_picture = $fileName;
            }
            $parent->status = intval($request->status);
            if (!empty($request->password)) {
                $parent->password = Hash::make($request->password);
            }
            $parent->user_type = 4;
            $parent->class_id = 0;
            $parent->save();

            return redirect('admin/parent/list')->with('success', 'Cet parent a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'un parent' : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function edit($id): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['getParent'] = User::getSingle($id);
        $data['header_title'] = "Modifier un Parent";
        if (!empty($data['getParent'])) {
            if (!empty($data['getParent']->profile_picture)) {
                $data['profile_picture_url'] = $data['getParent']->getProfile($data['getParent']->profile_picture);
            } else {
                $data['profile_picture_url'] = asset('upload/default.jpg');
            }
            return view('admin.parent.edit', $data);
        } else {
            abort(404);
        }
    }

    public function update(Request $request, $id): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $parent = User::getSingle($id);
            $parentMail = User::checkEmailSingle($request->email, $id);
            $passwordLength = strlen($request->password);
            $regex = '/^[a-z0-9]+@[a-z0-9]+\.(fr|com|org|bj|io)$/';

            if (!$parent) {
                return redirect()->back()->with('error', 'Cet parent est introuvable.');
            }

            if ($parentMail) {
                return redirect()->back()->with('error', 'Cet email a déjà été utilisé par un autre parent');
            }
            if (!empty($request->password) && $passwordLength < 6) {
                return redirect()->back()->with('error', 'Votre mot de passe ne doit pas être de moins de 6 caractères.');
            }

            if (!preg_match($regex, $request->email)) {
                return redirect()->back()->with('error', 'Cet email est invalide. Assurez-vous qu\'il se termine par .fr, .com, .org, .bj ou .io.');
            }

            $parent->name = trim($request->name);
            $parent->last_name = trim($request->last_name);
            $parent->email = trim($request->email);
            $parent->occupation = trim($request->occupation);
            $parent->address = trim($request->address);
            $parent->gender = trim($request->gender);
            if (!empty($request->mobile_number)) {
                $mobileNumber = trim($request->mobile_number);
                if (!preg_match('/^\d{8,15}$/', $mobileNumber)) {
                    return redirect()->back()->with('error', 'Le numéro de téléphone doit contenir uniquement des chiffres et être compris entre 8 et 15 chiffres.');
                }
                $parent->mobile_number = $mobileNumber;
            }
            if (!empty($request->file('profile_picture'))) {
                $parentProfilePicture = $parent->profile_picture;
                if (!empty($parentProfilePicture)) {
                    $profilePictureUrl = User::getProfile($parentProfilePicture);
                    if (!empty($profilePictureUrl)) {
                        unlink('upload/profile/' . $parentProfilePicture);
                    }
                }
                $ext = $request->file('profile_picture')->getClientOriginalExtension();
                $file = $request->file('profile_picture');
                $randomStr = 'parent' . date('dmYhis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;
                $file->move('upload/profile/', $fileName);
                $parent->profile_picture = $fileName;
            }
            $parent->status = intval($request->status);
            if (!empty($request->password)) {
                $parent->password = Hash::make($request->password);
            }
            $parent->save();

            return redirect('admin/parent/list')->with('success', 'Cet parent a été modifié avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification d'un parent' : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function student($id): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['parent_id'] = $id;
        $data['getStudentList'] = User::getStudentList(5);
        $data['getMyStudent'] = User::getMyStudent($id, 5);
        $data['header_title'] = "Listes des élèves du parent";
        return view('admin.parent.student', $data);
    }

    public function assignStudentParent($parent_id, $student_id): \Illuminate\Http\RedirectResponse
    {
        $student = User::getSingle($student_id);
        $student->parent_id = $parent_id;
        $student->save();
        return redirect()->back()->with('success', 'Cet élève a été assignée à un parent avec succès.');
    }

    public function desAssignStudentParent($student_id): \Illuminate\Http\RedirectResponse
    {
        $student = User::getSingle($student_id);
        $student->parent_id = null;
        $student->save();
        return redirect()->back()->with('success', 'Cet élève a été désassignée à un parent avec succès.');
    }

    public function delete($id)
    {
        $parent = User::getSingle($id);
        if ($parent) {
            $parent->is_delete = 1;
            $parent->save();
            return redirect('admin/parent/list')->with('success', 'Cet parent a été supprimé avec succès.');
        } else {
            abort(404);
        }
    }
}
