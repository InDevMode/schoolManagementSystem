<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class TimetableCreatedNotification extends Notification
{
    public function __construct(
        private string $className,
        private string $subjectName
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $userType = $notifiable->user_type ?? 3;
        $url = match ($userType) {
            3 => '/student/my_timetable',
            2 => '/teacher/class_subject',
            4 => '/parent/my_student',
            default => null,
        };

        return [
            'type'    => 'timetable_created',
            'icon'    => 'calendar',
            'color'   => 'teal',
            'title'   => 'Emploi du temps mis à jour',
            'message' => "L'emploi du temps de la classe {$this->className} a été mis à jour pour la matière « {$this->subjectName} ».",
            'url'     => $url,
        ];
    }
}
