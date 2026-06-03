<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
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
        if ($user) {
            try {
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
            ];
        }

        // Partager les settings de l'école (pour le header Vue)
        try {
            $setting = \App\Models\SettingModel::getSingle(1);
            $settings = $setting ? [
                'school_name'       => $setting->school_name,
                'logo_url'          => $setting->getLogo(),
                'kkiapay_public_key'=> $setting->kkiapay_public_key ?? '',
                'stripe_public_key' => $setting->stripe_public_key  ?? '',
                'fedapay_public_key'=> $setting->fedapay_public_key ?? '',
            ] : null;
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
