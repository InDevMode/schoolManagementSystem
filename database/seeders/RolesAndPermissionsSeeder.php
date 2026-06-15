<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Vider le cache des permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── 1. Créer les rôles avec user_type et description ────────────────
        $roles = [
            'admin'   => Role::firstOrCreate(
                ['name' => 'admin',   'guard_name' => 'web'],
                ['user_type' => 1, 'description' => 'Administrateur principal de la plateforme']
            ),
            'teacher' => Role::firstOrCreate(
                ['name' => 'teacher', 'guard_name' => 'web'],
                ['user_type' => 2, 'description' => 'Professeur / Enseignant']
            ),
            'student' => Role::firstOrCreate(
                ['name' => 'student', 'guard_name' => 'web'],
                ['user_type' => 3, 'description' => 'Apprenant / Élève']
            ),
            'parent'  => Role::firstOrCreate(
                ['name' => 'parent',  'guard_name' => 'web'],
                ['user_type' => 4, 'description' => 'Parent ou tuteur légal']
            ),
        ];

        // Mettre à jour les user_type si déjà existants sans cette info
        $roleDefaults = [
            'admin'   => ['user_type' => 1, 'description' => 'Administrateur principal de la plateforme'],
            'teacher' => ['user_type' => 2, 'description' => 'Professeur / Enseignant'],
            'student' => ['user_type' => 3, 'description' => 'Apprenant / Élève'],
            'parent'  => ['user_type' => 4, 'description' => 'Parent ou tuteur légal'],
        ];
        foreach ($roleDefaults as $name => $data) {
            Role::where('name', $name)->whereNull('user_type')->update($data);
        }

        // ─── 2. Créer les permissions par module ─────────────────────────────
        $permissions = [
            // ── Navigation / Pages (view.section.page) ──────────────────────
            'view.dashboard.admin',
            'view.users.admins', 'view.users.teachers', 'view.users.students', 'view.users.parents',
            'view.academics.classes', 'view.academics.subjects',
            'view.academics.assign_subjects', 'view.academics.assign_classes', 'view.academics.timetable',
            'view.exams.periods', 'view.exams.list', 'view.exams.schedules', 'view.exams.marks', 'view.exams.grades',
            'view.attendance.manage', 'view.attendance.report',
            'view.homework.list', 'view.homework.reports',
            'view.fees.collect', 'view.fees.reports',
            'view.communicate.noticeboard', 'view.communicate.mail',
            'view.settings',

            // ── Actions CRUD sur les administrateurs (granulaires) ──────────
            'action.admins.view', 'action.admins.create', 'action.admins.edit',
            'action.admins.delete', 'action.admins.reset_password', 'action.admins.export',
            // ── Actions CRUD sur les professeurs (granulaires) ───────────────
            'action.teachers.view', 'action.teachers.create', 'action.teachers.edit',
            'action.teachers.delete', 'action.teachers.reset_password', 'action.teachers.export',
            // ── Actions CRUD sur les apprenants (granulaires) ────────────────
            'action.students.view', 'action.students.create', 'action.students.edit',
            'action.students.delete', 'action.students.reset_password', 'action.students.export',
            // ── Actions CRUD sur les parents (granulaires) ───────────────────
            'action.parents.view', 'action.parents.create', 'action.parents.edit',
            'action.parents.delete', 'action.parents.reset_password', 'action.parents.export',
            'action.parents.manage_children',
            // ── Accès gestion globale des utilisateurs ────────────────────────
            'view.useradmins',       // ancien nom (rétrocompat)
            'view.users.all',        // nouveau nom utilisé par la page /superadmin/users
            // ── Actions CRUD sur les utilisateurs ────────────────────────────
            'action.users.create', 'action.users.edit', 'action.users.delete',
            // ── Actions CRUD académique ──────────────────────────────────────
            'action.classes.create', 'action.classes.edit', 'action.classes.delete',
            'action.subjects.create', 'action.subjects.edit', 'action.subjects.delete',
            // ── Actions examens & notes ──────────────────────────────────────
            'action.exams.create', 'action.exams.edit', 'action.exams.delete',
            'action.marks.manage',
            // ── Bulletins ────────────────────────────────────────────────────
            'view.bulletins.list',
            'action.bulletins.generate', 'action.bulletins.publish',
            // ── Personnel (Staff) ────────────────────────────────────────────
            'view.staff.list', 'view.staff.leaves', 'view.staff.events',
            'action.staff.create', 'action.staff.edit', 'action.staff.delete',
            'action.staff.leaves', 'action.staff.events',
            // ── Actions présences ────────────────────────────────────────────
            'action.attendance.save',
            // ── Actions devoirs ──────────────────────────────────────────────
            'action.homework.create', 'action.homework.edit', 'action.homework.delete',
            // ── Actions frais ────────────────────────────────────────────────
            'action.fees.collect', 'action.fees.delete',
            // ── Actions communication ────────────────────────────────────────
            'action.noticeboard.manage', 'action.mail.send',
            // ── Chat ─────────────────────────────────────────────────────────
            'chat.access',
            // ── Settings ─────────────────────────────────────────────────────
            'action.settings.manage',

            // ── Anciens noms (conservés pour rétrocompatibilité) ─────────────
            'admins.view', 'admins.create', 'admins.edit', 'admins.delete',
            'teachers.view', 'teachers.create', 'teachers.edit', 'teachers.delete',
            'students.view', 'students.create', 'students.edit', 'students.delete',
            'parents.view', 'parents.create', 'parents.edit', 'parents.delete',
            'classes.view', 'classes.create', 'classes.edit', 'classes.delete',
            'subjects.view', 'subjects.create', 'subjects.edit', 'subjects.delete',
            'assign.subjects', 'assign.classes',
            'timetable.view', 'timetable.manage',
            'exams.view', 'exams.create', 'exams.edit', 'exams.delete',
            'marks.view', 'marks.manage',
            'attendance.view', 'attendance.manage',
            'homework.view', 'homework.create', 'homework.edit', 'homework.delete',
            'fees.view', 'fees.manage',
            'noticeboard.view', 'noticeboard.manage',
            'mail.send',
            'settings.view', 'settings.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ─── 3. Assigner les permissions aux rôles ───────────────────────────

        // Admin : toutes les permissions (navigation + actions)
        $roles['admin']->syncPermissions($permissions);
        // Teacher : navigation + ses propres actions (évaluations incluses)
        $roles['teacher']->syncPermissions([
            // Navigation
            'view.academics.timetable',
            'view.exams.list', 'view.exams.schedules', 'view.exams.marks',
            'view.attendance.manage', 'view.attendance.report',
            'view.homework.list', 'view.homework.reports',
            'view.communicate.noticeboard',
            // Actions
            'action.exams.create', 'action.exams.edit',
            'action.marks.manage',
            'action.attendance.save',
            'action.homework.create', 'action.homework.edit', 'action.homework.delete',
            'chat.access',
            // Anciens noms rétrocompat
            'students.view', 'timetable.view',
            'exams.view', 'marks.view', 'marks.manage',
            'attendance.view', 'attendance.manage',
            'homework.view', 'homework.create', 'homework.edit', 'homework.delete',
            'noticeboard.view',
        ]);

        // Student : lecture seule + bulletins
        $roles['student']->syncPermissions([
            'view.academics.timetable',
            'view.exams.list', 'view.exams.marks',
            'view.bulletins.list',
            'view.attendance.report',
            'view.homework.list',
            'view.fees.collect',
            'view.communicate.noticeboard',
            'chat.access',
            // Rétrocompat
            'timetable.view', 'exams.view', 'marks.view',
            'attendance.view', 'homework.view',
            'fees.view', 'noticeboard.view',
        ]);

        // Parent : suivi des enfants + bulletins
        $roles['parent']->syncPermissions([
            'view.academics.timetable',
            'view.exams.list', 'view.exams.marks',
            'view.bulletins.list',
            'view.attendance.report',
            'view.homework.list',
            'view.fees.collect',
            'view.communicate.noticeboard',
            'chat.access',
            // Rétrocompat
            'students.view', 'timetable.view',
            'exams.view', 'marks.view',
            'attendance.view', 'homework.view',
            'fees.view', 'noticeboard.view',
        ]);

        // ─── 4. Migrer les utilisateurs existants vers les rôles Spatie ──────
        $map = [
            1 => 'admin',
            2 => 'teacher',
            3 => 'student',
            4 => 'parent',
        ];

        $migrated = 0;
        User::whereIn('user_type', [1, 2, 3, 4])
            ->where('is_delete', 0)
            ->chunk(100, function ($users) use ($map, &$migrated) {
                foreach ($users as $user) {
                    $roleName = $map[$user->user_type] ?? null;
                    if ($roleName && ! $user->hasRole($roleName)) {
                        $user->assignRole($roleName);
                        $migrated++;
                    }
                }
            });

        $this->command->info("✅ Rôles et permissions créés.");
        $this->command->info("✅ {$migrated} utilisateur(s) migré(s) vers les rôles Spatie.");
    }
}
