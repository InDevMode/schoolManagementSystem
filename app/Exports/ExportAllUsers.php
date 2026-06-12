<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportAllUsers implements FromCollection, WithMapping, WithHeadings, WithColumnWidths, WithStyles
{
    private Collection $users;

    private static array $roleMap = [
        0 => 'Super Administrateur',
        1 => 'Administrateur',
        2 => 'Professeur',
        3 => 'Apprenant',
        4 => 'Parent',
    ];

    private static array $statusMap = [1 => 'Actif', 0 => 'Inactif'];

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    public function collection(): Collection
    {
        return $this->users;
    }

    public function map($row): array
    {
        $fullName  = trim($row->last_name . ' ' . $row->name);
        $role      = self::$roleMap[(int) $row->user_type] ?? ('Rôle custom (' . $row->user_type . ')');
        $status    = self::$statusMap[(int) $row->status] ?? '—';
        $online    = Cache::has('OnlineUser.' . $row->id) ? 'En ligne' : 'Hors ligne';

        return [
            $row->id,
            $fullName,
            $row->email,
            $row->mobile_number ?? '—',
            $role,
            $status,
            $online,
            $row->school_name ?? '—',
            $this->formatDatetime($row->created_at),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Prénoms et Nom',
            'Email',
            'Téléphone',
            'Rôle',
            'Statut',
            'Présence',
            'École',
            'Créé le',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 32,
            'C' => 32,
            'D' => 16,
            'E' => 22,
            'F' => 12,
            'G' => 14,
            'H' => 20,
            'I' => 22,
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

    private function formatDatetime($date): string
    {
        if (!$date) return '—';
        try { return Carbon::parse($date)->format('d/m/Y H:i:s'); } catch (\Exception $e) { return '—'; }
    }
}
