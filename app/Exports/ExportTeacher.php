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

class ExportTeacher implements FromCollection, WithMapping, WithHeadings, WithColumnWidths, WithStyles
{
    private static array $genderMap  = ['male' => 'Masculin', 'female' => 'Féminin', 'other' => 'Autre'];
    private static array $statusMap  = [1 => 'Actif', 0 => 'Inactif'];

    public function map($row): array
    {
        return [
            $row->id,
            trim($row->last_name . ' ' . $row->name),
            $row->email,
            $row->mobile_number       ?? '—',
            self::$genderMap[$row->gender] ?? ($row->gender ?? '—'),
            $this->formatDate($row->date_of_birth),
            $this->formatDate($row->admission_date),
            $row->address             ?? '—',
            $row->permanent_address   ?? '—',
            $row->marital_status      ?? '—',
            $row->qualification       ?? '—',
            $row->note                ?? '—',
            $row->work_experience     ?? '—',
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
            'Genre', 'Date de naissance', 'Date d\'embauche',
            'Adresse actuelle', 'Adresse permanente', 'Situation matrimoniale',
            'Qualification', 'Note', 'Expérience (années)',
            'Statut', 'Présence', 'Créé le', 'Modifié le',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  'B' => 30, 'C' => 30, 'D' => 16,
            'E' => 12, 'F' => 16, 'G' => 16, 'H' => 28,
            'I' => 28, 'J' => 22, 'K' => 20, 'L' => 20,
            'M' => 14, 'N' => 12, 'O' => 14, 'P' => 20,
            'Q' => 20,
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
        return User::getAllTeacherList();
    }

    private function formatDate(?string $date): string
    {
        if (!$date) return '—';
        try { return Carbon::parse($date)->format('d/m/Y'); } catch (\Exception $e) { return '—'; }
    }

    private function formatDatetime($date): string
    {
        if (!$date) return '—';
        try { return Carbon::parse($date)->format('d/m/Y H:i:s'); } catch (\Exception $e) { return '—'; }
    }
}
