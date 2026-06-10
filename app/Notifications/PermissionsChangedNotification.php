<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PermissionsChangedNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'permissions_changed',
            'icon'    => 'key',
            'color'   => 'indigo',
            'title'   => 'Permissions mises à jour',
            'message' => 'Vos permissions d\'accès dans le système ont été modifiées par un administrateur.',
            'url'     => null,
        ];
    }
}
