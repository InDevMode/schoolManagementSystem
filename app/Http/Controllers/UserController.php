<?php

namespace App\Http\Controllers;

use App\Models\SettingModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class UserController extends Controller
{

    public function myAccount()
    {
        $user = User::getSingle(Auth::user()->id);
        abort_unless($user, 404);
        return Inertia::render('Profile/Account', [
            'userData'          => $user,
            'profilePictureUrl' => $this->getProfilePictureUrl($user),
        ]);
    }

    private function getProfilePictureUrl($userData): string
    {
        return !empty($userData->profile_picture)
            ? $userData->getProfile()
            : asset('upload/default.jpg');
    }

    public function updateAdminAccount(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $id = Auth::user()->id;
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

            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $ext = $file->getClientOriginalExtension();
                $randomStr = 'admin' . date('dmYHis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;

                // Supprimer l'ancienne photo si elle existe
                if (!empty($admin->profile_picture)) {
                    $oldPath = base_path('upload/profile/' . $admin->profile_picture);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                // Déplacer la nouvelle photo
                $file->move(base_path('upload/profile/'), $fileName);
                $admin->profile_picture = $fileName;
            }

            $admin->name = trim($request->name);
            $admin->last_name = trim($request->last_name);
            $admin->email = trim($request->email);
            $admin->save();

            return redirect()->back()->with('success', 'Les informations de cet administrateur ont été modifiées avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification des informations de cet utilisateur : " . $e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de la modification des informations utilisateur. Veuillez réessayer.');
        }
    }

    public function updateTeacherAccount(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $id = Auth::user()->id;
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

            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $ext = $file->getClientOriginalExtension();
                $randomStr = 'teacher' . date('dmYHis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;

                // Supprimer l'ancienne photo si elle existe
                if (!empty($teacher->profile_picture)) {
                    $oldPath = base_path('upload/profile/' . $teacher->profile_picture);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                // Déplacer la nouvelle photo
                $file->move(base_path('upload/profile/'), $fileName);
                $teacher->profile_picture = $fileName;
            }

            $teacher->save();

            return redirect()->back()->with('success', 'Vos informations personnelles ont été modifiées avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification de ces informations personnelles' : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function updateStudentAccount(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $id = Auth::user()->id;
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

            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $ext = $file->getClientOriginalExtension();
                $randomStr = 'student' . date('dmYHis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;

                // Supprimer l'ancienne photo si elle existe
                if (!empty($student->profile_picture)) {
                    $oldPath = base_path('upload/profile/' . $student->profile_picture);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                // Déplacer la nouvelle photo
                $file->move(base_path('upload/profile/'), $fileName);
                $student->profile_picture = $fileName;
            }

            $student->blood_group = trim($request->blood_group);
            $student->height = trim($request->height);
            $student->weight = trim($request->weight);
            $student->save();

            return redirect()->back()->with('success', 'Vos informations personnelles ont été modifiés avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification de ces informations personnelles' : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function updateParentAccount(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {

        try {
            $id = Auth::user()->id;
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

            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $ext = $file->getClientOriginalExtension();
                $randomStr = 'parent' . date('dmYHis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;

                // Supprimer l'ancienne photo si elle existe
                if (!empty($parent->profile_picture)) {
                    $oldPath = base_path('upload/profile/' . $parent->profile_picture);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                // Déplacer la nouvelle photo
                $file->move(base_path('upload/profile/'), $fileName);
                $parent->profile_picture = $fileName;
            }

            $parent->save();

            return redirect()->back()->with('success', 'Vos informations personnelles ont été modifiés avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification de ces informations personnelles' : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function changePassword()
    {
        return Inertia::render('Profile/ChangePassword');
    }

    public function updatePassword(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $user = User::getSingle(Auth::user()->id);

            if (Hash::check($request->old_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->save();
                return redirect()->back()->with('success', 'Votre mot de passe a été modifié avec succès.');
            } else {
                return redirect()->back()->with('error', "Votre ancien mot de passe n'est pas correct.");
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification du mot de passe : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function settings()
    {
        $setting = SettingModel::getSingle(1);
        return Inertia::render('Admin/Settings/Index', [
            'setting'    => $setting,
            'faviconUrl' => $setting?->getFavicon() ?? asset('upload/favicon.png'),
            'logoUrl'    => $setting?->getLogo()    ?? asset('upload/logo.png'),
        ]);
    }

    public function updateSettingInfo(Request $request)
    {

        try {

            $setting = SettingModel::getSingle(1);
            if ($setting) {
                $setting->paypal_email = trim($request->paypal_email);
                $setting->kkiapay_public_key = trim($request->kkiapay_public_key);
                $setting->kkiapay_private_key = trim($request->kkiapay_private_key);
                $setting->kkiapay_secret_key = trim($request->kkiapay_secret_key);
                $setting->stripe_public_key = trim($request->stripe_public_key);
                $setting->stripe_secret_key = trim($request->stripe_secret_key);
                $setting->school_name = trim($request->school_name);
                $setting->school_type = trim($request->school_type);
                $setting->address = trim($request->address);
                $setting->phone = trim($request->phone);
                $setting->email = trim($request->email);
                $setting->uai_number = trim($request->uai_number);
                $setting->status = trim($request->status);

                if ($request->hasFile('favicon')) {
                    $file = $request->file('favicon');
                    $ext = $file->getClientOriginalExtension();
                    $randomStr = 'favicon' . date('dmYHis') . Str::random(20);
                    $fileName = strtolower($randomStr) . '.' . $ext;

                    // Supprimer l'ancienne photo si elle existe
                    if (!empty($setting->favicon)) {
                        $oldPath = base_path('upload/setting/' . $setting->favicon);
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }

                    // Déplacer la nouvelle photo
                    $file->move(base_path('upload/setting/'), $fileName);
                    $setting->favicon = $fileName;
                }

                if ($request->hasFile('logo')) {
                    $file = $request->file('logo');
                    $ext = $file->getClientOriginalExtension();
                    $randomStr = 'logo' . date('dmYHis') . Str::random(20);
                    $fileName = strtolower($randomStr) . '.' . $ext;

                    // Supprimer l'ancienne photo si elle existe
                    if (!empty($setting->logo)) {
                        $oldPath = base_path('upload/setting/' . $setting->logo);
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }

                    // Déplacer la nouvelle photo
                    $file->move(base_path('upload/setting/'), $fileName);
                    $setting->logo = $fileName;
                }

                $setting->save();
                return redirect()->back()->with('success', 'Vos informations ont été modifiés avec succès.');
            } else {
                return redirect()->back()->with('error', 'Cet utilisateur n\'existe pas.');
            }

        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification des informations de cet utilisateur : " . $e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de la modification des informations utilisateur. Veuillez réessayer.');
        }

    }

}
