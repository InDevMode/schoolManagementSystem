<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        $userData = null;
        $permissionsRefreshed = false;
        if ($user) {
            try {
                // Si les permissions de cet utilisateur ont été modifiées par le super admin,
                // on force Spatie à oublier son cache en mémoire pour ce cycle de requête
                if (Cache::pull("perm_refreshed_{$user->id}")) {
                    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                    // Recharger les relations de permissions depuis la DB
                    $user->unsetRelation('permissions');
                    $user->unsetRelation('roles');
                    $permissionsRefreshed = true;
                }

                $roles       = $user->getRoleNames()->toArray();
                // getAllPermissions retourne rôle + directes, on filtre is_delete=0
                $permissions = $user->getAllPermissions()
                    ->filter(fn($p) => ($p->is_delete ?? 0) == 0)
                    ->pluck('name')
                    ->toArray();
            } catch (\Exception $e) {
                $roles       = [];
                $permissions = [];
            }

            // Libellé du rôle pour la sidebar — cherche dans les rôles Spatie
            $roleLabel = $roles[0] ?? null;
            if (!$roleLabel) {
                $roleLabel = match ((int) $user->user_type) {
                    0 => 'super_admin',
                    1 => 'admin',
                    2 => 'teacher',
                    3 => 'student',
                    4 => 'parent',
                    default => 'Utilisateur',
                };
            }

            $userData = [
                'id'              => $user->id,
                'name'            => $user->name,
                'last_name'       => $user->last_name,
                'email'           => $user->email,
                'user_type'       => $user->user_type,
                'profile_picture' => $user->profile_picture,
                'status'          => $user->status,
                'roles'           => $roles,
                'role_label'      => $roleLabel,
                'permissions'     => $permissions,
                'perm_refreshed'  => $permissionsRefreshed,
            ];
        }

        // Partager les settings de l'école (pour le header Vue)
        // Pour un admin/prof/élève/parent : on lit l'école à laquelle il appartient.
        // Pour le super admin (user_type = 0) : on lit les settings globaux (SettingModel id=1).
        try {
            $settings = null;

            if ($user) {
                if ((int) $user->user_type === 0) {
                    // Super admin → settings globaux
                    $setting = \App\Models\SettingModel::getSingle(1);
                    if ($setting) {
                        $settings = [
                            'school_name'        => $setting->school_name,
                            'logo_url'           => $setting->getLogo(),
                            'kkiapay_public_key' => $setting->kkiapay_public_key ?? '',
                            'stripe_public_key'  => $setting->stripe_public_key  ?? '',
                            'fedapay_public_key' => $setting->fedapay_public_key ?? '',
                        ];
                    }
                } else {
                    // Admin / autres → école de l'utilisateur
                    $school = \App\Models\School::find($user->school_id);
                    if ($school) {
                        // Si l'école n'a pas de logo propre, on utilise le logo global (settings id=1)
                        $logoUrl = $school->getLogoUrl();
                        $defaultUrl = url('upload/logo.png');
                        if ($logoUrl === $defaultUrl) {
                            $globalSetting = \App\Models\SettingModel::getSingle(1);
                            $logoUrl = $globalSetting ? $globalSetting->getLogo() : $defaultUrl;
                        }

                        $settings = [
                            'school_name'        => $school->school_name,
                            'logo_url'           => $logoUrl,
                            'kkiapay_public_key' => $school->kkiapay_public_key ?? '',
                            'stripe_public_key'  => $school->stripe_public_key  ?? '',
                            'fedapay_public_key' => $school->fedapay_public_key ?? '',
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            $settings = null;
        }

        return [
            ...parent::share($request),
            'auth'     => ['user' => $userData],
            'settings' => $settings,
            'flash'    => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
            ],
            'notifications' => function () use ($request) {
                if (!$request->user()) return [];
                try {
                    $userType = $request->user()->user_type;
                    // super_admin (0), admin (1), et rôles custom (>=5) voient le noticeboard admin
                    if ($userType == 0 || $userType == 1 || $userType >= 5) {
                        return \App\Models\CommunicateModel::getNoticeBoard(5);
                    }
                    return \App\Models\CommunicateModel::getCommunicateWithUserType($userType);
                } catch (\Exception $e) {
                    return [];
                }
            },
            'unreadMessages' => function () use ($request) {
                if (!$request->user()) return [];
                try {
                    return \App\Models\ChatModel::getUnreadMessages($request->user()->id);
                } catch (\Exception $e) {
                    return [];
                }
            },
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
