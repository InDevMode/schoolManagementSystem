<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class MailSentNotification extends Notification
{
    public function __construct(
        protected string $subject,
        protected string $senderName
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
            'type'    => 'mail',
            'icon'    => 'mail',
            'color'   => 'blue',
            'title'   => 'Nouveau mail reçu',
            'message' => "Vous avez reçu un mail intitulé « {$this->subject} » de la part de {$this->senderName}. Consultez votre boîte Gmail pour lire le message.",
            'url'     => null,
        ];
    }
}
