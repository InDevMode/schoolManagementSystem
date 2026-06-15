<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notification envoyée au professeur quand l'admin rejette une ou plusieurs de ses notes.
 */
class GradeRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly int    $evaluationId,
        private readonly string $evalLabel,
        private readonly ?int   $classId,
        private readonly int    $count,
        private readonly string $studentNames,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $plural  = $this->count > 1;
        $message = $plural
            ? "{$this->count} notes ont été rejetées pour « {$this->evalLabel} » : {$this->studentNames}. Veuillez re-saisir ces notes."
            : "La note de {$this->studentNames} a été rejetée pour « {$this->evalLabel} ». Veuillez re-saisir cette note.";

        return [
            'type'    => 'warning',
            'icon'    => 'alert',
            'color'   => 'orange',
            'title'   => $plural ? 'Notes rejetées' : 'Note rejetée',
            'message' => $message,
            'url'     => "/teacher/evaluations/grade-entry?evaluation_id={$this->evaluationId}",
            'meta'    => [
                'evaluation_id' => $this->evaluationId,
                'count'         => $this->count,
            ],
        ];
    }
}
