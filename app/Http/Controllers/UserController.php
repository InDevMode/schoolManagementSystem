<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordAdminMail;
use App\Models\SettingModel;
use App\Models\User;
use App\Notifications\PasswordResetByAdminNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

    public function updateSuperAdminAccount(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $id = Auth::user()->id;
            $admin = User::getSingle($id);
            $adminMail = User::checkEmailSingle($request->email, $id);
            $regex = '/^[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$/i';

            if (!$admin) {
                return redirect()->back()->with('error', 'Cet utilisateur est introuvable.');
            }

            if ($adminMail) {
                return redirect()->back()->with('error', 'Cet email est déjà utilisé par un autre utilisateur.');
            }

            if (!preg_match($regex, $request->email)) {
                return redirect()->back()->with('error', "L'adresse email est invalide.");
            }

            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $ext = $file->getClientOriginalExtension();
                $randomStr = 'superadmin' . date('dmYHis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;

                if (!empty($admin->profile_picture)) {
                    $oldPath = public_path('upload/profile/' . $admin->profile_picture);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $file->move(public_path('upload/profile/'), $fileName);
                $admin->profile_picture = $fileName;
            }

            $admin->name          = trim($request->name);
            $admin->last_name     = trim($request->last_name);
            $admin->email         = trim($request->email);
            $admin->mobile_number = trim($request->mobile_number ?? '');
            $admin->address       = trim($request->address ?? '');
            $admin->save();

            return redirect()->back()->with('success', 'Vos informations ont été modifiées avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification du profil super admin : " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la modification. Veuillez réessayer.');
        }
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
                    $oldPath = public_path('upload/profile/' . $admin->profile_picture);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                // Déplacer la nouvelle photo
                $file->move(public_path('upload/profile/'), $fileName);
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
                    $oldPath = public_path('upload/profile/' . $teacher->profile_picture);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                // Déplacer la nouvelle photo
                $file->move(public_path('upload/profile/'), $fileName);
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
                    $oldPath = public_path('upload/profile/' . $student->profile_picture);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                // Déplacer la nouvelle photo
                $file->move(public_path('upload/profile/'), $fileName);
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
                    $oldPath = public_path('upload/profile/' . $parent->profile_picture);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                // Déplacer la nouvelle photo
                $file->move(public_path('upload/profile/'), $fileName);
                $parent->profile_picture = $fileName;
            }

            $parent->save();

            return redirect()->back()->with('success', 'Vos informations personnelles ont été modifiés avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification de ces informations personnelles' : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    /**
     * Édition inline d'une cellule (super admin uniquement).
     * Champs autorisés : name, last_name, email, mobile_number, status.
     */
    public function inlineCellEdit(Request $request): \Illuminate\Http\JsonResponse
    {
        // Seul le super admin (user_type = 0) peut utiliser cette fonctionnalité
        if (!Auth::check() || Auth::user()->user_type !== 0) {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        $request->validate([
            'id'    => 'required|integer|exists:users,id',
            'field' => 'required|string|in:name,last_name,email,mobile_number,status,address,occupation,gender',
            'value' => 'present|string|max:255',
        ]);

        try {
            $user = User::getSingle((int) $request->id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
            }

            $field = $request->field;
            $value = trim($request->value);

            // Validation supplémentaire par champ
            if ($field === 'email') {
                $existing = User::where('email', $value)->where('id', '!=', $user->id)->first();
                if ($existing) {
                    return response()->json(['success' => false, 'message' => 'Cet email est déjà utilisé.'], 422);
                }
            }

            if ($field === 'status' && !in_array($value, ['0', '1'])) {
                return response()->json(['success' => false, 'message' => 'Statut invalide.'], 422);
            }

            $user->{$field} = $value;
            $user->save();

            Log::info("Inline edit par super admin #{Auth::id()} : user #{$user->id} champ '{$field}' → '{$value}'");

            return response()->json([
                'success' => true,
                'message' => 'Modification enregistrée.',
                'value'   => $value,
            ]);
        } catch (\Exception $e) {
            Log::error('Inline cell edit error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    public function resetUsersPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|exists:users,id']);

        try {
            $successCount = 0;
            $failCount    = 0;

            foreach ($request->ids as $userId) {
                $user = User::find((int) $userId);
                if (!$user) {
                    $failCount++;
                    continue;
                }

                // Générer un mot de passe aléatoire sécurisé (12 caractères)
                $plainPassword = Str::random(10) . rand(10, 99);

                // Mettre à jour le mot de passe hashé + forcer le changement à la prochaine connexion
                $user->password             = Hash::make($plainPassword);
                $user->must_change_password = 1;
                $user->save();

                // Envoyer le mot de passe en clair par email
                try {
                    Mail::to($user->email)->send(new ResetPasswordAdminMail($user, $plainPassword));
                } catch (\Exception $mailEx) {
                    Log::warning("Reset password mail failed for user #{$user->id}: " . $mailEx->getMessage());
                }

                // Notification in-app
                try {
                    $user->notify(new PasswordResetByAdminNotification());
                } catch (\Exception $notifEx) {
                    Log::warning("Reset password notification failed for user #{$user->id}: " . $notifEx->getMessage());
                }

                $successCount++;
            }

            $isSingle = count($request->ids) === 1;

            if ($isSingle) {
                $message = 'Mot de passe réinitialisé avec succès. '
                    . 'L\'utilisateur recevra son nouveau mot de passe par email et devra le changer à sa prochaine connexion.';
            } else {
                $message = $successCount . ' mot(s) de passe réinitialisé(s) avec succès. '
                    . 'Chaque utilisateur recevra son nouveau mot de passe par email et devra le modifier à sa prochaine connexion.';
            }

            if ($failCount > 0) {
                $message .= " ({$failCount} utilisateur(s) introuvable(s))";
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            Log::error('Reset password error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la réinitialisation.'], 500);
        }
    }

    /**
     * Liste de tous les utilisateurs (super admin uniquement).
     */
    public function allUsersList(): \Inertia\Response
    {
        $perPage = min((int) request('per_page', 5), 100);

        $query = User::select(
                'users.*',
                DB::raw('(SELECT COUNT(*) FROM model_has_permissions WHERE model_has_permissions.model_id = users.id AND model_has_permissions.model_type = \'App\\\\Models\\\\User\') as direct_permissions_count')
            )
            ->where('users.is_delete', 0);

        // Filtres
        $filters = [
            'users.name'         => strtolower(request('name', '')),
            'users.last_name'    => strtolower(request('last_name', '')),
            'users.email'        => strtolower(request('email', '')),
            'users.mobile_number'=> strtolower(request('mobile_number', '')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $query->where($column, 'like', '%' . $value . '%');
            }
        }

        $userType = request('user_type', '');
        if (is_numeric($userType) && $userType !== '') {
            $query->where('users.user_type', (int) $userType);
        }

        $status = request('status', '');
        if (in_array($status, ['0', '1'], true)) {
            $query->where('users.status', $status);
        }

        $users = $query->orderBy('users.id', 'desc')->paginate($perPage);

        // Enrichir avec rôles, permissions count et nom d'école
        $setting = SettingModel::getSingle(1);
        $schoolName = $setting?->school_name ?? config('app.name');

        $users->getCollection()->transform(function ($u) use ($schoolName) {
            try {
                $roles = $u->getRoleNames()->toArray();

                // Le super admin (user_type = 0) possède toutes les permissions par définition.
                // On affiche le total réel de permissions en base plutôt que ce qui est stocké
                // dans role_has_permissions (qui peut être désynchronisé).
                if ((int) $u->user_type === 0) {
                    $permCount = \Spatie\Permission\Models\Permission::where('guard_name', 'web')
                        ->count();
                } else {
                    $permCount = $u->getAllPermissions()
                        ->filter(fn($p) => ($p->is_delete ?? 0) == 0)
                        ->count();
                }
            } catch (\Exception $e) {
                $roles     = [];
                $permCount = 0;
            }

            $roleLabel = match ((int) $u->user_type) {
                0 => 'Super Administrateur',
                1 => 'Administrateur',
                2 => 'Professeur',
                3 => 'Apprenant',
                4 => 'Parent',
                default => $roles[0] ?? 'Rôle custom',
            };

            $u->role_label        = $roleLabel;
            $u->role_names        = $roles;
            $u->permissions_count = $permCount;
            $u->school_name       = $schoolName;
            $u->is_online         = Cache::has('OnlineUser.' . $u->id);
            return $u;
        });

        return Inertia::render('SuperAdmin/Users/Index', [
            'users' => $users,
            'roles' => \Spatie\Permission\Models\Role::where('is_delete', 0)
                ->orderBy('user_type')
                ->get()
                ->map(fn($r) => [
                    'id'        => $r->id,
                    'name'      => $r->name,
                    'user_type' => $r->user_type,
                    'label'     => match ((int) $r->user_type) {
                        0 => 'Super Administrateur',
                        1 => 'Administrateur',
                        2 => 'Professeur',
                        3 => 'Apprenant',
                        4 => 'Parent',
                        default => $r->name,
                    },
                ]),
        ]);
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
                $setting->paypal_email        = trim($request->paypal_email);
                $setting->kkiapay_public_key  = trim($request->kkiapay_public_key);
                $setting->kkiapay_private_key = trim($request->kkiapay_private_key);
                $setting->kkiapay_secret_key  = trim($request->kkiapay_secret_key);
                $setting->stripe_public_key   = trim($request->stripe_public_key);
                $setting->stripe_secret_key   = trim($request->stripe_secret_key);
                $setting->fedapay_public_key  = trim($request->fedapay_public_key);
                $setting->fedapay_secret_key  = trim($request->fedapay_secret_key);
                $setting->school_name         = trim($request->school_name);
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

    /**
     * Mise à jour d'un utilisateur depuis le panneau super admin.
     */
    public function updateUserFromPanel(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        if (!Auth::check() || Auth::user()->user_type !== 0) {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        $request->validate([
            'name'         => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => "required|email|unique:users,email,{$id}",
            'mobile_number'=> 'nullable|string|max:20',
            'status'       => 'required|in:0,1',
            'user_type'    => 'required|integer|min:0|max:99',
        ]);

        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
            }

            $oldUserType = (int) $user->user_type;
            $newUserType = (int) $request->user_type;

            $user->name          = trim($request->name);
            $user->last_name     = trim($request->last_name);
            $user->email         = trim($request->email);
            $user->mobile_number = $request->mobile_number ? trim($request->mobile_number) : null;
            $user->status        = $request->status;
            $user->user_type     = $newUserType;
            $user->save();

            // Synchroniser le rôle Spatie si le user_type a changé
            if ($oldUserType !== $newUserType) {
                $role = \Spatie\Permission\Models\Role::where('user_type', $newUserType)->first();
                if ($role) {
                    $user->syncRoles([$role->name]);
                }
                \Spatie\Permission\PermissionRegistrar::class;
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

                // Notifier l'utilisateur du changement de rôle
                try {
                    $roleLabels = [0=>'Super Administrateur',1=>'Administrateur',2=>'Professeur',3=>'Apprenant',4=>'Parent'];
                    $roleLabel  = $role?->name ?? ($roleLabels[$newUserType] ?? "type {$newUserType}");
                    $user->notify(new \App\Notifications\RoleChangedNotification($roleLabel));
                } catch (\Exception $notifEx) {
                    Log::warning("Role change notification failed for user #{$id}: " . $notifEx->getMessage());
                }
            }

            Log::info("Super admin #{Auth::id()} a mis à jour l'utilisateur #{$id}");

            return response()->json(['success' => true, 'message' => 'Utilisateur mis à jour avec succès.']);
        } catch (\Exception $e) {
            Log::error('updateUserFromPanel error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour.'], 500);
        }
    }

    /**
     * Soft-delete d'un utilisateur (super admin uniquement).
     */
    public function deleteUser(int $id): \Illuminate\Http\JsonResponse
    {
        if (!Auth::check() || Auth::user()->user_type !== 0) {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        if ($id === Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 403);
        }

        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
            }

            $user->update(['is_delete' => 1]);
            Log::info("Super admin #{Auth::id()} a supprimé l'utilisateur #{$id}");

            return response()->json(['success' => true, 'message' => 'Utilisateur supprimé avec succès.']);
        } catch (\Exception $e) {
            Log::error('Delete user error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression.'], 500);
        }
    }

}
