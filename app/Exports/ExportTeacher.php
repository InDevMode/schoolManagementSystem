<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ExportTeacher implements FromCollection, WithMapping, WithHeadings, WithColumnWidths
{
    public function map($row): array
    {
        $teacher_name = $row->name . ' ' . $row->last_name;
        $teacher_gender = [
            'male' => 'Homme',
            'female' => 'Femme',
        ];
        $teacher_status = [
            1 => 'Actif',
            0 => 'Inactif',
        ];

        return [
            $row->id,
            $teacher_name,
            $row->email,
            $row->mobile_number,
            $teacher_gender[$row->gender],
            $this->formatDate($row->date_of_birth),
            $this->formatDate($row->admission_date),
            $row->address,
            $row->permanent_address,
            $row->marital_status,
            $row->qualification,
            $row->note,
            $row->work_experience,
            $teacher_status[$row->status],
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
            'Date de naissance',
            'Date d\'Adhésion',
            'Adresse Actuelle',
            'Adresse Permanente',
            'Situation Matrimoniale',
            'Qualification',
            'Note',
            'Expérience de Travail',
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
            'G' => 40,
            'H' => 40,
            'I' => 40,
            'J' => 40,
            'K' => 40,
            'L' => 40,
            'M' => 40,
            'N' => 40,
            'O' => 40,
            'P' => 40,
        ];
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::getAllTeacherList();
    }

    private function formatDate($date): ?string
    {
        return $date ? Carbon::parse($date)->format('d-m-Y') : null;
    }

}
