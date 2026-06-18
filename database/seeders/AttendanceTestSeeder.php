<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AttendanceTestSeeder extends Seeder
{
    /**
     * Crée 9 apprenants supplémentaires dans la classe Test 6A (id=15)
     * pour tester la saisie de présence avec 10 apprenants au total.
     */
    public function run(): void
    {
        $class_id = 15; // Test 6A

        $students = [
            ['name' => 'Marie',    'last_name' => 'MARTIN',    'email' => 'marie.martin.test@school.dev'],
            ['name' => 'Thomas',   'last_name' => 'BERNARD',   'email' => 'thomas.bernard.test@school.dev'],
            ['name' => 'Sophie',   'last_name' => 'DURAND',    'email' => 'sophie.durand.test@school.dev'],
            ['name' => 'Lucas',    'last_name' => 'MOREAU',    'email' => 'lucas.moreau.test@school.dev'],
            ['name' => 'Emma',     'last_name' => 'PETIT',     'email' => 'emma.petit.test@school.dev'],
            ['name' => 'Hugo',     'last_name' => 'ROBERT',    'email' => 'hugo.robert.test@school.dev'],
            ['name' => 'Chloé',    'last_name' => 'RICHARD',   'email' => 'chloe.richard.test@school.dev'],
            ['name' => 'Nathan',   'last_name' => 'SIMON',     'email' => 'nathan.simon.test@school.dev'],
            ['name' => 'Camille',  'last_name' => 'MICHEL',    'email' => 'camille.michel.test@school.dev'],
        ];

        foreach ($students as $data) {
            // Éviter les doublons si le seeder est relancé
            if (User::where('email', $data['email'])->exists()) {
                continue;
            }

            User::create([
                'name'             => $data['name'],
                'last_name'        => $data['last_name'],
                'email'            => $data['email'],
                'password'         => Hash::make('password'),
                'user_type'        => 3,      // 3 = Apprenant
                'class_id'         => $class_id,
                'status'           => 1,
                'is_delete'        => 0,
                'admission_number' => strtoupper(substr($data['last_name'], 0, 3)) . rand(1000, 9999),
                'gender'           => in_array($data['name'], ['Marie', 'Sophie', 'Emma', 'Chloé', 'Camille']) ? 'female' : 'male',
                'admission_date'   => '2025-09-01',
            ]);
        }

        $this->command->info('✅ 9 apprenants créés dans la classe Test 6A (id=15).');
    }
}
