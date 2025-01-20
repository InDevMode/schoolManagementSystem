<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function list(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['getStudent'] = User::getAllStudent(10);
        $data['header_title'] = "Liste des Elèves";
        return view('admin.student.list', $data);
    }

    public function add(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Créer un nouvel élève";
        $data['getClass'] = ClassModel::getClass();
        return view('admin.student.add', $data);
    }

    public function create(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $studentMail = User::getEmailSingle($request->email);
            $passwordLength = strlen($request->password);
            $regex = '/^[a-z0-9]+@[a-z0-9]+\.(fr|com|org|bj|io)$/';

            if ($studentMail) {
                return redirect()->back()->with('error', 'Cet email a déjà été utilisé par un autre élève');
            }
            if (!empty($request->password) && $passwordLength < 6) {
                return redirect()->back()->with('error', 'Votre mot de passe ne doit pas être de moins de 6 caractères.');
            }

            if (!preg_match($regex, $request->email)) {
                return redirect()->back()->with('error', 'Cet email est invalide. Assurez-vous qu\'il se termine par .fr, .com, .org, .bj ou .io.');
            }

            $student = new User;
            $student->name = trim($request->name);
            $student->last_name = trim($request->last_name);
            $student->email = trim($request->email);
            $student->admission_number = trim($request->admission_number);
            $student->roll_number = trim($request->roll_number);
            $student->class_id = intval($request->class_id);
            $student->gender = trim($request->gender);
            if (!empty($request->date_of_birth)) {
                $dateOfBirth = Carbon::parse(trim($request->date_of_birth));
                $minimumAge = 2;
                $age = $dateOfBirth->diffInYears(Carbon::now());
                if ($age < $minimumAge) {
                    return redirect()->back()->with('error', 'L\'élève doit avoir au moins 2 ans.');
                }
                $student->date_of_birth = $dateOfBirth;
            }
            $student->caste = trim($request->caste);
            $student->religion = trim($request->religion);
            if (!empty($request->mobile_number)) {
                $mobileNumber = trim($request->mobile_number);
                if (!preg_match('/^\d{8,15}$/', $mobileNumber)) {
                    return redirect()->back()->with('error', 'Le numéro de téléphone doit contenir uniquement des chiffres et être compris entre 8 et 15 chiffres.');
                }
                $student->mobile_number = $mobileNumber;
            }
            if (!empty($request->admission_date)) {
                $student->admission_date = trim($request->admission_date);
            }
            if (!empty($request->file('profile_picture'))) {
                $ext = $request->file('profile_picture')->getClientOriginalExtension();
                $file = $request->file('profile_picture');
                $randomStr = date('dmYhis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;
                $file->move('upload/profile/', $fileName);
                $student->profile_picture = $fileName;
            }
            $student->blood_group = trim($request->blood_group);
            $student->height = trim($request->height);
            $student->weight = trim($request->weight);
            $student->status = intval($request->status);
            if (!empty($request->password)) {
                $student->password = Hash::make($request->password);
            }
            $student->user_type = 3;
            $student->save();

            return redirect('admin/student/list')->with('success', 'Cet élève a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'un élève' : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function edit($id)
    {
        $data['getStudent'] = User::getSingle($id);
        if (!empty($data['getStudent'])) {
            $data['getClass'] = ClassModel::getClass();
            if (!empty($data['getStudent']->profile_picture)) {
                $data['profile_picture_url'] = $data['getStudent']->getProfile($data['getStudent']->profile_picture);
            }else{
                $data['profile_picture_url'] = '';
            }
            $data['header_title'] = "Modifier un élève";
            return view('admin.student.edit', $data);
        } else {
            abort(404);
        }
    }

    public function update(Request $request, $id): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $student = User::getSingle($id);
            $studentMail = User::checkEmailSingle($request->email, $id);
            $passwordLength = strlen($request->password);
            $regex = '/^[a-z0-9]+@[a-z0-9]+\.(fr|com|org|bj|io)$/';

            if (!$student) {
                return redirect()->back()->with('error', 'Cet élève est introuvable.');
            }

            if ($studentMail) {
                return redirect()->back()->with('error', 'Cet email a déjà été utilisé par un autre élève');
            }
            if (!empty($request->password) && $passwordLength < 6) {
                return redirect()->back()->with('error', 'Votre mot de passe ne doit pas être de moins de 6 caractères.');
            }

            if (!preg_match($regex, $request->email)) {
                return redirect()->back()->with('error', 'Cet email est invalide. Assurez-vous qu\'il se termine par .fr, .com, .org, .bj ou .io.');
            }

            $student->name = trim($request->name);
            $student->last_name = trim($request->last_name);
            $student->email = trim($request->email);
            $student->admission_number = trim($request->admission_number);
            $student->roll_number = trim($request->roll_number);
            $student->class_id = intval($request->class_id);
            $student->gender = trim($request->gender);
            if (!empty($request->date_of_birth)) {
                $dateOfBirth = Carbon::parse(trim($request->date_of_birth));
                $minimumAge = 2;
                $age = $dateOfBirth->diffInYears(Carbon::now());
                if ($age < $minimumAge) {
                    return redirect('admin/student/add')->with('error', 'L\'élève doit avoir au moins 2 ans.');
                }
                $student->date_of_birth = $dateOfBirth;
            }
            $student->caste = trim($request->caste);
            $student->religion = trim($request->religion);
            if (!empty($request->mobile_number)) {
                $mobileNumber = trim($request->mobile_number);
                if (!preg_match('/^\d{8,15}$/', $mobileNumber)) {
                    return redirect()->back()->with('error', 'Le numéro de téléphone doit contenir uniquement des chiffres et être compris entre 8 et 15 chiffres.');
                }
                $student->mobile_number = $mobileNumber;
            }
            if (!empty($request->admission_date)) {
                $student->admission_date = trim($request->admission_date);
            }

            //Condition de chargement ou de modification d'une photo de profile
            if (!empty($request->file('profile_picture'))) {
                $studentProfilePicture = $student->profile_picture;

                if (!empty($studentProfilePicture)) {
                    $profilePictureUrl = User::getProfile($studentProfilePicture);

                    if (!empty($profilePictureUrl)) {
                        unlink('upload/profile/' . $studentProfilePicture);
                    }
                }

                $ext = $request->file('profile_picture')->getClientOriginalExtension();
                $file = $request->file('profile_picture');
                $randomStr = date('dmYhis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;

                $file->move('upload/profile/', $fileName);

                $student->profile_picture = $fileName;
            }

            $student->blood_group = trim($request->blood_group);
            $student->height = trim($request->height);
            $student->weight = trim($request->weight);
            $student->status = intval($request->status);
            if (!empty($request->password)) {
                $student->password = Hash::make($request->password);
            }
            $student->save();

            return redirect('admin/student/list')->with('success', 'Cet élève a été modifié avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification d'un élève' : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function delete($id)
    {
        $student = User::getSingle($id);
        if ($student) {
            $student->is_delete = 1;
            $student->save();
            return redirect('admin/student/list')->with('success', 'Cet élève a été supprimé avec succès.');
        } else {
            abort(404);
        }
    }

}
