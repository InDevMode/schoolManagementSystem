<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class RoleChangedNotification extends Notification
{
    public function __construct(
        private string $newRole
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'role_changed',
            'icon'    => 'shield-check',
            'color'   => 'violet',
            'title'   => 'Rôle modifié',
            'message' => "Votre rôle dans le système a été mis à jour : {$this->newRole}.",
            'url'     => null,
        ];
    }
}
