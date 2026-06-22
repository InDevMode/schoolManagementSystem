<?php

namespace App\Exports;

use App\Models\StudentAttendanceModel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Illuminate\Support\Collection;

class ExportAttendance implements FromCollection, WithMapping, WithHeadings, WithColumnWidths
{
    /**
     * Titres des colonnes
     */
    public function headings(): array
    {
        return [
            'ID',
            'Numéro Matricule',
            'Nom et Prénoms',
            'Classe',
            'Date de présence',
            'Statut',
            'Créé par',
            'Date de création',
            'Date de modification',
        ];
    }

    /**
     * Largeur des colonnes
     */
    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 30,
            'D' => 20,
            'E' => 20,
            'F' => 15,
            'G' => 25,
            'H' => 20,
            'I' => 20,
        ];
    }

    /**
     * Mapping de chaque ligne
     */
    public function map($row): array
    {
        $studentName = $row->student_name . ' ' . $row->student_last_name;

        $attendanceType = match ($row->attendance_type) {
            1, '1' => 'Présent',
            2, '2' => 'Retard',
            3, '3' => 'Absent',
            4, '4' => 'Demi-Journée',
            default => $row->attendance_type,
        };

        return [
            $row->id,
            $row->student_number,
            $studentName,
            $row->class_name,
            $this->formatDate($row->attendance_date),
            $attendanceType,
            $row->created_by_name,
            $row->created_at->format('d-m-Y H:i:s'),
            $row->updated_at->format('d-m-Y H:i:s'),
        ];
    }

    /**
     * Retourner les données à exporter
     */
    public function collection(): Collection
    {
        // Si tu veux TOUTES les présences :
        return StudentAttendanceModel::getAllAttendance();

    }

    private function formatDate($date): ?string
    {
        return $date ? Carbon::parse($date)->format('d-m-Y H:i') : null;
    }

}

