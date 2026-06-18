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

class ExportStudent implements FromCollection, WithMapping, WithHeadings, WithColumnWidths, WithStyles
{
    private static array $genderMap     = ['male' => 'Masculin', 'female' => 'Féminin', 'other' => 'Autre'];
    private static array $statusMap     = [1 => 'Actif', 0 => 'Inactif'];
    private static array $bloodGroupMap = [
        'a+' => 'A+', 'a-' => 'A-', 'b+' => 'B+', 'b-' => 'B-',
        'ab+' => 'AB+', 'ab-' => 'AB-', 'o+' => 'O+', 'o-' => 'O-',
    ];

    public function map($row): array
    {
        return [
            $row->id,
            $row->admission_number ?? '—',
            trim($row->last_name . ' ' . $row->name),
            trim(($row->parent_last_name ?? '') . ' ' . ($row->parent_name ?? '')),
            $row->email,
            $row->mobile_number ?? '—',
            self::$genderMap[$row->gender] ?? $row->gender ?? '—',
            $row->roll_number ?? '—',
            $this->formatDate($row->date_of_birth),
            $this->formatDate($row->admission_date),
            $row->class_name ?? '—',
            self::$statusMap[(int) $row->status] ?? '—',
            self::$bloodGroupMap[$row->blood_group] ?? ($row->blood_group ?? '—'),
            $row->height ?? '—',
            $row->weight ?? '—',
            Cache::has('OnlineUser.' . $row->id) ? 'En ligne' : 'Hors ligne',
            $this->formatDatetime($row->created_at),
            $this->formatDatetime($row->updated_at),
        ];
    }

    public function headings(): array
    {
        return [
            'ID', 'N° Matricule', 'Nom et Prénoms', 'Parent',
            'Email', 'Téléphone', 'Genre', 'N° de rôle',
            'Date de naissance', 'Date d\'admission', 'Classe',
            'Statut', 'Groupe sanguin', 'Taille (cm)', 'Poids (kg)',
            'Présence', 'Créé le', 'Modifié le',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  'B' => 18, 'C' => 30, 'D' => 28,
            'E' => 30, 'F' => 16, 'G' => 12, 'H' => 12,
            'I' => 16, 'J' => 16, 'K' => 18, 'L' => 12,
            'M' => 14, 'N' => 12, 'O' => 12, 'P' => 14,
            'Q' => 20, 'R' => 20,
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
        return User::getAllStudentList();
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
