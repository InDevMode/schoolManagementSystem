<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Notification envoyée à l'apprenant (et son parent) quand un paiement/contribution est enregistré.
 */
class FeesPaymentNotification extends Notification
{
    public function __construct(
        private readonly string $studentName,
        private readonly int    $paidAmount,
        private readonly int    $remainingAmount,
        private readonly string $paymentType,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $userType = $notifiable->user_type ?? 3;
        $url = match ($userType) {
            3 => '/student/my_fees',
            4 => '/parent/my_student',
            default => null,
        };

        $typeLabels = [
            'cash'     => 'espèces',
            'check'    => 'chèque',
            'transfer' => 'virement',
            'virement' => 'virement',
            'kkiapay'  => 'KKiaPay',
            'paypal'   => 'PayPal',
            'stripe'   => 'Stripe',
            'fedapay'  => 'FedaPay',
        ];

        $typeLabel = $typeLabels[$this->paymentType] ?? $this->paymentType;

        $message = $this->remainingAmount <= 0
            ? "Contribution de {$this->paidAmount} XOF enregistrée pour {$this->studentName} via {$typeLabel}. La scolarité est entièrement réglée."
            : "Contribution de {$this->paidAmount} XOF enregistrée pour {$this->studentName} via {$typeLabel}. Reste à payer : {$this->remainingAmount} XOF.";

        return [
            'type'    => 'success',
            'icon'    => 'check-circle',
            'color'   => 'green',
            'title'   => 'Contribution validée',
            'message' => $message,
            'url'     => $url,
        ];
    }
}
