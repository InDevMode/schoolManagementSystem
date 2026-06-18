<?php

namespace Database\Seeders;

use App\Models\LeaveTypeModel;
use App\Models\StaffEventModel;
use App\Models\StaffLeaveModel;
use App\Models\StaffModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * StaffAndEventsSeeder — Génère des données RH réalistes.
 *
 * Ce seeder crée :
 *  - 5 types de congés
 *  - Des fiches personnel pour les professeurs existants
 *  - 6 demandes de congés (pending, approved, rejected)
 *  - 8 événements scolaires variés
 *
 * Usage : php artisan db:seed --class=StaffAndEventsSeeder
 */
class StaffAndEventsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeder Personnel & RH — démarrage...');

        // ── 1. Types de congés ────────────────────────────────────────────────
        $leaveTypes = $this->seedLeaveTypes();
        $this->command->info('  ✅ ' . count($leaveTypes) . ' types de congés créés/existants');

        // ── 2. Fiches personnel ───────────────────────────────────────────────
        $staffMembers = $this->seedStaff();
        $this->command->info('  ✅ ' . count($staffMembers) . ' membres du personnel créés/existants');

        // ── 3. Demandes de congés ─────────────────────────────────────────────
        $leaves = $this->seedLeaves($staffMembers, $leaveTypes);
        $this->command->info('  ✅ ' . count($leaves) . ' demandes de congés créées');

        // ── 4. Événements scolaires ───────────────────────────────────────────
        $events = $this->seedEvents();
        $this->command->info('  ✅ ' . count($events) . ' événements créés');

        $this->command->info('🎉 Seeder Personnel & RH terminé !');
        $this->command->newLine();
        $this->command->line('📊 Résumé congés :');
        $this->command->line('  • En attente : ' . StaffLeaveModel::where('status', 'pending')->where('is_delete', 0)->count());
        $this->command->line('  • Approuvés  : ' . StaffLeaveModel::where('status', 'approved')->where('is_delete', 0)->count());
        $this->command->line('  • Rejetés    : ' . StaffLeaveModel::where('status', 'rejected')->where('is_delete', 0)->count());
    }

    // ── Types de congés ───────────────────────────────────────────────────────

    private function seedLeaveTypes(): array
    {
        $types = [
            ['name' => 'Congé annuel',        'description' => 'Congé annuel ordinaire',                 'color' => '#3b82f6'],
            ['name' => 'Congé maladie',        'description' => 'Arrêt maladie avec certificat médical', 'color' => '#ef4444'],
            ['name' => 'Congé maternité',      'description' => 'Congé maternité/paternité',             'color' => '#8b5cf6'],
            ['name' => 'Congé exceptionnel',   'description' => 'Événement familial exceptionnel',       'color' => '#f59e0b'],
            ['name' => 'Congé sans solde',     'description' => 'Absence sans traitement',               'color' => '#6b7280'],
        ];

        $result = [];
        foreach ($types as $data) {
            $existing = LeaveTypeModel::where('name', $data['name'])->where('is_delete', 0)->first();
            if ($existing) {
                $result[] = $existing;
                continue;
            }

            $lt              = new LeaveTypeModel;
            $lt->name        = $data['name'];
            $lt->description = $data['description'];
            $lt->color       = $data['color'];
            $lt->save();
            $result[] = $lt;
        }

        return $result;
    }

    // ── Fiches personnel ──────────────────────────────────────────────────────

    private function seedStaff(): array
    {
        $admin    = User::where('user_type', 1)->where('is_delete', 0)->first();
        $teachers = User::where('user_type', 2)->where('is_delete', 0)->where('status', 1)->take(6)->get();

        $staffData = [
            ['role' => 'director',   'department' => 'Direction',     'employee_number' => 'DIR-2024-001'],
            ['role' => 'teacher',    'department' => 'Mathématiques', 'employee_number' => 'ENS-2024-001'],
            ['role' => 'teacher',    'department' => 'Français',      'employee_number' => 'ENS-2024-002'],
            ['role' => 'teacher',    'department' => 'Sciences',      'employee_number' => 'ENS-2024-003'],
            ['role' => 'accountant', 'department' => 'Comptabilité',  'employee_number' => 'CPT-2024-001'],
            ['role' => 'secretary',  'department' => 'Secrétariat',   'employee_number' => 'SEC-2024-001'],
        ];

        $result = [];
        $idx = 0;

        // D'abord le directeur depuis admin
        if ($admin && !StaffModel::getByUserId($admin->id)) {
            $s                  = new StaffModel;
            $s->user_id         = $admin->id;
            $s->role            = 'director';
            $s->status          = 'active';
            $s->hire_date       = '2020-09-01';
            $s->department      = 'Direction';
            $s->employee_number = 'DIR-2024-001';
            $s->created_by      = $admin->id;
            $s->save();
            $result[] = $s;
            $idx = 1;
        }

        foreach ($teachers as $teacher) {
            if (StaffModel::getByUserId($teacher->id)) continue;
            if ($idx >= count($staffData)) break;

            $data = $staffData[$idx];
            $s                  = new StaffModel;
            $s->user_id         = $teacher->id;
            $s->role            = $data['role'];
            $s->status          = 'active';
            $s->hire_date       = Carbon::now()->subYears(rand(1, 8))->format('Y-m-d');
            $s->department      = $data['department'];
            $s->employee_number = $data['employee_number'];
            $s->created_by      = $admin?->id ?? 1;
            $s->save();
            $result[] = $s;
            $idx++;
        }

        // Compléter avec les existants si besoin
        if (empty($result)) {
            $result = StaffModel::where('is_delete', 0)->take(5)->get()->all();
        }

        return $result;
    }

    // ── Demandes de congés ────────────────────────────────────────────────────

    private function seedLeaves(array $staffMembers, array $leaveTypes): array
    {
        if (empty($staffMembers) || empty($leaveTypes)) return [];

        $admin = User::where('user_type', 1)->where('is_delete', 0)->first();

        $leavesData = [
            // staff_idx, type_idx, start, end, reason, status, admin_note
            [0, 0, Carbon::today()->addDays(5)->format('Y-m-d'),  Carbon::today()->addDays(12)->format('Y-m-d'),  'Vacances familiales annuelles',            'pending',  null],
            [1, 1, Carbon::today()->subDays(3)->format('Y-m-d'),  Carbon::today()->addDays(4)->format('Y-m-d'),   'Grippe avec certificat médical',           'approved', 'Bien rétabli rapidement'],
            [2, 3, Carbon::today()->addDays(2)->format('Y-m-d'),  Carbon::today()->addDays(3)->format('Y-m-d'),   'Mariage d\'un proche',                     'pending',  null],
            [1, 0, Carbon::today()->subDays(30)->format('Y-m-d'), Carbon::today()->subDays(23)->format('Y-m-d'),  'Congé annuel planifié',                    'approved', 'Dossier en ordre'],
            [0, 4, Carbon::today()->addDays(15)->format('Y-m-d'), null,                                           'Formation universitaire à distance',        'rejected', 'Période trop chargée'],
            [2, 0, Carbon::today()->addDays(20)->format('Y-m-d'), Carbon::today()->addDays(27)->format('Y-m-d'),  'Repos bien mérité',                        'pending',  null],
        ];

        $result = [];
        foreach ($leavesData as [$sIdx, $tIdx, $start, $end, $reason, $status, $note]) {
            if (!isset($staffMembers[$sIdx]) || !isset($leaveTypes[$tIdx])) continue;

            $staff     = $staffMembers[$sIdx];
            $leaveType = $leaveTypes[$tIdx];

            // Vérifier doublon
            $existing = StaffLeaveModel::where('staff_id', $staff->id)
                ->where('leave_type_id', $leaveType->id)
                ->where('start_date', $start)
                ->where('is_delete', 0)
                ->first();
            if ($existing) {
                $result[] = $existing;
                continue;
            }

            $leave                = new StaffLeaveModel;
            $leave->staff_id      = $staff->id;
            $leave->leave_type_id = $leaveType->id;
            $leave->start_date    = $start;
            $leave->end_date      = $end;
            $leave->reason        = $reason;
            $leave->status        = $status;

            if ($status !== 'pending') {
                $leave->approved_by = $admin?->id;
                $leave->approved_at = Carbon::parse($start)->subDays(rand(1, 3));
                $leave->admin_note  = $note;
            }

            $leave->save();
            $result[] = $leave;
        }

        return $result;
    }

    // ── Événements scolaires ──────────────────────────────────────────────────

    private function seedEvents(): array
    {
        $admin = User::where('user_type', 1)->where('is_delete', 0)->first();
        $createdBy = $admin?->id ?? 1;

        $eventsData = [
            [
                'title'       => 'Réunion des parents d\'élèves',
                'event_type'  => 'administrative',
                'event_date'  => Carbon::today()->addDays(7)->format('Y-m-d'),
                'start_time'  => '08:00',
                'end_time'    => '11:00',
                'location'    => 'Salle polyvalente',
                'description' => 'Réunion trimestrielle des parents avec les professeurs principaux.',
            ],
            [
                'title'       => 'Examen du 2ème trimestre — Mathématiques',
                'event_type'  => 'exam',
                'event_date'  => Carbon::today()->addDays(12)->format('Y-m-d'),
                'start_time'  => '08:00',
                'end_time'    => '10:00',
                'location'    => 'Toutes les salles de classe',
                'description' => 'Composition de fin du 2ème trimestre en Mathématiques.',
            ],
            [
                'title'       => 'Journée culturelle et artistique',
                'event_type'  => 'cultural',
                'event_date'  => Carbon::today()->addDays(18)->format('Y-m-d'),
                'start_time'  => '09:00',
                'end_time'    => '17:00',
                'location'    => 'Cour principale',
                'description' => 'Expositions, danses traditionnelles, pièces de théâtre et concours artistiques.',
            ],
            [
                'title'       => 'Sortie pédagogique au musée',
                'event_type'  => 'trip',
                'event_date'  => Carbon::today()->addDays(25)->format('Y-m-d'),
                'start_time'  => '07:30',
                'end_time'    => '17:00',
                'location'    => 'Musée national de Cotonou',
                'description' => 'Sortie culturelle et éducative pour les classes de terminale.',
            ],
            [
                'title'       => 'Conseil des professeurs — 2ème trimestre',
                'event_type'  => 'academic',
                'event_date'  => Carbon::today()->addDays(30)->format('Y-m-d'),
                'start_time'  => '14:00',
                'end_time'    => '17:30',
                'location'    => 'Salle de conférence',
                'description' => 'Délibération des notes du 2ème trimestre et remise des bulletins.',
            ],
            [
                'title'       => 'Cérémonie de remise des bulletins',
                'event_type'  => 'ceremony',
                'event_date'  => Carbon::today()->addDays(35)->format('Y-m-d'),
                'start_time'  => '08:00',
                'end_time'    => '12:00',
                'location'    => 'Amphithéâtre',
                'description' => 'Remise solennelle des bulletins scolaires du 2ème trimestre.',
            ],
            [
                'title'       => 'Formation pédagogique des enseignants',
                'event_type'  => 'academic',
                'event_date'  => Carbon::today()->subDays(5)->format('Y-m-d'),
                'start_time'  => '08:00',
                'end_time'    => '16:00',
                'location'    => 'Salle informatique',
                'description' => 'Formation aux nouvelles méthodes pédagogiques et outils numériques.',
            ],
            [
                'title'       => 'Rentrée scolaire 3ème trimestre',
                'event_type'  => 'academic',
                'event_date'  => Carbon::today()->addDays(50)->format('Y-m-d'),
                'start_time'  => '07:30',
                'end_time'    => '12:00',
                'location'    => 'Établissement',
                'description' => 'Rentrée des classes pour le 3ème et dernier trimestre de l\'année.',
            ],
        ];

        $result = [];
        foreach ($eventsData as $data) {
            $existing = StaffEventModel::where('title', $data['title'])
                ->where('event_date', $data['event_date'])
                ->where('is_delete', 0)
                ->first();

            if ($existing) {
                $result[] = $existing;
                continue;
            }

            $ev              = new StaffEventModel;
            $ev->title       = $data['title'];
            $ev->event_type  = $data['event_type'];
            $ev->event_date  = $data['event_date'];
            $ev->start_time  = $data['start_time'];
            $ev->end_time    = $data['end_time'];
            $ev->location    = $data['location'];
            $ev->description = $data['description'];
            $ev->created_by  = $createdBy;
            $ev->save();

            $result[] = $ev;
        }

        return $result;
    }
}
