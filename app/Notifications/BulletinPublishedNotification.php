<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class BulletinPublishedNotification extends Notification
{
    public function __construct(
        private string $studentName,
        private string $periodName,
        private int    $bulletinId,
        private string $recipientType = 'student'  // 'student' | 'parent'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $url = $this->recipientType === 'parent'
            ? "/parent/my_student/{$notifiable->id}/bulletins/{$this->bulletinId}"
            : "/student/my_bulletins/{$this->bulletinId}";

        $message = $this->recipientType === 'parent'
            ? "Le bulletin de {$this->studentName} pour la période « {$this->periodName} » est disponible."
            : "Votre bulletin pour la période « {$this->periodName} » est disponible.";

        return [
            'type'    => 'bulletin_published',
            'icon'    => 'academic-cap',
            'color'   => 'blue',
            'title'   => 'Bulletin disponible',
            'message' => $message,
            'url'     => $url,
        ];
    }
}
