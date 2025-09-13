<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ExportParent implements FromCollection, WithMapping, WithHeadings, WithColumnWidths
{
    public function map($row): array
    {
        $parent_name = $row->name . ' ' . $row->last_name;
        $parent_status = [
            1 => 'Actif',
            0 => 'Inactif',
        ];
        $parent_gender = [
            'male' => 'Homme',
            'female' => 'Femme',
        ];

        return [
            $row->id,
            $parent_name,
            $row->email,
            $row->mobile_number,
            $parent_gender[$row->gender],
            $row->address,
            $row->occupation,
            $parent_status[$row->status],
            $row->created_at->format('d-m-Y H:i:s'),
            $row->updated_at->format('d-m-Y H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nom et Prénoms',
            'Email',
            'Téléphone',
            'Genre',
            'Adresse',
            'Occupation',
            'Statut',
            'Date de création',
            'Date de modification',
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
            'I' => 30,
            'J' => 30,
        ];
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::getAllParentList();
    }
}
