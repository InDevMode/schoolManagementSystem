<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportAdmin implements FromCollection, WithMapping, WithHeadings, WithColumnWidths, WithStyles
{
    private static array $statusMap = [1 => 'Actif', 0 => 'Inactif'];

    public function map($row): array
    {
        $fullName = trim($row->last_name . ' ' . $row->name);
        $status   = self::$statusMap[(int) $row->status] ?? '—';
        $online   = Cache::has('OnlineUser.' . $row->id) ? 'En ligne' : 'Hors ligne';

        return [
            $row->id,
            $fullName,
            $row->email,
            $row->mobile_number ?? '—',
            $status,
            $online,
            $this->formatDate($row->created_at),
            $this->formatDate($row->updated_at),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Prénoms et Nom',
            'Email',
            'Téléphone',
            'Statut',
            'Présence',
            'Date de création',
            'Date de modification',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 35,
            'C' => 35,
            'D' => 18,
            'E' => 12,
            'F' => 14,
            'G' => 22,
            'H' => 22,
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
        return User::getAllAdminList();
    }

    private function formatDate($date): string
    {
        if (!$date) return '—';
        try {
            return Carbon::parse($date)->format('d/m/Y H:i:s');
        } catch (\Exception $e) {
            return '—';
        }
    }
}
