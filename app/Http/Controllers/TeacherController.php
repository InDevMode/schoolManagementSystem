<?php

namespace App\Http\Controllers;

use App\Exports\ExportTeacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class TeacherController extends Controller
{
    public function list(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Liste des Professeurs";
        $data['getTeacher'] = User::getAllTeacher(5);
        return view('admin.teacher.list', $data);
    }

    public function add(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Créer un nouveau professeur";
        return view('admin.teacher.add', $data);
    }

    public function create(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $teacherMail = User::getEmailSingle($request->email);
            $passwordLength = strlen($request->password);
            $regex = '/^[a-z0-9]+@[a-z0-9]+\.(fr|com|org|bj|io)$/';

            if ($teacherMail) {
                return redirect()->back()->with('error', 'Cet email a déjà été utilisé par un autre professeur');
            }
            if (!empty($request->password) && $passwordLength < 6) {
                return redirect()->back()->with('error', 'Votre mot de passe ne doit pas être de moins de 6 caractères.');
            }

            if (!preg_match($regex, $request->email)) {
                return redirect()->back()->with('error', 'Cet email est invalide. Assurez-vous qu\'il se termine par .fr, .com, .org, .bj ou .io.');
            }

            $teacher = new User;
            $teacher->name = trim($request->name);
            $teacher->last_name = trim($request->last_name);
            $teacher->email = trim($request->email);
            $teacher->marital_status = trim($request->marital_status);
            $teacher->address = trim($request->address);
            $teacher->permanent_address = trim($request->permanent_address);
            $teacher->qualification = trim($request->qualification);
            $teacher->work_experience = trim($request->work_experience);
            $teacher->admission_date = trim($request->admission_date);
            $teacher->date_of_birth = trim($request->date_of_birth);
            $teacher->note = trim($request->note);
            if (!empty($request->mobile_number)) {
                $mobileNumber = trim($request->mobile_number);
                if (!preg_match('/^\d{8,15}$/', $mobileNumber)) {
                    return redirect()->back()->with('error', 'Le numéro de téléphone doit contenir uniquement des chiffres et être compris entre 8 et 15 chiffres.');
                }
                $teacher->mobile_number = $mobileNumber;
            }
            if (!empty($request->file('profile_picture'))) {
                $ext = $request->file('profile_picture')->getClientOriginalExtension();
                $file = $request->file('profile_picture');
                $randomStr = 'teacher' . date('dmYhis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;
                $file->move('upload/profile/', $fileName);
                $teacher->profile_picture = $fileName;
            }
            $teacher->status = intval($request->status);
            $teacher->gender = trim($request->gender);
            if (!empty($request->password)) {
                $teacher->password = Hash::make($request->password);
            }
            $teacher->user_type = 2;
            $teacher->created_by = Auth::user()->id;
            $teacher->save();

            return redirect('admin/teacher/list')->with('success', 'Cet professeur a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'un professeur' : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function edit($id): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['getTeacher'] = User::getSingle($id);
        $data['header_title'] = "Modifier un professeur";
        if (!empty($data['getTeacher'])) {
            !empty($data['getTeacher']->profile_picture) ? $data['profile_picture_url'] = $data['getTeacher']->getProfile() : $data['profile_picture_url'] = asset('upload/default.jpg');
            return view('admin.teacher.edit', $data);
        } else {
            abort(404);
        }
    }

    public function update(Request $request, $id): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $teacher = User::getSingle($id);
            $teacherMail = User::checkEmailSingle($request->email, $id);
            $passwordLength = strlen($request->password);
            $regex = '/^[a-z0-9]+@[a-z0-9]+\.(fr|com|org|bj|io)$/';

            if (!$teacher) {
                return redirect()->back()->with('error', 'Cet professeur est introuvable.');
            }

            if ($teacherMail) {
                return redirect()->back()->with('error', 'Cet email a déjà été utilisé par un autre professeur');
            }
            if (!empty($request->password) && $passwordLength < 6) {
                return redirect()->back()->with('error', 'Votre mot de passe ne doit pas être de moins de 6 caractères.');
            }

            if (!preg_match($regex, $request->email)) {
                return redirect()->back()->with('error', 'Cet email est invalide. Assurez-vous qu\'il se termine par .fr, .com, .org, .bj ou .io.');
            }

            $teacher->name = trim($request->name);
            $teacher->last_name = trim($request->last_name);
            $teacher->email = trim($request->email);
            $teacher->gender = trim($request->gender);
            $teacher->date_of_birth = trim($request->date_of_birth);
            $teacher->admission_date = trim($request->admission_date);
            $teacher->marital_status = trim($request->marital_status);
            $teacher->address = trim($request->address);
            $teacher->permanent_address = trim($request->permanent_address);
            $teacher->qualification = trim($request->qualification);
            $teacher->work_experience = trim($request->work_experience);
            $teacher->note = trim($request->note);
            if (!empty($request->mobile_number)) {
                $mobileNumber = trim($request->mobile_number);
                if (!preg_match('/^\d{8,15}$/', $mobileNumber)) {
                    return redirect()->back()->with('error', 'Le numéro de téléphone doit contenir uniquement des chiffres et être compris entre 8 et 15 chiffres.');
                }
                $teacher->mobile_number = $mobileNumber;
            }
            if (!empty($request->file('profile_picture'))) {
                $teacherProfilePicture = $teacher->profile_picture;
                if (!empty($teacherProfilePicture)) {
                    $profilePictureUrl = User::getProfile();
                    if (!empty($profilePictureUrl)) {
                        unlink('upload/profile/' . $teacherProfilePicture);
                    }
                }
                $ext = $request->file('profile_picture')->getClientOriginalExtension();
                $file = $request->file('profile_picture');
                $randomStr = 'teacher' . date('dmYhis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;
                $file->move('upload/profile/', $fileName);
                $teacher->profile_picture = $fileName;
            }
            $teacher->status = intval($request->status);
            if (!empty($request->password)) {
                $teacher->password = Hash::make($request->password);
            }
            $teacher->save();

            return redirect('admin/teacher/list')->with('success', 'Cet professeur a été modifié avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification d'un professeur' : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function delete($id)
    {
        $teacher = User::getSingle($id);
        if ($teacher) {
            $teacher->is_delete = 1;
            $teacher->save();
            return redirect('admin/teacher/list')->with('success', 'Cet professeur a été supprimé avec succès.');
        } else {
            abort(404);
        }
    }

    public function exportTeacher()
    {
        return Excel::download(new ExportTeacher, 'teacher_' . date('d_m_Y') . '.xlsx');
    }

}
