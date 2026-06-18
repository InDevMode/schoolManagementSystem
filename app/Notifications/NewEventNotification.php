<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewEventNotification extends Notification
{
    public function __construct(
        private string $eventTitle,
        private string $eventDate,
        private string $eventType
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $typeLabels = [
            'cultural'       => 'Culturel',
            'academic'       => 'Académique',
            'administrative' => 'Administratif',
            'exam'           => 'Examen',
            'ceremony'       => 'Cérémonie',
            'trip'           => 'Sortie',
        ];

        $typeLabel = $typeLabels[$this->eventType] ?? ucfirst($this->eventType);

        return [
            'type'    => 'new_event',
            'icon'    => 'star',
            'color'   => 'orange',
            'title'   => "Nouvel événement : {$typeLabel}",
            'message' => "L'événement « {$this->eventTitle} » est prévu le {$this->eventDate}.",
            'url'     => null,
        ];
    }
}
