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
                'profile_url'     => $user->getProfile(),
                'status'          => $user->status,
                'roles'           => $roles,
                'role_label'      => $roleLabel,
                'permissions'     => $permissions,
                'perm_refreshed'  => $permissionsRefreshed,
            ];
        }

        // Partager les settings de l'école (pour le header Vue)
        // Pour un admin/prof/apprenant/parent : on lit l'école à laquelle il appartient.
        // Pour le super admin (user_type = 0) : on lit les settings globaux (SettingModel id=1).
        // Le auth_background est toujours lu depuis les settings globaux (id=1),
        // car il s'applique à la page de connexion accessible à tous.
        try {
            $settings = null;

            // Settings globaux — cachés 10 min (changent rarement)
            $globalSetting = Cache::remember('settings.global.1', 600, fn () => \App\Models\SettingModel::getSingle(1));
            $authBackground = $globalSetting
                ? $globalSetting->getAuthBackground()
                : [
                    'type'    => 'gradient',
                    'value'   => 'linear-gradient(145deg, #5b21b6 0%, #7c3aed 50%, #6d28d9 100%)',
                    'label'   => null,
                    'overlay' => 'rgba(0,0,0,0.35)',
                ];

            if ($user) {
                if ((int) $user->user_type === 0) {
                    // Super admin → settings globaux
                    if ($globalSetting) {
                        $settings = [
                            'school_name'        => $globalSetting->school_name,
                            'logo_url'           => $globalSetting->getLogo(),
                            'kkiapay_public_key' => $globalSetting->kkiapay_public_key ?? '',
                            'stripe_public_key'  => $globalSetting->stripe_public_key  ?? '',
                            'fedapay_public_key' => $globalSetting->fedapay_public_key ?? '',
                            'paypal_email'       => $globalSetting->paypal_email       ?? '',
                            'paypal_client_id'   => $globalSetting->paypal_client_id   ?? '',
                            'auth_background'    => $authBackground,
                        ];
                    }
                } else {
                    // Admin / autres → école de l'utilisateur (réutilise le cache de EnsureSchoolActive)
                    $school = Cache::remember("school.active.{$user->school_id}", 300, fn () => \App\Models\School::find($user->school_id));
                    if ($school) {
                        // Si l'école n'a pas de logo propre, on utilise le logo global (settings id=1)
                        $logoUrl = $school->getLogoUrl();
                        $defaultUrl = url('upload/logo.png');
                        if ($logoUrl === $defaultUrl) {
                            $logoUrl = $globalSetting ? $globalSetting->getLogo() : $defaultUrl;
                        }

                        $settings = [
                            'school_name'        => $school->school_name,
                            'logo_url'           => $logoUrl,
                            'kkiapay_public_key' => $school->kkiapay_public_key ?? '',
                            'stripe_public_key'  => $school->stripe_public_key  ?? '',
                            'fedapay_public_key' => $school->fedapay_public_key ?? '',
                            'paypal_email'       => $school->paypal_email       ?? '',
                            'paypal_client_id'   => $school->paypal_client_id   ?? '',
                            'auth_background'    => $authBackground,
                        ];
                    }
                }
            } else {
                // Utilisateur non connecté — partage uniquement les infos publiques
                $settings = [
                    'school_name'     => $globalSetting->school_name ?? 'School Management System',
                    'logo_url'        => $globalSetting ? $globalSetting->getLogo() : url('upload/logo.png'),
                    'auth_background' => $authBackground,
                ];
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
