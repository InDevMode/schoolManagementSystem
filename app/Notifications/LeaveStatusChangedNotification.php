<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class LeaveStatusChangedNotification extends Notification
{
    public function __construct(
        private string $status,      // 'approved' | 'rejected'
        private string $startDate,
        private string $leaveType,
        private string $adminNote = ''
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $approved = $this->status === 'approved';

        $message = $approved
            ? "Votre demande de congé ({$this->leaveType}) du {$this->startDate} a été approuvée."
            : "Votre demande de congé ({$this->leaveType}) du {$this->startDate} a été refusée.";

        if (!$approved && !empty($this->adminNote)) {
            $message .= " Motif : {$this->adminNote}";
        }

        return [
            'type'    => 'leave_status',
            'icon'    => $approved ? 'check-circle' : 'x-circle',
            'color'   => $approved ? 'green' : 'red',
            'title'   => $approved ? 'Congé approuvé' : 'Congé refusé',
            'message' => $message,
            'url'     => null,
        ];
    }
}
