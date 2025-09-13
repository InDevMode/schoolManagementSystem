<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ExportAdmin implements FromCollection, WithMapping, WithHeadings, WithColumnWidths
{
    public function map($row): array
    {
        $admin_name = $row->name . ' ' . $row->last_name;
        $admin_status = [
            1 => 'Actif',
            0 => 'Inactif',
        ];

        return [
            $row->id,
            $admin_name,
            $row->email,
            $admin_status[$row->status],
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
            'Statut',
            'Date de création',
            'Date de modification',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 40,
            'C' => 40,
            'D' => 40,
            'E' => 40,
            'F' => 40,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::getAllAdminList();
    }
}
