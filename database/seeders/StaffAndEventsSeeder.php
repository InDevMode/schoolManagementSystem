<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * StaffAndEventsSeeder — Données RH : fiches personnel, congés, événements scolaires.
 *
 * Dépendances :
 *   - LeaveTypesSeeder (leave_types doit être peuplé)
 *   - MultiSchoolSeeder (users admin/teacher doivent exister)
 */
class StaffAndEventsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('  🌱 Gestion Personnel...');

        $this->seedStaff();
        $this->seedLeaves();
        $this->seedEvents();

        $this->command->info('  ✅ Personnel, congés et événements créés.');
    }

    // ── Fiches personnel ──────────────────────────────────────────────────────

    private function seedStaff(): void
    {
        $staffData = [
            ['role' => 'director',   'department' => 'Direction',     'employee_number' => 'DIR-2024-001'],
            ['role' => 'teacher',    'department' => 'Mathématiques', 'employee_number' => 'ENS-2024-001'],
            ['role' => 'teacher',    'department' => 'Français',      'employee_number' => 'ENS-2024-002'],
            ['role' => 'teacher',    'department' => 'Sciences',      'employee_number' => 'ENS-2024-003'],
            ['role' => 'accountant', 'department' => 'Comptabilité',  'employee_number' => 'CPT-2024-001'],
            ['role' => 'secretary',  'department' => 'Secrétariat',   'employee_number' => 'SEC-2024-001'],
        ];

        // Prendre admin + 5 premiers profs tous établissements confondus
        $admin    = User::where('user_type', 1)->where('is_delete', 0)->orderBy('id')->first();
        $teachers = User::where('user_type', 2)->where('is_delete', 0)->orderBy('id')->take(5)->get();

        $users   = collect();
        if ($admin) $users->push($admin);
        foreach ($teachers as $t) $users->push($t);

        foreach ($users as $idx => $user) {
            $alreadyStaff = DB::table('staff')->where('user_id', $user->id)->exists();
            if ($alreadyStaff) continue;

            $data = $staffData[$idx] ?? $staffData[1]; // fallback teacher

            // Vérifier l'unicité du numéro d'employé
            $empNum = $data['employee_number'];
            if (DB::table('staff')->where('employee_number', $empNum)->exists()) {
                $empNum = $empNum . '-' . $user->id;
            }

            DB::table('staff')->insert([
                'user_id'         => $user->id,
                'school_id'       => $user->school_id,
                'role'            => $data['role'],
                'status'          => 'active',
                'hire_date'       => Carbon::now()->subYears(rand(1, 8))->format('Y-m-d'),
                'department'      => $data['department'],
                'employee_number' => $empNum,
                'is_delete'       => 0,
                'created_by'      => $admin?->id ?? 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }

    // ── Demandes de congés ────────────────────────────────────────────────────

    private function seedLeaves(): void
    {
        $staffMembers = DB::table('staff')->where('is_delete', 0)->orderBy('id')->take(4)->get();
        $leaveTypes   = DB::table('leave_types')->where('is_delete', 0)->orderBy('id')->take(5)->get();

        if ($staffMembers->isEmpty() || $leaveTypes->isEmpty()) return;

        $admin     = User::where('user_type', 1)->where('is_delete', 0)->orderBy('id')->first();
        $adminId   = $admin?->id;

        $leavesData = [
            [0, 0, Carbon::today()->addDays(5),   Carbon::today()->addDays(12),  'Vacances familiales',          'pending',  null],
            [1, 1, Carbon::today()->subDays(3),   Carbon::today()->addDays(4),   'Grippe avec certificat',       'approved', 'Bien rétabli rapidement'],
            [2, 3, Carbon::today()->addDays(2),   Carbon::today()->addDays(3),   'Mariage d\'un proche',         'pending',  null],
            [1, 0, Carbon::today()->subDays(30),  Carbon::today()->subDays(23),  'Congé annuel planifié',        'approved', 'Dossier en ordre'],
            [0, 4, Carbon::today()->addDays(15),  null,                           'Formation universitaire',      'rejected', 'Période trop chargée'],
            [2, 0, Carbon::today()->addDays(20),  Carbon::today()->addDays(27),  'Repos bien mérité',            'pending',  null],
        ];

        foreach ($leavesData as [$sIdx, $tIdx, $start, $end, $reason, $status, $note]) {
            $staff     = $staffMembers[$sIdx] ?? null;
            $leaveType = $leaveTypes[$tIdx]   ?? null;
            if (! $staff || ! $leaveType) continue;

            $startStr = $start->format('Y-m-d');
            $exists = DB::table('staff_leaves')
                ->where('staff_id', $staff->id)
                ->where('leave_type_id', $leaveType->id)
                ->where('start_date', $startStr)
                ->exists();
            if ($exists) continue;

            DB::table('staff_leaves')->insert([
                'staff_id'      => $staff->id,
                'leave_type_id' => $leaveType->id,
                'start_date'    => $startStr,
                'end_date'      => $end?->format('Y-m-d'),
                'reason'        => $reason,
                'status'        => $status,
                'approved_by'   => $status !== 'pending' ? $adminId : null,
                'approved_at'   => $status !== 'pending' ? $start->subDays(1)->toDateTimeString() : null,
                'admin_note'    => $note,
                'is_delete'     => 0,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }

    // ── Événements scolaires ──────────────────────────────────────────────────

    private function seedEvents(): void
    {
        $admin     = User::where('user_type', 1)->where('is_delete', 0)->orderBy('id')->first();
        $createdBy = $admin?->id ?? 1;
        $schoolId  = $admin?->school_id;

        $eventsData = [
            ['Réunion des parents d\'apprenants',          'administrative', Carbon::today()->addDays(7),  '08:00', '11:00', 'Salle polyvalente'],
            ['Examen 2ème trimestre — Mathématiques',  'exam',           Carbon::today()->addDays(12), '08:00', '10:00', 'Toutes salles'],
            ['Journée culturelle et artistique',       'cultural',       Carbon::today()->addDays(18), '09:00', '17:00', 'Cour principale'],
            ['Sortie pédagogique au musée',            'trip',           Carbon::today()->addDays(25), '07:30', '17:00', 'Musée national'],
            ['Conseil des professeurs — 2ème trim.',   'academic',       Carbon::today()->addDays(30), '14:00', '17:30', 'Salle de conférence'],
            ['Cérémonie de remise des bulletins',      'ceremony',       Carbon::today()->addDays(35), '08:00', '12:00', 'Amphithéâtre'],
            ['Formation pédagogique des enseignants',  'academic',       Carbon::today()->subDays(5),  '08:00', '16:00', 'Salle informatique'],
            ['Rentrée scolaire 3ème trimestre',        'academic',       Carbon::today()->addDays(50), '07:30', '12:00', 'Établissement'],
        ];

        foreach ($eventsData as [$title, $type, $date, $start, $end, $location]) {
            $dateStr = $date->format('Y-m-d');
            $exists  = DB::table('staff_events')
                ->where('title', $title)
                ->where('event_date', $dateStr)
                ->exists();
            if ($exists) continue;

            DB::table('staff_events')->insert([
                'school_id'  => $schoolId,
                'title'      => $title,
                'event_type' => $type,
                'event_date' => $dateStr,
                'start_time' => $start,
                'end_time'   => $end,
                'location'   => $location,
                'is_delete'  => 0,
                'created_by' => $createdBy,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
