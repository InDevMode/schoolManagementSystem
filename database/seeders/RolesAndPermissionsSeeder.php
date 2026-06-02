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

        // ─── 1. Créer les rôles ───────────────────────────────────────────────
        $roles = [
            'admin'   => Role::firstOrCreate(['name' => 'admin',   'guard_name' => 'web']),
            'teacher' => Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']),
            'student' => Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']),
            'parent'  => Role::firstOrCreate(['name' => 'parent',  'guard_name' => 'web']),
        ];

        // ─── 2. Créer les permissions par module ─────────────────────────────
        $permissions = [
            // Admins
            'admins.view', 'admins.create', 'admins.edit', 'admins.delete',
            // Teachers
            'teachers.view', 'teachers.create', 'teachers.edit', 'teachers.delete',
            // Students
            'students.view', 'students.create', 'students.edit', 'students.delete',
            // Parents
            'parents.view', 'parents.create', 'parents.edit', 'parents.delete',
            // Classes
            'classes.view', 'classes.create', 'classes.edit', 'classes.delete',
            // Subjects
            'subjects.view', 'subjects.create', 'subjects.edit', 'subjects.delete',
            // Assign
            'assign.subjects', 'assign.classes',
            // Timetable
            'timetable.view', 'timetable.manage',
            // Examinations
            'exams.view', 'exams.create', 'exams.edit', 'exams.delete',
            'marks.view', 'marks.manage',
            // Attendance
            'attendance.view', 'attendance.manage',
            // Homework
            'homework.view', 'homework.create', 'homework.edit', 'homework.delete',
            // Fees
            'fees.view', 'fees.manage',
            // Communication
            'noticeboard.view', 'noticeboard.manage',
            'mail.send',
            // Settings
            'settings.view', 'settings.manage',
            // Chat
            'chat.access',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ─── 3. Assigner les permissions aux rôles ───────────────────────────

        // Admin : tout
        $roles['admin']->syncPermissions($permissions);

        // Teacher : ses propres données
        $roles['teacher']->syncPermissions([
            'students.view',
            'timetable.view',
            'exams.view', 'marks.view', 'marks.manage',
            'attendance.view', 'attendance.manage',
            'homework.view', 'homework.create', 'homework.edit', 'homework.delete',
            'noticeboard.view',
            'chat.access',
        ]);

        // Student : lecture seule de ses données
        $roles['student']->syncPermissions([
            'timetable.view',
            'exams.view', 'marks.view',
            'attendance.view',
            'homework.view',
            'fees.view',
            'noticeboard.view',
            'chat.access',
        ]);

        // Parent : suivi de ses enfants
        $roles['parent']->syncPermissions([
            'students.view',
            'timetable.view',
            'exams.view', 'marks.view',
            'attendance.view',
            'homework.view',
            'fees.view',
            'noticeboard.view',
            'chat.access',
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
