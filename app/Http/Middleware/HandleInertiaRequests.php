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
                $permissions = $user->getAllPermissions()->pluck('name')->toArray();
            } catch (\Exception $e) {
                $roles       = [];
                $permissions = [];
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
                'permissions'     => $permissions,
            ];
        }

        // Partager les settings de l'école (pour le header Vue)
        try {
            $setting = \App\Models\SettingModel::getSingle(1);
            $settings = $setting ? [
                'school_name' => $setting->school_name,
                'logo_url'    => $setting->getLogo(),
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
                    if ($userType == 1) {
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
