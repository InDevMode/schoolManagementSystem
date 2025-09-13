<?php

namespace App\Exports;

use App\Models\FeesCollectionModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportFeesCollection implements FromCollection, WithMapping, WithHeadings, WithColumnWidths
{
    public function headings(): array
    {
        return [
            'ID',
            'N° Matricule',
            'Nom et Prénoms',
            'Classe',
            'Montant Total',
            'Montant Payé',
            'Montant Restant',
            'Methode de Paiement',
            'Remarque',
            'Crée par',
            'Date de Création',
            'Date de Mise à Jour',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 30,
            'C' => 30,
            'D' => 30,
            'E' => 30,
            'F' => 30,
            'G' => 30,
            'H' => 30,
            'I' => 60,
            'J' => 30,
            'K' => 30,
            'L' => 30,
        ];
    }

    public function map($row): array
    {
        $student_name = $row->student_name . ' ' . $row->student_last_name;
        $payment_type = '';
        $payment_type = match ($row->payment_type) {
            'cash' => 'Espèce',
            'transfer' => 'Virement',
            'check' => 'Chèque',
            'paypal' => 'Paypal',
            'kkiapay' => 'Kkiapay',
            'stripe' => 'Stripe',
            default => $row->payment_type,
        };
        return [
            $row->id,
            $row->student_admission_number,
            $student_name,
            $row->class_name,
            number_format($row->class_amount, 2) . 'FCFA',
            number_format($row->paid_amount, 2) . 'FCFA',
            number_format($row->remaning_amount, 2) . 'FCFA',
            $payment_type,
            $row->remark,
            $row->created_by_name,
            $row->created_at->format('d-m-Y H:i:s'),
            $row->updated_at->format('d-m-Y H:i:s'),
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return FeesCollectionModel::getAllFeesCollections();
    }
}
