<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * MultiSchoolSeeder — Crée les utilisateurs pour les 3 écoles de démo.
 *
 * Par école : 1 admin, 2 profs, 4 élèves, 2 parents, classes et matières.
 *
 * Dépendances :
 *   - SchoolSeeder (les 3 écoles doivent exister)
 *   - RolesAndPermissionsSeeder (rôles admin/teacher/student/parent doivent exister)
 *
 * ─────────────────────────────────────────────────────
 * Lycée Moderne de Cotonou   → admin@lmc.bj / Admin@LMC2025
 * Collège Saint-Michel       → admin@csm.bj / Admin@CSM2025
 * École Primaire Les Étoiles → admin@epe.bj / Admin@EPE2025
 * Professeurs : Prof@1234  |  Élèves : Eleve@1234  |  Parents : Parent@1234
 * ─────────────────────────────────────────────────────
 */
class MultiSchoolSeeder extends Seeder
{
    // Permissions accordées à chaque admin d'école
    private const ADMIN_PERMISSIONS = [
        'view.dashboard.admin',
        'view.users.all', 'view.users.admins', 'view.users.teachers',
        'view.users.students', 'view.users.parents',
        'action.users.edit', 'action.users.delete', 'action.users.reset_password',
        'action.admins.view', 'action.admins.create', 'action.admins.edit',
        'action.admins.delete', 'action.admins.reset_password', 'action.admins.export',
        'action.teachers.view', 'action.teachers.create', 'action.teachers.edit',
        'action.teachers.delete', 'action.teachers.reset_password', 'action.teachers.export',
        'action.students.view', 'action.students.create', 'action.students.edit',
        'action.students.delete', 'action.students.reset_password', 'action.students.export',
        'action.parents.view', 'action.parents.create', 'action.parents.edit',
        'action.parents.delete', 'action.parents.reset_password', 'action.parents.export',
        'action.parents.manage_children',
        'view.academics.classes', 'view.academics.subjects',
        'view.academics.assign_subjects', 'view.academics.assign_classes', 'view.academics.timetable',
        'action.classes.create', 'action.classes.edit', 'action.classes.delete',
        'action.subjects.create', 'action.subjects.edit', 'action.subjects.delete',
        'view.exams.periods', 'view.exams.list', 'view.exams.marks',
        'action.exams.create', 'action.exams.edit', 'action.exams.delete',
        'action.marks.manage',
        'view.bulletins.list', 'action.bulletins.generate', 'action.bulletins.publish',
        'view.attendance.manage', 'view.attendance.report', 'action.attendance.save',
        'view.homework.list', 'view.homework.reports',
        'action.homework.create', 'action.homework.edit', 'action.homework.delete',
        'view.fees.collect', 'view.fees.reports', 'action.fees.collect', 'action.fees.delete',
        'view.communicate.noticeboard', 'view.communicate.mail',
        'action.noticeboard.manage', 'action.mail.send',
        'view.staff.list', 'view.staff.leaves', 'view.staff.events',
        'action.staff.create', 'action.staff.edit', 'action.staff.delete',
        'action.staff.leaves', 'action.staff.events',
        'view.settings', 'action.settings.manage',
        'chat.access',
    ];

