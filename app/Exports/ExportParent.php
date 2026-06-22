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

class ExportParent implements FromCollection, WithMapping, WithHeadings, WithColumnWidths, WithStyles
{
    private static array $statusMap = [1 => 'Actif', 0 => 'Inactif'];
    private static array $genderMap = ['male' => 'Masculin', 'female' => 'Féminin', 'other' => 'Autre'];

    public function map($row): array
    {
        return [
            $row->id,
            trim($row->last_name . ' ' . $row->name),
            $row->email,
            $row->mobile_number ?? '—',
            self::$genderMap[$row->gender] ?? ($row->gender ?? '—'),
            $row->address     ?? '—',
            $row->occupation  ?? '—',
            self::$statusMap[(int) $row->status] ?? '—',
            Cache::has('OnlineUser.' . $row->id) ? 'En ligne' : 'Hors ligne',
            $this->formatDatetime($row->created_at),
            $this->formatDatetime($row->updated_at),
        ];
    }

    public function headings(): array
    {
        return [
            'ID', 'Nom et Prénoms', 'Email', 'Téléphone',
            'Genre', 'Adresse', 'Occupation', 'Statut',
            'Présence', 'Créé le', 'Modifié le',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  'B' => 30, 'C' => 30, 'D' => 16,
            'E' => 12, 'F' => 30, 'G' => 20, 'H' => 12,
            'I' => 14, 'J' => 20, 'K' => 20,
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

    public function collection()
    {
        return User::getAllParentList();
    }

    private function formatDatetime($date): string
    {
        if (!$date) return '—';
        try { return \Carbon\Carbon::parse($date)->format('d-m-Y H:i:s'); } catch (\Exception $e) { return '—'; }
    }
}
