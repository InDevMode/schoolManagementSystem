<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NoticeBoardNotification extends Notification
{
    public function __construct(
        protected string $title,
        protected string $publishedBy
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
            'type'    => 'notice',
            'icon'    => 'bell',
            'color'   => 'green',
            'title'   => 'Nouvelle publication',
            'message' => "Une nouvelle publication « {$this->title} » a été postée par {$this->publishedBy}. Consultez le tableau d'affichage pour en savoir plus.",
            'url'     => null,
        ];
    }
}
