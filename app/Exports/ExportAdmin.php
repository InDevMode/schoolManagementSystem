<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportAdmin implements FromCollection, WithMapping, WithHeadings, WithColumnWidths, WithStyles
{
    public function map($row): array
    {
        $admin_name   = $row->name . ' ' . $row->last_name;
        $admin_status = [1 => 'Actif', 0 => 'Inactif'];
        $online       = \Illuminate\Support\Facades\Cache::has('OnlineUser.' . $row->id) ? 'En ligne' : 'Hors ligne';

        return [
            $row->id,
            $admin_name,
            $row->email,
            $admin_status[$row->status],
            $online,
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
            'En ligne',
            'Date de création',
            'Date de modification',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 35,
            'C' => 35,
            'D' => 15,
            'E' => 15,
            'F' => 22,
            'G' => 22,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '7C3AED']],
                'alignment' => ['horizontal' => 'center'],
            ],
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
