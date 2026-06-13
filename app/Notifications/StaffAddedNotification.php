<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class StaffAddedNotification extends Notification
{
    public function __construct(
        protected string $role,
        protected string $hireDate
    ) {}

    /**
     * Canaux de diffusion : uniquement la base de données (in-app).
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Données stockées dans la table notifications.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => 'staff',
            'icon'    => 'briefcase',
            'color'   => 'purple',
            'title'   => 'Vous êtes maintenant membre du personnel',
            'message' => "Vous avez été ajouté au personnel en tant que {$this->role}" .
                         ($this->hireDate ? " à partir du {$this->hireDate}." : "."),
            'url'     => null,
        ];
    }
}