    private array $schoolsData = [
        [
            'school_code' => 'lmc-cotonou',
            'admin'       => [
                'name' => 'KOFFI', 'last_name' => 'Arnaud',
                'email' => 'admin@lmc.bj', 'password' => 'Admin@LMC2025',
            ],
            'classes'  => [
                ['name' => '3ème A', 'amount' => 45000],
                ['name' => '2nde B', 'amount' => 50000],
            ],
            'subjects' => ['Mathématiques', 'Physique-Chimie', 'SVT', 'Français', 'Histoire-Géo', 'Anglais'],
            'teachers' => [
                ['name' => 'ADJAÏ',  'last_name' => 'Rodrigue', 'email' => 'prof1@lmc.bj'],
                ['name' => 'SOGLO',  'last_name' => 'Marlène',  'email' => 'prof2@lmc.bj'],
            ],
            'students' => [
                ['name' => 'HOUNSOU', 'last_name' => 'Michel',   'email' => 'eleve1@lmc.bj', 'class_idx' => 0],
                ['name' => 'DOSSOU',  'last_name' => 'Patricia', 'email' => 'eleve2@lmc.bj', 'class_idx' => 0],
                ['name' => 'AZONNOU', 'last_name' => 'François', 'email' => 'eleve3@lmc.bj', 'class_idx' => 1],
                ['name' => 'AHOUA',   'last_name' => 'Estelle',  'email' => 'eleve4@lmc.bj', 'class_idx' => 1],
            ],
            'parents' => [
                ['name' => 'HOUNSOU', 'last_name' => 'Jean',  'email' => 'parent1@lmc.bj', 'student_idx' => 0],
                ['name' => 'DOSSOU',  'last_name' => 'Marie', 'email' => 'parent2@lmc.bj', 'student_idx' => 1],
            ],
        ],
        [
            'school_code' => 'csm-abomey',
            'admin'       => [
                'name' => 'AGBOSSOU', 'last_name' => 'Christine',
                'email' => 'admin@csm.bj', 'password' => 'Admin@CSM2025',
            ],
            'classes'  => [
                ['name' => '5ème C', 'amount' => 35000],
                ['name' => '4ème D', 'amount' => 38000],
            ],
            'subjects' => ['Mathématiques', 'Français', 'Anglais', 'Sciences', 'Éducation Civique', 'EPS'],
            'teachers' => [
                ['name' => 'TOSSOU',   'last_name' => 'Pascal',  'email' => 'prof1@csm.bj'],
                ['name' => 'GBAGUIDI', 'last_name' => 'Noëlle',  'email' => 'prof2@csm.bj'],
            ],
            'students' => [
                ['name' => 'VODOUNOU',   'last_name' => 'Clément', 'email' => 'eleve1@csm.bj', 'class_idx' => 0],
                ['name' => 'AHOTON',     'last_name' => 'Sandra',  'email' => 'eleve2@csm.bj', 'class_idx' => 0],
                ['name' => 'MISSINHOUN', 'last_name' => 'Kevin',   'email' => 'eleve3@csm.bj', 'class_idx' => 1],
                ['name' => 'AKAKPO',     'last_name' => 'Vanessa', 'email' => 'eleve4@csm.bj', 'class_idx' => 1],
            ],
            'parents' => [
                ['name' => 'VODOUNOU', 'last_name' => 'Thomas',   'email' => 'parent1@csm.bj', 'student_idx' => 0],
                ['name' => 'AHOTON',   'last_name' => 'Béatrice', 'email' => 'parent2@csm.bj', 'student_idx' => 1],
            ],
        ],
        [
            'school_code' => 'epe-parakou',
            'admin'       => [
                'name' => 'PARAISO', 'last_name' => 'Théodore',
                'email' => 'admin@epe.bj', 'password' => 'Admin@EPE2025',
            ],
            'classes'  => [
                ['name' => 'CE2', 'amount' => 20000],
                ['name' => 'CM1', 'amount' => 22000],
            ],
            'subjects' => ['Calcul', 'Lecture', 'Écriture', 'Éveil', 'Dessin', 'Chant'],
            'teachers' => [
                ['name' => 'BIAO',   'last_name' => 'Alphonse', 'email' => 'prof1@epe.bj'],
                ['name' => 'ZANNOU', 'last_name' => 'Cécile',   'email' => 'prof2@epe.bj'],
            ],
            'students' => [
                ['name' => 'OROU',  'last_name' => 'Ibrahim',  'email' => 'eleve1@epe.bj', 'class_idx' => 0],
                ['name' => 'SABI',  'last_name' => 'Fatima',   'email' => 'eleve2@epe.bj', 'class_idx' => 0],
                ['name' => 'ALABI', 'last_name' => 'Moussa',   'email' => 'eleve3@epe.bj', 'class_idx' => 1],
                ['name' => 'GUIWA', 'last_name' => 'Aïssatou', 'email' => 'eleve4@epe.bj', 'class_idx' => 1],
            ],
            'parents' => [
                ['name' => 'OROU', 'last_name' => 'Mounirou',  'email' => 'parent1@epe.bj', 'student_idx' => 0],
                ['name' => 'SABI', 'last_name' => 'Raïnatou',  'email' => 'parent2@epe.bj', 'student_idx' => 1],
            ],
        ],
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Récupérer les rôles (créés par RolesAndPermissionsSeeder)
        $adminRole   = Role::firstOrCreate(['name' => 'admin',   'guard_name' => 'web'], ['user_type' => 1]);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web'], ['user_type' => 2]);
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web'], ['user_type' => 3]);
        $parentRole  = Role::firstOrCreate(['name' => 'parent',  'guard_name' => 'web'], ['user_type' => 4]);

        foreach ($this->schoolsData as $data) {
            // ── Récupérer l'école ─────────────────────────────────────────────
            $school = DB::table('schools')->where('school_code', $data['school_code'])->first();
            if (! $school) {
                $this->command->error("  ✗ École introuvable : {$data['school_code']} — lancez SchoolSeeder d'abord.");
                continue;
            }

            $this->command->info("\n  🏫 {$school->school_name}");

            // ── Admin de l'école ──────────────────────────────────────────────
            $admin = User::firstOrCreate(
                ['email' => $data['admin']['email']],
                [
                    'name'      => $data['admin']['name'],
                    'last_name' => $data['admin']['last_name'],
                    'password'  => Hash::make($data['admin']['password']),
                    'user_type' => 1,
                    'status'    => 1,
                    'is_delete' => 0,
                    'school_id' => $school->id,
                ]
            );
            $admin->school_id = $school->id;
            $admin->save();

            if (! $admin->hasRole('admin')) {
                $admin->assignRole('admin');
            }

            // Donner les permissions admin directement sur l'utilisateur
            $adminPermsToGive = Permission::where('guard_name', 'web')
                ->whereIn('name', self::ADMIN_PERMISSIONS)
                ->get();
            $admin->syncPermissions($adminPermsToGive);

            $this->command->info("    ✅ Admin : {$data['admin']['email']} / {$data['admin']['password']}");

            // ── Classes ───────────────────────────────────────────────────────
            $classIds = [];
            foreach ($data['classes'] as $classData) {
                $existing = DB::table('class')
                    ->where('name', $classData['name'])
                    ->where('school_id', $school->id)
                    ->first();

                if (! $existing) {
                    $classIds[] = DB::table('class')->insertGetId([
                        'school_id'  => $school->id,
                        'name'       => $classData['name'],
                        'amount'     => $classData['amount'],
                        'status'     => 1,
                        'is_delete'  => 0,
                        'created_by' => $admin->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $classIds[] = $existing->id;
                }
            }
            $this->command->info("    ✅ " . count($classIds) . " classes créées");

            // ── Matières ──────────────────────────────────────────────────────
            $subjectIds = [];
            foreach ($data['subjects'] as $subjectName) {
                $existing = DB::table('subject')->where('name', $subjectName)->first();
                if (! $existing) {
                    $subjectIds[] = DB::table('subject')->insertGetId([
                        'name'       => $subjectName,
                        'type'       => 'general',
                        'status'     => 1,
                        'is_delete'  => 0,
                        'created_by' => $admin->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $subjectIds[] = $existing->id;
                }
            }
            $this->command->info("    ✅ " . count($subjectIds) . " matières créées");

            // ── Matières ↔ Classes (class_subject) ───────────────────────────
            foreach ($classIds as $classId) {
                foreach ($subjectIds as $idx => $subjectId) {
                    $exists = DB::table('class_subject')
                        ->where('class_id', $classId)
                        ->where('subject_id', $subjectId)
                        ->exists();
                    if (! $exists) {
                        DB::table('class_subject')->insert([
                            'class_id'   => $classId,
                            'subject_id' => $subjectId,
                            'coefficient'=> rand(1, 4),
                            'status'     => 1,
                            'is_delete'  => 0,
                            'created_by' => $admin->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // ── Professeurs ───────────────────────────────────────────────────
            $teacherIds = [];
            foreach ($data['teachers'] as $idx => $teacherData) {
                $teacher = User::firstOrCreate(
                    ['email' => $teacherData['email']],
                    [
                        'name'      => $teacherData['name'],
                        'last_name' => $teacherData['last_name'],
                        'password'  => Hash::make('Prof@1234'),
                        'user_type' => 2,
                        'status'    => 1,
                        'is_delete' => 0,
                        'school_id' => $school->id,
                    ]
                );
                $teacher->school_id = $school->id;
                $teacher->save();

                if (! $teacher->hasRole('teacher')) {
                    $teacher->assignRole('teacher');
                }

                // Assigner à la classe correspondante
                if (isset($classIds[$idx])) {
                    $exists = DB::table('class_teacher')
                        ->where('class_id', $classIds[$idx])
                        ->where('teacher_id', $teacher->id)
                        ->exists();
                    if (! $exists) {
                        DB::table('class_teacher')->insert([
                            'class_id'   => $classIds[$idx],
                            'teacher_id' => $teacher->id,
                            'status'     => 1,
                            'is_delete'  => 0,
                            'created_by' => $admin->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                $teacherIds[] = $teacher->id;
            }
            $this->command->info("    ✅ " . count($teacherIds) . " professeurs (mot de passe : Prof@1234)");

            // ── Élèves ────────────────────────────────────────────────────────
            $studentIds = [];
            $schoolPrefix = strtoupper(substr($data['school_code'], 0, 3));
            foreach ($data['students'] as $sData) {
                $classId = $classIds[$sData['class_idx']] ?? $classIds[0];
                $admNum  = $schoolPrefix . '-' . date('Y') . '-' . str_pad(count($studentIds) + 1, 4, '0', STR_PAD_LEFT);

                $student = User::firstOrCreate(
                    ['email' => $sData['email']],
                    [
                        'name'             => $sData['name'],
                        'last_name'        => $sData['last_name'],
                        'password'         => Hash::make('Eleve@1234'),
                        'user_type'        => 3,
                        'status'           => 1,
                        'is_delete'        => 0,
                        'school_id'        => $school->id,
                        'class_id'         => $classId,
                        'admission_number' => $admNum,
                    ]
                );
                $student->school_id = $school->id;
                $student->class_id  = $classId;
                $student->save();

                if (! $student->hasRole('student')) {
                    $student->assignRole('student');
                }

                $studentIds[] = $student->id;
            }
            $this->command->info("    ✅ " . count($studentIds) . " élèves (mot de passe : Eleve@1234)");

            // ── Parents ───────────────────────────────────────────────────────
            foreach ($data['parents'] as $pData) {
                $parent = User::firstOrCreate(
                    ['email' => $pData['email']],
                    [
                        'name'      => $pData['name'],
                        'last_name' => $pData['last_name'],
                        'password'  => Hash::make('Parent@1234'),
                        'user_type' => 4,
                        'status'    => 1,
                        'is_delete' => 0,
                        'school_id' => $school->id,
                    ]
                );
                $parent->school_id = $school->id;
                $parent->save();

                if (! $parent->hasRole('parent')) {
                    $parent->assignRole('parent');
                }

                // Lier l'élève à ce parent
                if (isset($studentIds[$pData['student_idx']])) {
                    User::where('id', $studentIds[$pData['student_idx']])
                        ->update(['parent_id' => $parent->id]);
                }
            }
            $this->command->info("    ✅ " . count($data['parents']) . " parents (mot de passe : Parent@1234)");
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->newLine();
        $this->command->line('══════════════════════════════════════════════════');
        $this->command->table(
            ['École', 'Admin', 'Mot de passe'],
            [
                ['Lycée Moderne de Cotonou',    'admin@lmc.bj', 'Admin@LMC2025'],
                ['Collège Saint-Michel',         'admin@csm.bj', 'Admin@CSM2025'],
                ['École Primaire Les Étoiles',   'admin@epe.bj', 'Admin@EPE2025'],
            ]
        );
        $this->command->line('Profs : Prof@1234  |  Élèves : Eleve@1234  |  Parents : Parent@1234');
        $this->command->line('══════════════════════════════════════════════════');
    }
}
