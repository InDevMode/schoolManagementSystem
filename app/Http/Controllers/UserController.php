<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordAdminMail;
use App\Models\SettingModel;
use App\Models\User;
use App\Notifications\PasswordResetByAdminNotification;
use App\Exports\ExportAllUsers;
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
use Maatwebsite\Excel\Facades\Excel;

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
                UploadService::delete($admin->profile_picture);
                $admin->profile_picture = UploadService::upload($request->file('profile_picture'), UploadService::profileFolder(), 'superadmin');
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
                UploadService::delete($admin->profile_picture);
                $admin->profile_picture = UploadService::upload($request->file('profile_picture'), UploadService::profileFolder(), 'admin');
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
                UploadService::delete($teacher->profile_picture);
                $teacher->profile_picture = UploadService::upload($request->file('profile_picture'), UploadService::profileFolder(), 'teacher');
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
                return redirect()->back()->with('error', 'Cet apprenant est introuvable.');
            }

            if ($studentMail) {
                return redirect()->back()->with('error', 'Cet email a déjà été utilisé par un autre apprenant');
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
                    return redirect()->back()->with('error', 'L\'apprenant doit avoir au moins 2 ans.');
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
                UploadService::delete($student->profile_picture);
                $student->profile_picture = UploadService::upload($request->file('profile_picture'), UploadService::profileFolder(), 'student');
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
                UploadService::delete($parent->profile_picture);
                $parent->profile_picture = UploadService::upload($request->file('profile_picture'), UploadService::profileFolder(), 'parent');
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
            $user = User::getSingle($request->id);
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

        $currentUser  = Auth::user();
        $isSuperAdmin = $currentUser->user_type === 0;

        // Vérifier la permission
        if (! $isSuperAdmin && ! $currentUser->can('action.users.reset_password')) {
            return response()->json(['success' => false, 'message' => 'Permission refusée.'], 403);
        }

        try {
            $successCount = 0;
            $failCount    = 0;

            foreach ($request->ids as $userId) {
                $user = User::find($userId);
                if (!$user) {
                    $failCount++;
                    continue;
                }

                // Scoping multi-tenant : un admin ne peut réinitialiser que les users de son école
                if (! $isSuperAdmin && $user->school_id !== $currentUser->school_id) {
                    $failCount++;
                    continue;
                }

                // Pas de réinitialisation du super admin
                if ($user->user_type === 0) {
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
     * Liste des utilisateurs — avec scoping multi-tenant.
     *
     * Super admin (user_type=0) : voit TOUS les utilisateurs de toutes les écoles.
     * Admin / rôle custom       : voit UNIQUEMENT les utilisateurs de son école (school_id).
     */
    public function allUsersList(): \Inertia\Response
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->user_type === 0;
        $perPage = min((int) request('per_page', 5), 100);

        $query = User::select('users.*')
            ->where('users.is_delete', 0);

        // ── Scoping multi-tenant ──────────────────────────────────────────
        // Un admin ne voit que les utilisateurs de son école.
        // Le super admin voit tout (pas de contrainte school_id).
        if (! $isSuperAdmin) {
            $query->where('users.school_id', $currentUser->school_id);
            // Un admin ne peut pas se voir lui-même dans la liste (optionnel — on le garde)
            // et ne voit jamais le super admin
            $query->where('users.user_type', '!=', 0);
        }

        // ── Filtres ───────────────────────────────────────────────────────
        $filters = [
            'users.name'          => strtolower(request('name', '')),
            'users.last_name'     => strtolower(request('last_name', '')),
            'users.email'         => strtolower(request('email', '')),
            'users.mobile_number' => strtolower(request('mobile_number', '')),
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

        // ── Enrichissement ────────────────────────────────────────────────
        // Précharger toutes les écoles concernées pour éviter N+1
        $schoolIds = $users->getCollection()->pluck('school_id')->filter()->unique()->values();
        $schools   = \App\Models\School::whereIn('id', $schoolIds)->get()->keyBy('id');

        $users->getCollection()->transform(function ($u) use ($schools) {
            try {
                $roles = $u->getRoleNames()->toArray();

                if ((int) $u->user_type === 0) {
                    $permCount = \Spatie\Permission\Models\Permission::where('guard_name', 'web')->count();
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

            // Nom de l'école depuis la table schools (multi-tenant)
            $school = $u->school_id ? ($schools[$u->school_id] ?? null) : null;

            $u->role_label        = $roleLabel;
            $u->role_names        = $roles;
            $u->permissions_count = $permCount;
            $u->school_name       = $school?->school_name ?? '—';
            $u->is_online         = Cache::has('OnlineUser.' . $u->id);
            return $u;
        });

        // ── Permissions granulaires passées au frontend ───────────────────
        return Inertia::render('SuperAdmin/Users/Index', [
            'users'       => $users,
            'isSuperAdmin' => $isSuperAdmin,
            'canView'     => true, // déjà filtré par check_perm:view.users.all
            'canEdit'     => $isSuperAdmin || $currentUser->can('action.users.edit'),
            'canReset'    => $isSuperAdmin || $currentUser->can('action.users.reset_password'),
            'canDelete'   => $isSuperAdmin || $currentUser->can('action.users.delete'),
            'canExport'   => $isSuperAdmin || $currentUser->can('action.users.export'),
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

    /**
     * Export des utilisateurs — avec scoping multi-tenant.
     */
    public function exportAllUsers(Request $request)
    {
        $currentUser  = Auth::user();
        $isSuperAdmin = $currentUser->user_type === 0;

        $query = User::select('users.*')
            ->where('users.is_delete', 0);

        // Scoping multi-tenant
        if (! $isSuperAdmin) {
            $query->where('users.school_id', $currentUser->school_id)
                  ->where('users.user_type', '!=', 0);
        }

        $userType = $request->get('user_type', '');
        if (is_numeric($userType) && $userType !== '') {
            $query->where('users.user_type', (int) $userType);
        }

        $status = $request->get('status', '');
        if (in_array($status, ['0', '1'], true)) {
            $query->where('users.status', $status);
        }

        // Précharger les écoles
        $users = $query->orderBy('users.id', 'desc')->get();
        $schoolIds = $users->pluck('school_id')->filter()->unique();
        $schools   = \App\Models\School::whereIn('id', $schoolIds)->get()->keyBy('id');

        $users = $users->map(function ($u) use ($schools) {
            $school = $u->school_id ? ($schools[$u->school_id] ?? null) : null;
            $u->school_name = $school?->school_name ?? '—';
            $u->is_online   = Cache::has('OnlineUser.' . $u->id);
            return $u;
        });

        return Excel::download(
            new ExportAllUsers($users),
            'utilisateurs_' . date('d_m_Y') . '.xlsx'
        );
    }    public function updatePassword(Request $request): \Illuminate\Http\RedirectResponse
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

    /**
     * Paramètres — super admin uniquement (config globale settings id=1).
     * Les admins d'école utilisent SchoolController::settings() via /admin/settings.
     */
    public function settings()
    {
        // Seul le super admin accède à cette route (superadmin.settings)
        // Les admins d'école utilisent SchoolController::settings() via /admin/settings
        $setting = SettingModel::getSingle(1);
        return Inertia::render('Admin/Settings/Index', [
            'setting'    => $setting,
            'faviconUrl' => $setting?->getFavicon() ?? asset('upload/favicon.png'),
            'logoUrl'    => $setting?->getLogo()    ?? asset('upload/logo.png'),
            'isSchool'   => false,
        ]);
    }

    public function updateSettingInfo(Request $request)
    {

        try {

            $setting = SettingModel::getSingle(1);
            if ($setting) {
                $setting->paypal_email        = trim($request->paypal_email ?? '');
                $setting->paypal_client_id    = trim($request->paypal_client_id ?? '');
                $setting->paypal_secret       = trim($request->paypal_secret ?? '');
                $setting->paypal_mode         = in_array($request->paypal_mode, ['sandbox', 'live']) ? $request->paypal_mode : 'sandbox';
                $setting->kkiapay_public_key  = trim($request->kkiapay_public_key ?? '');
                $setting->kkiapay_private_key = trim($request->kkiapay_private_key ?? '');
                $setting->kkiapay_secret_key  = trim($request->kkiapay_secret_key ?? '');
                $setting->stripe_public_key   = trim($request->stripe_public_key ?? '');
                $setting->stripe_secret_key   = trim($request->stripe_secret_key ?? '');
                $setting->fedapay_public_key  = trim($request->fedapay_public_key ?? '');
                $setting->fedapay_secret_key  = trim($request->fedapay_secret_key ?? '');
                $setting->school_name         = trim($request->school_name ?? '');
                $setting->school_type         = trim($request->school_type ?? '');
                $setting->address             = trim($request->address ?? '');
                $setting->phone               = trim($request->phone ?? '');
                $setting->email               = trim($request->email ?? '');
                $setting->uai_number          = trim($request->uai_number ?? '');
                $setting->status              = trim($request->status ?? '1');

                // ── Background de la page d'authentification ───────────────
                if ($request->filled('auth_bg_type')) {
                    $allowedTypes = ['gradient', 'image', 'video', 'particles'];
                    $bgType = trim($request->auth_bg_type);
                    if (in_array($bgType, $allowedTypes)) {
                        $setting->auth_bg_type = $bgType;
                    }
                }

                // Upload image/vidéo de fond (Supabase Storage)
                if ($request->hasFile('auth_bg_image')) {
                    UploadService::delete(basename($setting->auth_bg_value ?? ''));
                    $path = UploadService::upload($request->file('auth_bg_image'), UploadService::settingFolder(), 'auth_bg');
                    $setting->auth_bg_value = UploadService::url($path);
                    $setting->auth_bg_type  = 'image';
                }
                elseif ($request->hasFile('auth_bg_video')) {
                    UploadService::delete(basename($setting->auth_bg_value ?? ''));
                    $path = UploadService::upload($request->file('auth_bg_video'), UploadService::settingFolder(), 'auth_bg_vid');
                    $setting->auth_bg_value = UploadService::url($path);
                    $setting->auth_bg_type  = 'video';
                }
                elseif ($request->has('auth_bg_value') && $request->filled('auth_bg_value')) {
                    $setting->auth_bg_value = trim($request->auth_bg_value);
                }

                if ($request->has('auth_bg_label')) {
                    $setting->auth_bg_label = trim($request->auth_bg_label ?? '');
                }
                if ($request->has('auth_bg_overlay')) {
                    $setting->auth_bg_overlay = trim($request->auth_bg_overlay ?? 'rgba(0,0,0,0.35)');
                }

                if ($request->hasFile('favicon')) {
                    UploadService::delete($setting->favicon);
                    $setting->favicon = UploadService::upload($request->file('favicon'), UploadService::settingFolder(), 'favicon');
                }

                if ($request->hasFile('logo')) {
                    UploadService::delete($setting->logo);
                    $setting->logo = UploadService::upload($request->file('logo'), UploadService::settingFolder(), 'logo');
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
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Non authentifié.'], 401);
        }

        $authUser = Auth::user();
        // Super admin ou utilisateur avec la permission action.users.edit
        if ($authUser->user_type !== 0 && !$authUser->can('action.users.edit')) {
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

            // Scoping multi-tenant : un admin ne peut modifier qu'un utilisateur de son école
            if ($authUser->user_type !== 0 && $user->school_id !== $authUser->school_id) {
                return response()->json(['success' => false, 'message' => 'Accès refusé — utilisateur hors de votre école.'], 403);
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
     * Soft-delete d'un utilisateur (super admin ou utilisateur avec permission action.users.delete).
     */
    public function deleteUser(int $id): \Illuminate\Http\JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Non authentifié.'], 401);
        }

        $authUser = Auth::user();
        if ($authUser->user_type !== 0 && !$authUser->can('action.users.delete')) {
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

            // Scoping multi-tenant : un admin ne peut supprimer qu'un utilisateur de son école
            if ($authUser->user_type !== 0 && $user->school_id !== $authUser->school_id) {
                return response()->json(['success' => false, 'message' => 'Accès refusé — utilisateur hors de votre école.'], 403);
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
