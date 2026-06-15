<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notification envoyée au professeur quand un administrateur annule son évaluation.
 */
class EvaluationCancelledByAdminNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly int    $evaluationId,
        private readonly string $evalLabel,
        private readonly ?int   $classId,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => 'danger',
            'icon'    => 'x-circle',
            'color'   => 'red',
            'title'   => 'Évaluation annulée',
            'message' => "Votre évaluation « {$this->evalLabel} » a été annulée par l'administration. Elle ne sera pas prise en compte dans le calcul des moyennes.",
            'url'     => '/teacher/evaluations',
            'meta'    => [
                'evaluation_id' => $this->evaluationId,
            ],
        ];
    }
}
