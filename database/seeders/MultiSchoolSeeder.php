<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * MultiSchoolSeeder
 * ─────────────────────────────────────────────────────────────────────────────
 * Crée 3 écoles avec chacune :
 *   - 1 admin      (gère toute l'école)
 *   - 2 professeurs
 *   - 4 apprenants (2 par classe)
 *   - 2 parents    (1 par apprenant pair)
 *
 * Chaque école a ses propres classes et matières.
 *
 * Connexions (format: email / mot de passe) :
 *   Lycée Moderne de Cotonou
 *     admin@lmc.bj / Admin@LMC2025
 *     prof1@lmc.bj / Prof@1234
 *   Collège Saint-Michel
 *     admin@csm.bj / Admin@CSM2025
 *     prof1@csm.bj / Prof@1234
 *   École Primaire Les Étoiles
 *     admin@epe.bj / Admin@EPE2025
 *     prof1@epe.bj / Prof@1234
 */
class MultiSchoolSeeder extends Seeder
{
    // ── Permissions à donner aux admins d'école ────────────────────────────
    private const ADMIN_PERMISSIONS = [
        // Dashboard
        'view.dashboard.admin',
        // Gestion utilisateurs de l'école
        'view.users.all',
        'view.users.admins', 'view.users.teachers', 'view.users.students', 'view.users.parents',
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
        // Académique
        'view.academics.classes', 'view.academics.subjects',
        'view.academics.assign_subjects', 'view.academics.assign_classes', 'view.academics.timetable',
        'action.classes.create', 'action.classes.edit', 'action.classes.delete',
        'action.subjects.create', 'action.subjects.edit', 'action.subjects.delete',
        // Examens & notes
        'view.exams.periods', 'view.exams.list', 'view.exams.marks',
        'action.exams.create', 'action.exams.edit', 'action.exams.delete',
        'action.marks.manage',
        // Bulletins
        'view.bulletins.list',
        'action.bulletins.generate', 'action.bulletins.publish',
        // Présences
        'view.attendance.manage', 'view.attendance.report',
        'action.attendance.save',
        // Devoirs
        'view.homework.list', 'view.homework.reports',
        'action.homework.create', 'action.homework.edit', 'action.homework.delete',
        // Contributions
        'view.fees.collect', 'view.fees.reports',
        'action.fees.collect', 'action.fees.delete',
        // Communication
        'view.communicate.noticeboard', 'view.communicate.mail',
        'action.noticeboard.manage', 'action.mail.send',
        // Personnel
        'view.staff.list', 'view.staff.leaves', 'view.staff.events',
        'action.staff.create', 'action.staff.edit', 'action.staff.delete',
        'action.staff.leaves', 'action.staff.events',
        // Paramètres de l'école
        'view.settings', 'action.settings.manage',
        // Chat
        'chat.access',
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── S'assurer que les permissions existent ─────────────────────────
        foreach (self::ADMIN_PERMISSIONS as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        // Aussi action.users.reset_password (ajoutée par migration)
        Permission::firstOrCreate(['name' => 'action.users.reset_password', 'guard_name' => 'web']);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Récupérer les rôles ────────────────────────────────────────────
        $adminRole   = Role::firstOrCreate(['name' => 'admin',   'guard_name' => 'web'], ['user_type' => 1]);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web'], ['user_type' => 2]);
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web'], ['user_type' => 3]);
        $parentRole  = Role::firstOrCreate(['name' => 'parent',  'guard_name' => 'web'], ['user_type' => 4]);

        // ── Définition des 3 écoles ────────────────────────────────────────
        $schoolsData = [
            [
                'school' => [
                    'school_name' => 'Lycée Moderne de Cotonou',
                    'school_type' => 'Lycée',
                    'school_code' => 'lmc-cotonou',
                    'address'     => 'Avenue Jean-Paul II, Cotonou, Bénin',
                    'phone'       => '0022997000001',
                    'email'       => 'contact@lmc.bj',
                    'uai_number'  => 'BJ-LMC-001',
                    'status'      => 1,
                ],
                'admin' => [
                    'name'      => 'KOFFI',
                    'last_name' => 'Arnaud',
                    'email'     => 'admin@lmc.bj',
                    'password'  => 'Admin@LMC2025',
                ],
                'classes' => [
                    ['name' => '3ème A', 'amount' => 45000],
                    ['name' => '2nde B', 'amount' => 50000],
                ],
                'subjects' => ['Mathématiques', 'Physique-Chimie', 'SVT', 'Français', 'Histoire-Géo', 'Anglais'],
                'teachers' => [
                    ['name' => 'ADJAÏ',   'last_name' => 'Rodrigue', 'email' => 'prof1@lmc.bj'],
                    ['name' => 'SOGLO',   'last_name' => 'Marlène',  'email' => 'prof2@lmc.bj'],
                ],
                'students' => [
                    ['name' => 'HOUNSOU', 'last_name' => 'Michel',  'email' => 'eleve1@lmc.bj', 'class_idx' => 0],
                    ['name' => 'DOSSOU',  'last_name' => 'Patricia','email' => 'eleve2@lmc.bj', 'class_idx' => 0],
                    ['name' => 'AZONNOU', 'last_name' => 'François', 'email' => 'eleve3@lmc.bj', 'class_idx' => 1],
                    ['name' => 'AHOUA',   'last_name' => 'Estelle',  'email' => 'eleve4@lmc.bj', 'class_idx' => 1],
                ],
                'parents' => [
                    ['name' => 'HOUNSOU', 'last_name' => 'Jean',    'email' => 'parent1@lmc.bj', 'student_idx' => 0],
                    ['name' => 'DOSSOU',  'last_name' => 'Marie',   'email' => 'parent2@lmc.bj', 'student_idx' => 1],
                ],
            ],
            [
                'school' => [
                    'school_name' => 'Collège Saint-Michel',
                    'school_type' => 'Collège',
                    'school_code' => 'csm-abomey',
                    'address'     => 'Rue du Commerce, Abomey-Calavi, Bénin',
                    'phone'       => '0022997000002',
                    'email'       => 'contact@csm.bj',
                    'uai_number'  => 'BJ-CSM-002',
                    'status'      => 1,
                ],
                'admin' => [
                    'name'      => 'AGBOSSOU',
                    'last_name' => 'Christine',
                    'email'     => 'admin@csm.bj',
                    'password'  => 'Admin@CSM2025',
                ],
                'classes' => [
                    ['name' => '5ème C', 'amount' => 35000],
                    ['name' => '4ème D', 'amount' => 38000],
                ],
                'subjects' => ['Mathématiques', 'Français', 'Anglais', 'Sciences', 'Éducation Civique', 'EPS'],
                'teachers' => [
                    ['name' => 'TOSSOU',  'last_name' => 'Pascal',  'email' => 'prof1@csm.bj'],
                    ['name' => 'GBAGUIDI','last_name' => 'Noëlle',  'email' => 'prof2@csm.bj'],
                ],
                'students' => [
                    ['name' => 'VODOUNOU','last_name' => 'Clément', 'email' => 'eleve1@csm.bj', 'class_idx' => 0],
                    ['name' => 'AHOTON',  'last_name' => 'Sandra',  'email' => 'eleve2@csm.bj', 'class_idx' => 0],
                    ['name' => 'MISSINHOUN','last_name' => 'Kevin', 'email' => 'eleve3@csm.bj', 'class_idx' => 1],
                    ['name' => 'AKAKPO',  'last_name' => 'Vanessa', 'email' => 'eleve4@csm.bj', 'class_idx' => 1],
                ],
                'parents' => [
                    ['name' => 'VODOUNOU','last_name' => 'Thomas',  'email' => 'parent1@csm.bj', 'student_idx' => 0],
                    ['name' => 'AHOTON',  'last_name' => 'Béatrice','email' => 'parent2@csm.bj', 'student_idx' => 1],
                ],
            ],
            [
                'school' => [
                    'school_name' => 'École Primaire Les Étoiles',
                    'school_type' => 'École Primaire',
                    'school_code' => 'epe-parakou',
                    'address'     => 'Quartier Zongo, Parakou, Bénin',
                    'phone'       => '0022997000003',
                    'email'       => 'contact@epe.bj',
                    'uai_number'  => 'BJ-EPE-003',
                    'status'      => 1,
                ],
                'admin' => [
                    'name'      => 'PARAISO',
                    'last_name' => 'Théodore',
                    'email'     => 'admin@epe.bj',
                    'password'  => 'Admin@EPE2025',
                ],
                'classes' => [
                    ['name' => 'CE2',  'amount' => 20000],
                    ['name' => 'CM1',  'amount' => 22000],
                ],
                'subjects' => ['Calcul', 'Lecture', 'Écriture', 'Éveil', 'Dessin', 'Chant'],
                'teachers' => [
                    ['name' => 'BIAO',    'last_name' => 'Alphonse', 'email' => 'prof1@epe.bj'],
                    ['name' => 'ZANNOU',  'last_name' => 'Cécile',   'email' => 'prof2@epe.bj'],
                ],
                'students' => [
                    ['name' => 'OROU',    'last_name' => 'Ibrahim',  'email' => 'eleve1@epe.bj', 'class_idx' => 0],
                    ['name' => 'SABI',    'last_name' => 'Fatima',   'email' => 'eleve2@epe.bj', 'class_idx' => 0],
                    ['name' => 'ALABI',   'last_name' => 'Moussa',   'email' => 'eleve3@epe.bj', 'class_idx' => 1],
                    ['name' => 'GUIWA',   'last_name' => 'Aïssatou', 'email' => 'eleve4@epe.bj', 'class_idx' => 1],
                ],
                'parents' => [
                    ['name' => 'OROU',    'last_name' => 'Mounirou', 'email' => 'parent1@epe.bj', 'student_idx' => 0],
                    ['name' => 'SABI',    'last_name' => 'Raïnatou', 'email' => 'parent2@epe.bj', 'student_idx' => 1],
                ],
            ],
        ];

        foreach ($schoolsData as $data) {
            $this->command->info("\n🏫 Création de l'école : {$data['school']['school_name']}");

            // ── 1. École ──────────────────────────────────────────────────────
            $school = School::firstOrCreate(
                ['school_code' => $data['school']['school_code']],
                array_merge($data['school'], ['is_delete' => 0])
            );

            // ── 2. Admin de l'école ───────────────────────────────────────────
            $admin = User::firstOrCreate(
                ['email' => $data['admin']['email']],
                [
                    'name'       => $data['admin']['name'],
                    'last_name'  => $data['admin']['last_name'],
                    'email'      => $data['admin']['email'],
                    'password'   => Hash::make($data['admin']['password']),
                    'user_type'  => 1,
                    'status'     => 1,
                    'is_delete'  => 0,
                    'school_id'  => $school->id,
                ]
            );
            // S'assurer que le school_id est bien mis
            $admin->school_id = $school->id;
            $admin->save();

            if (! $admin->hasRole('admin')) {
                $admin->assignRole('admin');
            }

            // Donner les permissions admin à cet utilisateur (direct + rôle)
            $adminPerms = array_filter(self::ADMIN_PERMISSIONS, function ($p) {
                return Permission::where('name', $p)->where('guard_name', 'web')->exists();
            });
            $admin->syncPermissions(array_values($adminPerms));
            // Ajouter aussi reset_password
            $resetPerm = Permission::where('name', 'action.users.reset_password')->first();
            if ($resetPerm) {
                $admin->givePermissionTo($resetPerm);
            }

            $this->command->info("  ✅ Admin : {$data['admin']['email']} / {$data['admin']['password']}");

            // ── 3. Classes ────────────────────────────────────────────────────
            $classIds = [];
            foreach ($data['classes'] as $classData) {
                // La table 'class' n'a pas encore school_id — on identifie par nom + created_by
                $existing = DB::table('class')
                    ->where('name', $classData['name'])
                    ->where('created_by', $admin->id)
                    ->first();

                if (! $existing) {
                    $classId = DB::table('class')->insertGetId([
                        'name'       => $classData['name'],
                        'amount'     => $classData['amount'],
                        'status'     => 1,
                        'is_delete'  => 0,
                        'created_by' => $admin->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $classIds[] = $classId;
                } else {
                    $classIds[] = $existing->id;
                }
            }
            $this->command->info("  ✅ " . count($classIds) . " classe(s) créée(s)");

            // ── 4. Matières ───────────────────────────────────────────────────
            $subjectIds = [];
            foreach ($data['subjects'] as $subjectName) {
                // Table: subject (sans 's'), pas de school_id — on identifie par nom
                $existing = DB::table('subject')
                    ->where('name', $subjectName)
                    ->first();

                if (! $existing) {
                    $subjectId = DB::table('subject')->insertGetId([
                        'name'       => $subjectName,
                        'type'       => 'general',
                        'status'     => 1,
                        'is_delete'  => 0,
                        'created_by' => $admin->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $subjectIds[] = $subjectId;
                } else {
                    $subjectIds[] = $existing->id;
                }
            }
            $this->command->info("  ✅ " . count($subjectIds) . " matière(s) créée(s)");

            // ── 5. Professeurs ────────────────────────────────────────────────
            $teacherIds = [];
            foreach ($data['teachers'] as $idx => $teacherData) {
                $teacher = User::firstOrCreate(
                    ['email' => $teacherData['email']],
                    [
                        'name'      => $teacherData['name'],
                        'last_name' => $teacherData['last_name'],
                        'email'     => $teacherData['email'],
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

                // Assigner à une classe
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
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
                $teacherIds[] = $teacher->id;
            }
            $this->command->info("  ✅ " . count($teacherIds) . " professeur(s) créé(s) — mot de passe : Prof@1234");

            // ── 6. Apprenants ─────────────────────────────────────────────────
            $studentIds = [];
            foreach ($data['students'] as $sData) {
                $classId = $classIds[$sData['class_idx']] ?? $classIds[0];
                $admNum  = strtoupper(substr($data['school']['school_code'], 0, 3))
                    . '-' . date('Y') . '-' . str_pad(count($studentIds) + 1, 4, '0', STR_PAD_LEFT);

                $student = User::firstOrCreate(
                    ['email' => $sData['email']],
                    [
                        'name'             => $sData['name'],
                        'last_name'        => $sData['last_name'],
                        'email'            => $sData['email'],
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
            $this->command->info("  ✅ " . count($studentIds) . " apprenant(s) créé(s) — mot de passe : Eleve@1234");

            // ── 7. Parents ────────────────────────────────────────────────────
            foreach ($data['parents'] as $pData) {
                $parent = User::firstOrCreate(
                    ['email' => $pData['email']],
                    [
                        'name'      => $pData['name'],
                        'last_name' => $pData['last_name'],
                        'email'     => $pData['email'],
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

                // Lier l'apprenant à ce parent
                if (isset($studentIds[$pData['student_idx']])) {
                    User::where('id', $studentIds[$pData['student_idx']])
                        ->update(['parent_id' => $parent->id]);
                }
            }
            $this->command->info("  ✅ " . count($data['parents']) . " parent(s) créé(s) — mot de passe : Parent@1234");

            // ── 8. Matières assignées aux classes ─────────────────────────────
            foreach ($classIds as $classId) {
                foreach ($subjectIds as $subjectId) {
                    $exists = DB::table('class_subject')
                        ->where('class_id', $classId)
                        ->where('subject_id', $subjectId)
                        ->exists();
                    if (! $exists) {
                        DB::table('class_subject')->insert([
                            'class_id'    => $classId,
                            'subject_id'  => $subjectId,
                            'coefficient' => rand(1, 4),
                            'created_by'  => $admin->id,
                            'status'      => 1,
                            'is_delete'   => 0,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ]);
                    }
                }
            }
            $this->command->info("  ✅ Matières assignées aux classes");
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->newLine();
        $this->command->info('══════════════════════════════════════════════');
        $this->command->info('✅ Multi-école seed terminé !');
        $this->command->newLine();
        $this->command->table(
            ['École', 'Admin email', 'Mot de passe'],
            [
                ['Lycée Moderne de Cotonou',    'admin@lmc.bj', 'Admin@LMC2025'],
                ['Collège Saint-Michel',         'admin@csm.bj', 'Admin@CSM2025'],
                ['École Primaire Les Étoiles',   'admin@epe.bj', 'Admin@EPE2025'],
            ]
        );
        $this->command->newLine();
        $this->command->line('Professeurs : Prof@1234  |  Apprenants : Eleve@1234  |  Parents : Parent@1234');
        $this->command->info('══════════════════════════════════════════════');
    }
}
