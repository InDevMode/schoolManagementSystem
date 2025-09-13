<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ExportStudent implements FromCollection, WithMapping, WithHeadings, WithColumnWidths
{

    public function map($row): array
    {
        $student_name = $row->name . ' ' . $row->last_name;
        $parent_name = $row->parent_name . ' ' . $row->parent_last_name;
        $student_gender = [
            'male' => 'Homme',
            'female' => 'Femme',
        ];
        $student_status = [
            1 => 'Actif',
            0 => 'Inactif',
        ];
        $student_blood_group = [
            'a+' => 'A+',
            'a-' => 'A-',
            'b+' => 'B+',
            'b-' => 'B-',
            'ab+' => 'AB+',
            'ab-' => 'AB-',
            'o+' => 'O+',
            'o-' => 'O-',
        ];

        return [
            $row->id,
            $row->admission_number,
            $student_name,
            $parent_name,
            $row->email,
            $row->mobile_number,
            $student_gender[$row->gender],
            $row->roll_number,
            $this->formatDate($row->date_of_birth),
            $this->formatDate($row->admission_date),
            $row->caste,
            $row->religion,
            $row->class_name,
            $student_status[$row->status],
            $student_blood_group[$row->blood_group],
            $row->height,
            $row->weight,
            $row->created_at->format('d-m-Y H:i:s'),
            $row->updated_at->format('d-m-Y H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Numéro matricule',
            'Nom et Prénoms de l\'apprenant',
            'Nom et Prénoms du parent',
            'Email',
            'Téléphone',
            'Genre',
            'Numéro de rôle',
            'Date de naissance',
            'Date d\'admission',
            'Caste',
            'Religion',
            'Classe',
            'Statut',
            'Groupe sanguin',
            'Taille',
            'Poids',
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
           'Q' => 40,
           'R' => 40,
           'S' => 40,
        ];
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::getAllStudentList();
    }

        private function formatDate($date): ?string
    {
        return $date ? Carbon::parse($date)->format('d-m-Y') : null;
    }

}
