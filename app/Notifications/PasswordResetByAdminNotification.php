<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PasswordResetByAdminNotification extends Notification
{
    public function __construct() {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'password_reset',
            'icon'    => 'key',
            'color'   => 'amber',
            'title'   => 'Mot de passe réinitialisé',
            'message' => 'Votre mot de passe a été réinitialisé par un administrateur. Vous devez le changer à votre prochaine connexion.',
            'url'     => null,
        ];
    }
}
