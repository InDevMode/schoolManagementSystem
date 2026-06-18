<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewHomeworkNotification extends Notification
{
    public function __construct(
        private string $subjectName,
        private string $className,
        private string $submissionDate
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $userType = $notifiable->user_type ?? 3;
        $url = match ($userType) {
            3 => '/student/my_homework',
            4 => '/parent/my_student',
            default => null,
        };

        return [
            'type'    => 'new_homework',
            'icon'    => 'book-open',
            'color'   => 'purple',
            'title'   => 'Nouveau travail à rendre',
            'message' => "Un nouveau devoir de {$this->subjectName} a été assigné à la classe {$this->className}. À rendre pour le {$this->submissionDate}.",
            'url'     => $url,
        ];
    }
}
