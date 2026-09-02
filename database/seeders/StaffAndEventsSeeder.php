<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * StaffAndEventsSeeder — Staff, congés, événements, types personnalisés,
 * présences, frais, devoirs, annonces noticeboard, chats, notifications.
 * Toutes colonnes nullable renseignées. Max 20 lignes par table.
 */
class StaffAndEventsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('  🌱 Staff, congés, événements...');
        $this->seedEventTypeCustoms();
        $this->seedStaff();
        $this->seedLeaves();
        $this->seedEvents();
        $this->command->info('  🌱 Présences, frais, devoirs, annonces...');
        $this->seedAttendances();
        $this->seedFees();
        $this->seedWorks();
        $this->seedHomework();
        $this->seedNoticeboard();
        $this->seedChats();
        $this->seedNotifications();
        $this->command->info('  ✅ Toutes les données complémentaires insérées.');
    }

    // ── Types d'événements personnalisés ─────────────────────────────────────

    private function seedEventTypeCustoms(): void
    {
        $types = [
            ['school_id' => SchoolSeeder::LMC_ID, 'admin_id' => MultiSchoolSeeder::LMC_ADMIN_ID,
             'name' => 'Conseil de classe',    'color' => '#6366f1', 'description' => 'Réunion du conseil de classe trimestriel'],
            ['school_id' => SchoolSeeder::LMC_ID, 'admin_id' => MultiSchoolSeeder::LMC_ADMIN_ID,
             'name' => 'Journée portes ouvertes', 'color' => '#10b981', 'description' => 'Journée d\'accueil pour futurs élèves'],
            ['school_id' => SchoolSeeder::CSM_ID, 'admin_id' => MultiSchoolSeeder::CSM_ADMIN_ID,
             'name' => 'Réunion parents-profs', 'color' => '#f59e0b', 'description' => 'Rencontre trimestrielle parents et professeurs'],
            ['school_id' => SchoolSeeder::CSM_ID, 'admin_id' => MultiSchoolSeeder::CSM_ADMIN_ID,
             'name' => 'Concours interne',     'color' => '#ef4444', 'description' => 'Compétition académique inter-classes'],
            ['school_id' => SchoolSeeder::EPE_ID, 'admin_id' => MultiSchoolSeeder::EPE_ADMIN_ID,
             'name' => 'Fête scolaire',         'color' => '#f97316', 'description' => 'Célébration de fin d\'année'],
        ];

        foreach ($types as $t) {
            if (!DB::table('event_type_customs')->where('name', $t['name'])->where('school_id', $t['school_id'])->exists()) {
                DB::table('event_type_customs')->insert([
                    'id'          => (string) Str::uuid(),
                    'school_id'   => $t['school_id'],
                    'name'        => $t['name'],
                    'color'       => $t['color'],
                    'description' => $t['description'],
                    'is_delete'   => 0,
                    'created_by'  => $t['admin_id'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
        $this->command->info('    ✅ 5 types d\'événements personnalisés.');
    }

    // ── Fiches staff ──────────────────────────────────────────────────────────

    private function seedStaff(): void
    {
        $staffDefs = [
            // LMC
            [
                'user_id'   => MultiSchoolSeeder::LMC_ADMIN_ID,
                'school_id' => SchoolSeeder::LMC_ID,
                'role'      => 'director',
                'department'=> 'Direction',
                'emp_num'   => 'LMC-DIR-2020-001',
                'hire_date' => '2020-09-01',
                'end_date'  => '2030-08-31',
                'status'    => 'active',
                'bio'       => 'Directeur administratif avec 15 ans d\'expérience dans l\'éducation nationale béninoise.',
                'created_by'=> MultiSchoolSeeder::LMC_ADMIN_ID,
            ],
            [
                'user_id'   => MultiSchoolSeeder::LMC_PROF1_ID,
                'school_id' => SchoolSeeder::LMC_ID,
                'role'      => 'teacher',
                'department'=> 'Mathématiques et Sciences',
                'emp_num'   => 'LMC-ENS-2020-001',
                'hire_date' => '2020-09-01',
                'end_date'  => '2030-08-31',
                'status'    => 'active',
                'bio'       => 'Professeur de mathématiques certifié, responsable de la filière scientifique.',
                'created_by'=> MultiSchoolSeeder::LMC_ADMIN_ID,
            ],
            [
                'user_id'   => MultiSchoolSeeder::LMC_PROF2_ID,
                'school_id' => SchoolSeeder::LMC_ID,
                'role'      => 'teacher',
                'department'=> 'Lettres et Langues',
                'emp_num'   => 'LMC-ENS-2021-002',
                'hire_date' => '2021-09-01',
                'end_date'  => '2031-08-31',
                'status'    => 'active',
                'bio'       => 'Professeure de français et coordinatrice du club de lecture.',
                'created_by'=> MultiSchoolSeeder::LMC_ADMIN_ID,
            ],
            // CSM
            [
                'user_id'   => MultiSchoolSeeder::CSM_ADMIN_ID,
                'school_id' => SchoolSeeder::CSM_ID,
                'role'      => 'director',
                'department'=> 'Direction Pédagogique',
                'emp_num'   => 'CSM-DIR-2019-001',
                'hire_date' => '2019-09-01',
                'end_date'  => '2029-08-31',
                'status'    => 'active',
                'bio'       => 'Directrice pédagogique avec 20 ans d\'expérience dans l\'enseignement secondaire.',
                'created_by'=> MultiSchoolSeeder::CSM_ADMIN_ID,
            ],
            [
                'user_id'   => MultiSchoolSeeder::CSM_PROF1_ID,
                'school_id' => SchoolSeeder::CSM_ID,
                'role'      => 'teacher',
                'department'=> 'Sciences',
                'emp_num'   => 'CSM-ENS-2021-001',
                'hire_date' => '2021-09-01',
                'end_date'  => '2031-08-31',
                'status'    => 'active',
                'bio'       => 'Professeur de sciences, chef de département, passionné par la pédagogie active.',
                'created_by'=> MultiSchoolSeeder::CSM_ADMIN_ID,
            ],
            [
                'user_id'   => MultiSchoolSeeder::CSM_PROF2_ID,
                'school_id' => SchoolSeeder::CSM_ID,
                'role'      => 'teacher',
                'department'=> 'Langues Étrangères',
                'emp_num'   => 'CSM-ENS-2022-002',
                'hire_date' => '2022-09-01',
                'end_date'  => '2032-08-31',
                'status'    => 'active',
                'bio'       => 'Professeure d\'anglais, responsable du club d\'anglais et des échanges internationaux.',
                'created_by'=> MultiSchoolSeeder::CSM_ADMIN_ID,
            ],
            // EPE
            [
                'user_id'   => MultiSchoolSeeder::EPE_ADMIN_ID,
                'school_id' => SchoolSeeder::EPE_ID,
                'role'      => 'director',
                'department'=> 'Direction',
                'emp_num'   => 'EPE-DIR-2018-001',
                'hire_date' => '2018-09-01',
                'end_date'  => '2028-08-31',
                'status'    => 'active',
                'bio'       => 'Directeur de l\'école primaire Les Étoiles depuis 2018. Spécialiste de la pédagogie Freinet.',
                'created_by'=> MultiSchoolSeeder::EPE_ADMIN_ID,
            ],
            [
                'user_id'   => MultiSchoolSeeder::EPE_PROF1_ID,
                'school_id' => SchoolSeeder::EPE_ID,
                'role'      => 'teacher',
                'department'=> 'Cycle Primaire',
                'emp_num'   => 'EPE-ENS-2020-001',
                'hire_date' => '2020-09-01',
                'end_date'  => '2030-08-31',
                'status'    => 'active',
                'bio'       => 'Instituteur titulaire de la classe CE2. Maître expérimenté et apprécié des élèves.',
                'created_by'=> MultiSchoolSeeder::EPE_ADMIN_ID,
            ],
            [
                'user_id'   => MultiSchoolSeeder::EPE_PROF2_ID,
                'school_id' => SchoolSeeder::EPE_ID,
                'role'      => 'teacher',
                'department'=> 'Cycle Primaire',
                'emp_num'   => 'EPE-ENS-2022-002',
                'hire_date' => '2022-09-01',
                'end_date'  => '2032-08-31',
                'status'    => 'active',
                'bio'       => 'Institutrice titulaire de la classe CM1. Spécialiste des méthodes de lecture active.',
                'created_by'=> MultiSchoolSeeder::EPE_ADMIN_ID,
            ],
        ];

        $count = 0;
        foreach ($staffDefs as $s) {
            if (!DB::table('staff')->where('user_id', $s['user_id'])->exists()) {
                DB::table('staff')->insert([
                    'id'              => (string) Str::uuid(),
                    'user_id'         => $s['user_id'],
                    'school_id'       => $s['school_id'],
                    'role'            => $s['role'],
                    'status'          => $s['status'],
                    'hire_date'       => $s['hire_date'],
                    'end_date'        => $s['end_date'],
                    'employee_number' => $s['emp_num'],
                    'department'      => $s['department'],
                    'bio'             => $s['bio'],
                    'is_delete'       => 0,
                    'created_by'      => $s['created_by'],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
                $count++;
            }
        }
        $this->command->info("    ✅ $count fiches staff insérées.");
    }

    // ── Demandes de congés ────────────────────────────────────────────────────

    private function seedLeaves(): void
    {
        $staffRows = DB::table('staff')->where('is_delete', 0)->get()->keyBy('user_id');
        $leaveTypes = DB::table('leave_types')->where('is_delete', 0)->get();

        if ($staffRows->isEmpty() || $leaveTypes->isEmpty()) return;

        $lt = $leaveTypes->values();

        $leaveDefs = [
            [
                'staff_user' => MultiSchoolSeeder::LMC_PROF1_ID,
                'lt_idx' => 0, 'status' => 'approved',
                'start' => '2025-12-23', 'end' => '2026-01-05',
                'reason' => 'Congé de fin d\'année scolaire. Repos nécessaire avant le 2ème trimestre.',
                'admin_note' => 'Accordé. Bon repos et bonne reprise.',
                'approved_by' => MultiSchoolSeeder::LMC_ADMIN_ID,
                'approved_at' => '2025-12-10 10:00:00',
            ],
            [
                'staff_user' => MultiSchoolSeeder::LMC_PROF2_ID,
                'lt_idx' => 1, 'status' => 'approved',
                'start' => '2025-11-10', 'end' => '2025-11-15',
                'reason' => 'Arrêt maladie — grippe saisonnière avec fièvre. Certificat médical joint.',
                'admin_note' => 'Approuvé sur présentation du certificat médical.',
                'approved_by' => MultiSchoolSeeder::LMC_ADMIN_ID,
                'approved_at' => '2025-11-09 08:30:00',
            ],
            [
                'staff_user' => MultiSchoolSeeder::CSM_PROF1_ID,
                'lt_idx' => 4, 'status' => 'pending',
                'start' => '2026-03-15', 'end' => '2026-03-16',
                'reason' => 'Absence pour mariage d\'un cousin. Invitation officielle disponible.',
                'admin_note' => 'En attente de validation du directeur.',
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'staff_user' => MultiSchoolSeeder::CSM_PROF2_ID,
                'lt_idx' => 0, 'status' => 'approved',
                'start' => '2025-12-23', 'end' => '2026-01-05',
                'reason' => 'Congé annuel de fin d\'année. Retour prévu le 6 janvier 2026.',
                'admin_note' => 'Validé. Bon congé.',
                'approved_by' => MultiSchoolSeeder::CSM_ADMIN_ID,
                'approved_at' => '2025-12-05 09:00:00',
            ],
            [
                'staff_user' => MultiSchoolSeeder::EPE_PROF1_ID,
                'lt_idx' => 6, 'status' => 'approved',
                'start' => '2025-10-20', 'end' => '2025-10-22',
                'reason' => 'Décès d\'un membre de la famille. Nécessité de se rendre à Parakou.',
                'admin_note' => 'Accordé avec nos condoléances.',
                'approved_by' => MultiSchoolSeeder::EPE_ADMIN_ID,
                'approved_at' => '2025-10-19 14:00:00',
            ],
            [
                'staff_user' => MultiSchoolSeeder::EPE_PROF2_ID,
                'lt_idx' => 2, 'status' => 'approved',
                'start' => '2026-04-01', 'end' => '2026-06-30',
                'reason' => 'Congé maternité. Date d\'accouchement prévisionnelle : 15 avril 2026.',
                'admin_note' => 'Accordé conformément à la législation en vigueur.',
                'approved_by' => MultiSchoolSeeder::EPE_ADMIN_ID,
                'approved_at' => '2026-03-01 10:00:00',
            ],
        ];

        $count = 0;
        foreach ($leaveDefs as $l) {
            $staff = $staffRows->get($l['staff_user']);
            if (!$staff) continue;
            $leaveType = $lt[$l['lt_idx']] ?? $lt->first();

            if (!DB::table('staff_leaves')->where('staff_id', $staff->id)->where('start_date', $l['start'])->exists()) {
                DB::table('staff_leaves')->insert([
                    'id'            => (string) Str::uuid(),
                    'staff_id'      => $staff->id,
                    'leave_type_id' => $leaveType->id,
                    'start_date'    => $l['start'],
                    'end_date'      => $l['end'],
                    'reason'        => $l['reason'],
                    'status'        => $l['status'],
                    'approved_by'   => $l['approved_by'],
                    'approved_at'   => $l['approved_at'],
                    'admin_note'    => $l['admin_note'],
                    'is_delete'     => 0,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                $count++;
            }
        }
        $this->command->info("    ✅ $count demandes de congés insérées.");
    }

    // ── Événements scolaires ──────────────────────────────────────────────────

    private function seedEvents(): void
    {
        $customTypes = DB::table('event_type_customs')->where('is_delete', 0)->get()->keyBy('name');

        $events = [
            // LMC
            [
                'school_id' => SchoolSeeder::LMC_ID, 'created_by' => MultiSchoolSeeder::LMC_ADMIN_ID,
                'title' => 'Réunion pédagogique 2ème trimestre',
                'description' => 'Bilan du 1er trimestre et planification du 2ème trimestre avec tous les enseignants.',
                'event_date' => '2026-01-08', 'start_time' => '08:00:00', 'end_time' => '12:00:00',
                'event_type' => 'academic', 'location' => 'Salle de conférence — LMC',
                'custom_type_name' => null,
            ],
            [
                'school_id' => SchoolSeeder::LMC_ID, 'created_by' => MultiSchoolSeeder::LMC_ADMIN_ID,
                'title' => 'Conseil de classe 1er trimestre — 3ème A',
                'description' => 'Conseil de classe du 1er trimestre pour la classe de 3ème A. Présence des délégués requise.',
                'event_date' => '2025-12-22', 'start_time' => '14:00:00', 'end_time' => '17:00:00',
                'event_type' => 'academic', 'location' => 'Salle B201 — LMC',
                'custom_type_name' => 'Conseil de classe',
            ],
            [
                'school_id' => SchoolSeeder::LMC_ID, 'created_by' => MultiSchoolSeeder::LMC_ADMIN_ID,
                'title' => 'Remise des bulletins 1er trimestre',
                'description' => 'Cérémonie de remise des bulletins du 1er trimestre en présence des parents d\'élèves.',
                'event_date' => '2025-12-23', 'start_time' => '08:00:00', 'end_time' => '12:00:00',
                'event_type' => 'ceremony', 'location' => 'Amphithéâtre — LMC',
                'custom_type_name' => null,
            ],
            [
                'school_id' => SchoolSeeder::LMC_ID, 'created_by' => MultiSchoolSeeder::LMC_ADMIN_ID,
                'title' => 'Sortie pédagogique au musée national',
                'description' => 'Visite culturelle du musée national de Cotonou pour les classes de 3ème. Guide prévu sur place.',
                'event_date' => '2026-02-20', 'start_time' => '07:30:00', 'end_time' => '17:00:00',
                'event_type' => 'trip', 'location' => 'Musée National de Cotonou',
                'custom_type_name' => null,
            ],
            // CSM
            [
                'school_id' => SchoolSeeder::CSM_ID, 'created_by' => MultiSchoolSeeder::CSM_ADMIN_ID,
                'title' => 'Réunion parents-professeurs 1er trimestre',
                'description' => 'Rencontre entre les parents d\'élèves et les professeurs pour discuter des résultats du 1er trimestre.',
                'event_date' => '2025-12-20', 'start_time' => '09:00:00', 'end_time' => '13:00:00',
                'event_type' => 'administrative', 'location' => 'Hall principal — CSM',
                'custom_type_name' => 'Réunion parents-profs',
            ],
            [
                'school_id' => SchoolSeeder::CSM_ID, 'created_by' => MultiSchoolSeeder::CSM_ADMIN_ID,
                'title' => 'Concours interne de mathématiques',
                'description' => 'Compétition académique inter-classes en mathématiques. Prix pour les 3 premiers de chaque niveau.',
                'event_date' => '2026-03-05', 'start_time' => '09:00:00', 'end_time' => '12:00:00',
                'event_type' => 'exam', 'location' => 'Salles de classe — CSM',
                'custom_type_name' => 'Concours interne',
            ],
            [
                'school_id' => SchoolSeeder::CSM_ID, 'created_by' => MultiSchoolSeeder::CSM_ADMIN_ID,
                'title' => 'Journée culturelle africaine',
                'description' => 'Journée dédiée à la culture africaine : expositions, danses traditionnelles et gastronomie locale.',
                'event_date' => '2026-02-28', 'start_time' => '08:00:00', 'end_time' => '18:00:00',
                'event_type' => 'cultural', 'location' => 'Cour principale — CSM',
                'custom_type_name' => null,
            ],
            // EPE
            [
                'school_id' => SchoolSeeder::EPE_ID, 'created_by' => MultiSchoolSeeder::EPE_ADMIN_ID,
                'title' => 'Fête de Noël des enfants',
                'description' => 'Célébration de Noël avec spectacle des élèves, distribution de cadeaux et goûter festif.',
                'event_date' => '2025-12-19', 'start_time' => '09:00:00', 'end_time' => '13:00:00',
                'event_type' => 'ceremony', 'location' => 'Cour de récréation — EPE',
                'custom_type_name' => 'Fête scolaire',
            ],
            [
                'school_id' => SchoolSeeder::EPE_ID, 'created_by' => MultiSchoolSeeder::EPE_ADMIN_ID,
                'title' => 'Remise des livrets scolaires 1er trimestre',
                'description' => 'Distribution des carnets de notes aux parents d\'élèves accompagnée d\'un bref entretien avec les maîtres.',
                'event_date' => '2025-12-22', 'start_time' => '07:30:00', 'end_time' => '11:30:00',
                'event_type' => 'ceremony', 'location' => 'Salle polyvalente — EPE',
                'custom_type_name' => null,
            ],
        ];

        $count = 0;
        foreach ($events as $e) {
            if (!DB::table('staff_events')->where('title', $e['title'])->where('school_id', $e['school_id'])->exists()) {
                $customTypeId = null;
                if ($e['custom_type_name'] && isset($customTypes[$e['custom_type_name']])) {
                    $customTypeId = $customTypes[$e['custom_type_name']]->id;
                }
                DB::table('staff_events')->insert([
                    'id'                  => (string) Str::uuid(),
                    'school_id'           => $e['school_id'],
                    'title'               => $e['title'],
                    'description'         => $e['description'],
                    'event_date'          => $e['event_date'],
                    'start_time'          => $e['start_time'],
                    'end_time'            => $e['end_time'],
                    'event_type'          => $e['event_type'],
                    'custom_event_type_id'=> $customTypeId,
                    'location'            => $e['location'],
                    'is_delete'           => 0,
                    'created_by'          => $e['created_by'],
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
                $count++;
            }
        }
        $this->command->info("    ✅ $count événements scolaires insérés.");
    }

    // ── Présences ─────────────────────────────────────────────────────────────

    private function seedAttendances(): void
    {
        $classes = [
            ['class_id' => MultiSchoolSeeder::LMC_CLASS1_ID, 'admin_id' => MultiSchoolSeeder::LMC_ADMIN_ID],
            ['class_id' => MultiSchoolSeeder::CSM_CLASS1_ID, 'admin_id' => MultiSchoolSeeder::CSM_ADMIN_ID],
            ['class_id' => MultiSchoolSeeder::EPE_CLASS1_ID, 'admin_id' => MultiSchoolSeeder::EPE_ADMIN_ID],
        ];

        // 10 jours de cours passés (lundi à vendredi)
        $workDays = [];
        $d = Carbon::today()->subDays(20);
        while (count($workDays) < 10 && $d->lt(Carbon::today())) {
            if ($d->isWeekday()) $workDays[] = $d->format('Y-m-d');
            $d->addDay();
        }

        $typesByPattern = ['present', 'present', 'present', 'present', 'present', 'present', 'late', 'late', 'absent', 'half_day'];

        $count = 0;
        foreach ($classes as $cls) {
            $students = DB::table('users')
                ->where('class_id', $cls['class_id'])
                ->where('user_type', 3)->where('is_delete', 0)
                ->pluck('id');

            foreach ($workDays as $di => $date) {
                foreach ($students as $si => $studentId) {
                    if (DB::table('attendances')->where('student_id', $studentId)->where('attendance_date', $date)->exists()) continue;
                    $type = $typesByPattern[($di + $si) % count($typesByPattern)];
                    DB::table('attendances')->insert([
                        'id'              => (string) Str::uuid(),
                        'class_id'        => $cls['class_id'],
                        'student_id'      => $studentId,
                        'attendance_date' => $date,
                        'attendance_type' => $type,
                        'is_delete'       => 0,
                        'created_by'      => $cls['admin_id'],
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                    $count++;
                }
            }
        }
        $this->command->info("    ✅ $count présences insérées.");
    }

    // ── Frais de scolarité ────────────────────────────────────────────────────

    private function seedFees(): void
    {
        $feeConfigs = [
            // LMC — 3ème A
            ['class_id' => MultiSchoolSeeder::LMC_CLASS1_ID, 'admin_id' => MultiSchoolSeeder::LMC_ADMIN_ID,
             'entries' => [
                ['student_email' => 'eleve1@lmc.bj', 'paid' => 45000, 'type' => 'cash',    'remark' => 'Paiement intégral en espèces — reçu n°LMC-001'],
                ['student_email' => 'eleve2@lmc.bj', 'paid' => 22500, 'type' => 'partial', 'remark' => 'Premier versement — solde dû avant mars 2026'],
                ['student_email' => 'eleve3@lmc.bj', 'paid' => 45000, 'type' => 'mobile',  'remark' => 'Paiement via KKiaPay — transaction KKP-20251015'],
             ]],
            // LMC — 2nde B
            ['class_id' => MultiSchoolSeeder::LMC_CLASS2_ID, 'admin_id' => MultiSchoolSeeder::LMC_ADMIN_ID,
             'entries' => [
                ['student_email' => 'eleve4@lmc.bj', 'paid' => 50000, 'type' => 'transfer', 'remark' => 'Virement bancaire — référence VIR-2025-LMC-004'],
                ['student_email' => 'eleve5@lmc.bj', 'paid' => 25000, 'type' => 'partial',  'remark' => 'Acompte versé. Reste 25 000 FCFA à régler.'],
                ['student_email' => 'eleve6@lmc.bj', 'paid' => 50000, 'type' => 'cash',     'remark' => 'Paiement comptant — reçu n°LMC-006'],
             ]],
            // CSM — 5ème C
            ['class_id' => MultiSchoolSeeder::CSM_CLASS1_ID, 'admin_id' => MultiSchoolSeeder::CSM_ADMIN_ID,
             'entries' => [
                ['student_email' => 'eleve1@csm.bj', 'paid' => 35000, 'type' => 'cash',   'remark' => 'Règlement complet — reçu n°CSM-001'],
                ['student_email' => 'eleve2@csm.bj', 'paid' => 17500, 'type' => 'mobile', 'remark' => 'Paiement partiel via FedaPay — ref FEDA-2025-002'],
                ['student_email' => 'eleve3@csm.bj', 'paid' => 35000, 'type' => 'cash',   'remark' => 'Paiement intégral — reçu n°CSM-003'],
             ]],
            // EPE
            ['class_id' => MultiSchoolSeeder::EPE_CLASS1_ID, 'admin_id' => MultiSchoolSeeder::EPE_ADMIN_ID,
             'entries' => [
                ['student_email' => 'eleve1@epe.bj', 'paid' => 20000, 'type' => 'cash',   'remark' => 'Paiement en espèces — reçu n°EPE-001'],
                ['student_email' => 'eleve2@epe.bj', 'paid' => 10000, 'type' => 'mobile', 'remark' => 'Versement partiel mobile money — reste 10 000 FCFA'],
             ]],
        ];

        $count = 0;
        foreach ($feeConfigs as $cfg) {
            $classRow = DB::table('class')->where('id', $cfg['class_id'])->first();
            $amount   = $classRow?->amount ?? 40000;

            foreach ($cfg['entries'] as $entry) {
                $student = DB::table('users')->where('email', $entry['student_email'])->first();
                if (!$student) continue;
                if (DB::table('feescollections')->where('student_id', $student->id)->where('class_id', $cfg['class_id'])->exists()) continue;

                $paid      = $entry['paid'];
                $remaining = $amount - $paid;
                $isPaid    = $remaining <= 0 ? 1 : 0;
                $status    = $isPaid ? 'Paid' : 'Pending';

                DB::table('feescollections')->insert([
                    'id'              => (string) Str::uuid(),
                    'class_id'        => $cfg['class_id'],
                    'student_id'      => $student->id,
                    'total_amount'    => $amount,
                    'paid_amount'     => $paid,
                    'remaning_amount' => max(0, $remaining),
                    'payment_type'    => $entry['type'],
                    'remark'          => $entry['remark'],
                    'payment_data'    => json_encode(['ref' => 'REF-' . strtoupper(Str::random(8)), 'date' => now()->toDateString(), 'method' => $entry['type']]),
                    'payment_status'  => $status,
                    'is_payment'      => $isPaid,
                    'is_delete'       => 0,
                    'created_by'      => $cfg['admin_id'],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
                $count++;
            }
        }
        $this->command->info("    ✅ $count enregistrements de frais insérés.");
    }

    // ── Devoirs (works) ───────────────────────────────────────────────────────

    private function seedWorks(): void
    {
        $workDefs = [
            [
                'class_id'  => MultiSchoolSeeder::LMC_CLASS1_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_MATHS_ID,
                'created_by'=> MultiSchoolSeeder::LMC_PROF1_ID,
                'work_date' => '2026-01-15', 'submission_date' => '2026-01-22',
                'description'=> 'Résoudre les exercices 1 à 10 du chapitre 4 (Fonctions). Présenter les calculs détaillés sur feuille double. Notation sur 20 points.',
            ],
            [
                'class_id'  => MultiSchoolSeeder::LMC_CLASS1_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_FRENCH_ID,
                'created_by'=> MultiSchoolSeeder::LMC_PROF2_ID,
                'work_date' => '2026-01-18', 'submission_date' => '2026-01-25',
                'description'=> 'Rédiger une dissertation de 3 pages sur le thème : "L\'éducation est-elle la clé du développement en Afrique ?" Plan et introduction obligatoires.',
            ],
            [
                'class_id'  => MultiSchoolSeeder::LMC_CLASS2_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_PHYS_ID,
                'created_by'=> MultiSchoolSeeder::LMC_PROF1_ID,
                'work_date' => '2026-01-20', 'submission_date' => '2026-01-27',
                'description'=> 'Compte-rendu du TP sur les circuits électriques. Inclure schéma, mesures et conclusions. Format A4, manuscrit accepté.',
            ],
            [
                'class_id'  => MultiSchoolSeeder::CSM_CLASS1_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_MATHS_ID,
                'created_by'=> MultiSchoolSeeder::CSM_PROF1_ID,
                'work_date' => '2026-01-14', 'submission_date' => '2026-01-21',
                'description'=> 'Exercices sur les fractions et les pourcentages (pages 45-50 du manuel). Tous les calculs doivent être montrés.',
            ],
            [
                'class_id'  => MultiSchoolSeeder::EPE_CLASS1_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_CALCUL_ID,
                'created_by'=> MultiSchoolSeeder::EPE_PROF1_ID,
                'work_date' => '2026-01-13', 'submission_date' => '2026-01-17',
                'description'=> 'Exercices de calcul mental : tables de multiplication de 1 à 10. Apprendre et réciter devant la classe le vendredi.',
            ],
        ];

        // UUIDs fixes pour référence dans seedHomework
        $this->workIds = [];
        $count = 0;
        foreach ($workDefs as $w) {
            $existing = DB::table('works')
                ->where('class_id', $w['class_id'])
                ->where('subject_id', $w['subject_id'])
                ->where('work_date', $w['work_date'])
                ->first();
            if ($existing) {
                $this->workIds[] = $existing->id;
                continue;
            }
            $workId = (string) Str::uuid();
            DB::table('works')->insert([
                'id'              => $workId,
                'class_id'        => $w['class_id'],
                'subject_id'      => $w['subject_id'],
                'work_date'       => $w['work_date'],
                'submission_date' => $w['submission_date'],
                'document_file'   => 'devoir_' . Str::slug(substr($w['description'], 0, 30)) . '.pdf',
                'description'     => $w['description'],
                'is_delete'       => 0,
                'deleted_at'      => null,
                'deleted_by'      => null,
                'created_by'      => $w['created_by'],
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
            $this->workIds[] = $workId;

            // Work attachments
            DB::table('work_attachments')->insert([
                'id'        => (string) Str::uuid(),
                'work_id'   => $workId,
                'file_name' => 'Consignes_devoir_' . substr(md5($workId), 0, 6) . '.pdf',
                'file_path' => 'uploads/works/' . $workId . '/consignes.pdf',
                'file_ext'  => 'pdf',
                'file_size' => rand(50000, 500000),
                'is_delete' => 0,
                'created_at'=> now(),
                'updated_at'=> now(),
            ]);
            $count++;
        }
        $this->command->info("    ✅ $count devoirs insérés (+ pièces jointes).");
    }

    private array $workIds = [];

    // ── Soumissions de devoirs (homework) ─────────────────────────────────────

    private function seedHomework(): void
    {
        if (empty($this->workIds)) {
            $this->workIds = DB::table('works')->where('is_delete', 0)->pluck('id')->toArray();
        }

        $statuses = ['submitted', 'done', 'processed', 'submitted', 'resolved'];
        $count = 0;

        foreach ($this->workIds as $wi => $workId) {
            $work = DB::table('works')->where('id', $workId)->first();
            if (!$work) continue;

            $students = DB::table('users')
                ->where('class_id', $work->class_id)
                ->where('user_type', 3)->where('is_delete', 0)
                ->pluck('id');

            foreach ($students as $si => $studentId) {
                if (DB::table('homework')->where('work_id', $workId)->where('student_id', $studentId)->exists()) continue;
                $status = $statuses[($wi + $si) % count($statuses)];
                DB::table('homework')->insert([
                    'id'           => (string) Str::uuid(),
                    'work_id'      => $workId,
                    'student_id'   => $studentId,
                    'document_file'=> 'rendu_devoir_' . substr(md5($studentId . $workId), 0, 8) . '.pdf',
                    'description'  => 'Devoir rendu dans les délais impartis. Travail soigné et complet.',
                    'status'       => $status,
                    'is_delete'    => 0,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
                $count++;
            }
        }
        $this->command->info("    ✅ $count soumissions de devoirs insérées.");
    }

    // ── Tableau d'affichage (communicates) ───────────────────────────────────

    private function seedNoticeboard(): void
    {
        $notices = [
            [
                'school_id' => SchoolSeeder::LMC_ID,
                'created_by'=> MultiSchoolSeeder::LMC_ADMIN_ID,
                'title'     => 'Réunion pédagogique du 2ème trimestre',
                'message'   => '<p>Nous informons tous les <strong>enseignants</strong> que la réunion pédagogique du 2ème trimestre aura lieu le <strong>jeudi 8 janvier 2026 à 08h00</strong> en salle de conférence. La présence est <em>obligatoire</em>. Un ordre du jour sera distribué la veille.</p>',
                'notice_date' => '2026-01-05', 'publish_date' => '2026-01-05',
                'targets' => [1, 2],
            ],
            [
                'school_id' => SchoolSeeder::LMC_ID,
                'created_by'=> MultiSchoolSeeder::LMC_ADMIN_ID,
                'title'     => 'Calendrier des examens 2ème trimestre',
                'message'   => '<p>Le calendrier des évaluations du 2ème trimestre est désormais disponible. Les <strong>apprenants</strong> sont invités à le consulter et à se préparer en conséquence.</p><ul><li>Semaine 1 (20-24 jan) : Mathématiques, Sciences</li><li>Semaine 2 (27-31 jan) : Français, Anglais, Histoire</li><li>Semaine 3 (3-7 fév) : Examens blancs toutes matières</li></ul>',
                'notice_date' => '2026-01-10', 'publish_date' => '2026-01-10',
                'targets' => [2, 3, 4],
            ],
            [
                'school_id' => SchoolSeeder::LMC_ID,
                'created_by'=> MultiSchoolSeeder::LMC_ADMIN_ID,
                'title'     => 'Rappel paiement frais de scolarité',
                'message'   => '<p>Nous rappelons aux <strong>familles</strong> que le 2ème versement des frais de scolarité est attendu <strong>avant le 31 janvier 2026</strong>. Tout retard entraînera une pénalité de 5%.</p><p>Modes de paiement acceptés : espèces, virement bancaire, KKiaPay, FedaPay.</p>',
                'notice_date' => '2026-01-12', 'publish_date' => '2026-01-12',
                'targets' => [4],
            ],
            [
                'school_id' => SchoolSeeder::CSM_ID,
                'created_by'=> MultiSchoolSeeder::CSM_ADMIN_ID,
                'title'     => 'Concours de mathématiques inter-classes',
                'message'   => '<p>Le Collège Saint-Michel organise son <strong>concours annuel de mathématiques</strong> le <strong>5 mars 2026</strong>. Tous les apprenants des classes de 4ème et 5ème sont encouragés à participer.</p><p>Inscriptions auprès du professeur principal avant le 20 février 2026. <strong>Prix à gagner !</strong></p>',
                'notice_date' => '2026-02-01', 'publish_date' => '2026-02-01',
                'targets' => [2, 3, 4],
            ],
            [
                'school_id' => SchoolSeeder::EPE_ID,
                'created_by'=> MultiSchoolSeeder::EPE_ADMIN_ID,
                'title'     => 'Préparation de la fête de fin d\'année',
                'message'   => '<p>L\'École Primaire Les Étoiles prépare sa <strong>grande fête de fin d\'année</strong> prévue en juin 2026. Nous sollicitons la participation des <strong>parents</strong> pour la logistique et les décorations.</p><p>Réunion de coordination le samedi 15 mars 2026 à 10h00 dans la cour de l\'école.</p>',
                'notice_date' => '2026-03-01', 'publish_date' => '2026-03-01',
                'targets' => [1, 2, 3, 4],
            ],
        ];

        $count = 0;
        foreach ($notices as $n) {
            if (DB::table('communicates')->where('title', $n['title'])->where('school_id', $n['school_id'])->exists()) continue;

            $comId = (string) Str::uuid();
            DB::table('communicates')->insert([
                'id'             => $comId,
                'school_id'      => $n['school_id'],
                'title'          => $n['title'],
                'message'        => $n['message'],
                'notice_date'    => $n['notice_date'],
                'publish_date'   => $n['publish_date'],
                'is_active'      => 1,
                'is_delete'      => 0,
                'deleted_at'     => null,
                'deleted_reason' => null,
                'email_sent_at'  => Carbon::now()->subHours(2)->toDateTimeString(),
                'created_by'     => $n['created_by'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            foreach ($n['targets'] as $userType) {
                DB::table('noticeboard_messages')->insert([
                    'id'              => (string) Str::uuid(),
                    'communicates_id' => $comId,
                    'message_to'      => $userType,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
            $count++;
        }
        $this->command->info("    ✅ $count annonces noticeboard insérées.");
    }

    // ── Chats ─────────────────────────────────────────────────────────────────

    private function seedChats(): void
    {
        $conversations = [
            [
                'sender'   => MultiSchoolSeeder::LMC_ADMIN_ID,
                'receiver' => MultiSchoolSeeder::LMC_PROF1_ID,
                'messages' => [
                    ['from' => 'admin', 'msg' => 'Bonjour M. ADJAÏ, avez-vous terminé la saisie des notes du 1er trimestre pour la 3ème A ?', 'status' => 1],
                    ['from' => 'prof',  'msg' => 'Bonjour M. KOFFI. Oui, toutes les notes sont saisies et validées. Le bulletin peut être généré.', 'status' => 1],
                    ['from' => 'admin', 'msg' => 'Parfait, merci ! Je génère les bulletins cet après-midi.', 'status' => 0],
                ],
            ],
            [
                'sender'   => MultiSchoolSeeder::LMC_ADMIN_ID,
                'receiver' => MultiSchoolSeeder::LMC_PROF2_ID,
                'messages' => [
                    ['from' => 'admin', 'msg' => 'Mme SOGLO, rappel : la réunion pédagogique est jeudi 8 janvier à 8h.', 'status' => 1],
                    ['from' => 'prof',  'msg' => 'Bien noté, je serai présente. Faut-il préparer un compte-rendu de la période ?', 'status' => 1],
                    ['from' => 'admin', 'msg' => 'Oui, un bref bilan de vos classes. 10 minutes de présentation maximum.', 'status' => 0],
                ],
            ],
            [
                'sender'   => MultiSchoolSeeder::CSM_ADMIN_ID,
                'receiver' => MultiSchoolSeeder::CSM_PROF1_ID,
                'messages' => [
                    ['from' => 'admin', 'msg' => 'Bonjour M. TOSSOU, avez-vous les résultats du concours interne de maths ?', 'status' => 1],
                    ['from' => 'prof',  'msg' => 'Oui ! Le classement est prêt. Le premier est VODOUNOU avec 18/20, suivi de KAKPO avec 16.5.', 'status' => 0],
                ],
            ],
        ];

        $count = 0;
        $baseTime = Carbon::now()->subDays(3);

        foreach ($conversations as $conv) {
            $adminId = $conv['sender'];
            $profId  = $conv['receiver'];

            foreach ($conv['messages'] as $mi => $m) {
                $sender   = $m['from'] === 'admin' ? $adminId : $profId;
                $receiver = $m['from'] === 'admin' ? $profId  : $adminId;
                DB::table('chats')->insert([
                    'id'           => (string) Str::uuid(),
                    'sender_id'    => $sender,
                    'receiver_id'  => $receiver,
                    'message'      => $m['msg'],
                    'file'         => '',
                    'created_date' => $baseTime->copy()->addMinutes($mi * 5)->toDateTimeString(),
                    'status'       => $m['status'],
                    'is_delete'    => 0,
                    'created_at'   => $baseTime->copy()->addMinutes($mi * 5)->toDateTimeString(),
                    'updated_at'   => $baseTime->copy()->addMinutes($mi * 5)->toDateTimeString(),
                ]);
                $count++;
            }
        }
        $this->command->info("    ✅ $count messages de chat insérés.");
    }

    // ── Notifications in-app ──────────────────────────────────────────────────

    private function seedNotifications(): void
    {
        $notifs = [
            [
                'user_id'   => MultiSchoolSeeder::LMC_ADMIN_ID,
                'user_type' => 'App\Models\User',
                'type'      => 'App\Notifications\NewHomeworkSubmission',
                'data'      => json_encode(['title' => 'Nouveau devoir rendu', 'message' => 'HOUNSOU Michel a rendu son devoir de Mathématiques.', 'icon' => 'book', 'url' => '/admin/practicalworks/homework/list']),
                'read_at'   => null,
            ],
            [
                'user_id'   => MultiSchoolSeeder::LMC_ADMIN_ID,
                'user_type' => 'App\Models\User',
                'type'      => 'App\Notifications\FeePayment',
                'data'      => json_encode(['title' => 'Paiement reçu', 'message' => 'DOSSOU Patricia a réglé 22 500 FCFA de frais de scolarité.', 'icon' => 'wallet', 'url' => '/admin/feescollections/collections/list']),
                'read_at'   => Carbon::now()->subHour()->toDateTimeString(),
            ],
            [
                'user_id'   => MultiSchoolSeeder::LMC_PROF1_ID,
                'user_type' => 'App\Models\User',
                'type'      => 'App\Notifications\GradeValidated',
                'data'      => json_encode(['title' => 'Notes validées', 'message' => 'Les notes du DS Maths 3ème A ont été validées par l\'administration.', 'icon' => 'check-circle', 'url' => '/teacher/evaluations']),
                'read_at'   => null,
            ],
            [
                'user_id'   => MultiSchoolSeeder::CSM_ADMIN_ID,
                'user_type' => 'App\Models\User',
                'type'      => 'App\Notifications\NewLeaveRequest',
                'data'      => json_encode(['title' => 'Demande de congé', 'message' => 'TOSSOU Pascal a soumis une demande de congé exceptionnel.', 'icon' => 'calendar', 'url' => '/admin/staff/leaves/list']),
                'read_at'   => null,
            ],
            [
                'user_id'   => MultiSchoolSeeder::EPE_ADMIN_ID,
                'user_type' => 'App\Models\User',
                'type'      => 'App\Notifications\BulletinGenerated',
                'data'      => json_encode(['title' => 'Bulletins générés', 'message' => 'Les bulletins du 1er trimestre pour la classe CE2 ont été générés avec succès.', 'icon' => 'file-text', 'url' => '/admin/bulletins/list']),
                'read_at'   => Carbon::now()->subMinutes(30)->toDateTimeString(),
            ],
        ];

        $count = 0;
        foreach ($notifs as $n) {
            DB::table('notifications')->insert([
                'id'              => (string) Str::uuid(),
                'type'            => $n['type'],
                'notifiable_type' => $n['user_type'],
                'notifiable_id'   => $n['user_id'],
                'data'            => $n['data'],
                'read_at'         => $n['read_at'],
                'created_at'      => Carbon::now()->subMinutes(rand(10, 120))->toDateTimeString(),
                'updated_at'      => now(),
            ]);
            $count++;
        }
        $this->command->info("    ✅ $count notifications insérées.");
    }
}
