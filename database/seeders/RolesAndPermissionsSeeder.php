<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * RolesAndPermissionsSeeder — Crée rôles, permissions et les assigne.
 *
 * Rôles :
 *   super_admin (0), admin (1), teacher (2), student (3), parent (4)
 *
 * Dépendances : aucune (tables Spatie créées par la migration permissions_tables)
 */
class RolesAndPermissionsSeeder extends Seeder
{
    // ── Toutes les permissions de l'application ──────────────────────────────
    private const ALL_PERMISSIONS = [
        // Navigation
        'view.dashboard.admin',
        'view.users.admins', 'view.users.teachers', 'view.users.students', 'view.users.parents',
        'view.users.all',
        'view.academics.classes', 'view.academics.subjects',
        'view.academics.assign_subjects', 'view.academics.assign_classes', 'view.academics.timetable',
        'view.exams.periods', 'view.exams.list', 'view.exams.schedules', 'view.exams.marks', 'view.exams.grades',
        'view.attendance.manage', 'view.attendance.report',
        'view.homework.list', 'view.homework.reports',
        'view.fees.collect', 'view.fees.reports',
        'view.communicate.noticeboard', 'view.communicate.mail',
        'view.settings',
        'view.bulletins.list',
        'view.staff.list', 'view.staff.leaves', 'view.staff.events',

        // Actions utilisateurs
        'action.users.create', 'action.users.edit', 'action.users.delete', 'action.users.reset_password',
        'action.admins.view', 'action.admins.create', 'action.admins.edit',
        'action.admins.delete', 'action.admins.reset_password', 'action.admins.export',
        'action.teachers.view', 'action.teachers.create', 'action.teachers.edit',
        'action.teachers.delete', 'action.teachers.reset_password', 'action.teachers.export',
        'action.students.view', 'action.students.create', 'action.students.edit',
        'action.students.delete', 'action.students.reset_password', 'action.students.export',
        'action.parents.view', 'action.parents.create', 'action.parents.edit',
        'action.parents.delete', 'action.parents.reset_password', 'action.parents.export',
        'action.parents.manage_children',

        // Actions académiques
        'action.classes.create', 'action.classes.edit', 'action.classes.delete',
        'action.subjects.create', 'action.subjects.edit', 'action.subjects.delete',

        // Actions examens & notes
        'action.exams.create', 'action.exams.edit', 'action.exams.delete',
        'action.marks.manage',

        // Bulletins
        'action.bulletins.generate', 'action.bulletins.publish',

        // Personnel
        'action.staff.create', 'action.staff.edit', 'action.staff.delete',
        'action.staff.leaves', 'action.staff.events',

        // Présences
        'action.attendance.save',

        // Devoirs
        'action.homework.create', 'action.homework.edit', 'action.homework.delete',

        // Frais
        'action.fees.collect', 'action.fees.delete',

        // Communication
        'action.noticeboard.manage', 'action.mail.send',

        // Chat
        'chat.access',

        // Settings
        'action.settings.manage',

        // RBAC (super_admin seulement)
        'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
        'permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete',
        'permissions.assign',

        // Anciens noms (rétrocompatibilité)
        'view.useradmins',
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

    // ── Permissions par rôle ─────────────────────────────────────────────────
    private const TEACHER_PERMISSIONS = [
        'view.academics.timetable',
        'view.exams.list', 'view.exams.schedules', 'view.exams.marks',
        'view.attendance.manage', 'view.attendance.report',
        'view.homework.list', 'view.homework.reports',
        'view.communicate.noticeboard',
        'action.exams.create', 'action.exams.edit',
        'action.marks.manage',
        'action.attendance.save',
        'action.homework.create', 'action.homework.edit', 'action.homework.delete',
        'chat.access',
        // Rétrocompat
        'students.view', 'timetable.view',
        'exams.view', 'marks.view', 'marks.manage',
        'attendance.view', 'attendance.manage',
        'homework.view', 'homework.create', 'homework.edit', 'homework.delete',
        'noticeboard.view',
    ];

    private const STUDENT_PERMISSIONS = [
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
    ];

    private const PARENT_PERMISSIONS = [
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
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. Créer toutes les permissions ──────────────────────────────────
        foreach (self::ALL_PERMISSIONS as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
        $this->command->info('  ✅ ' . count(self::ALL_PERMISSIONS) . ' permissions créées/vérifiées.');

        // ── 2. Créer les rôles ────────────────────────────────────────────────
        $roleDefinitions = [
            'super_admin' => ['user_type' => 0, 'description' => 'Super administrateur — accès total'],
            'admin'       => ['user_type' => 1, 'description' => 'Administrateur d\'école'],
            'teacher'     => ['user_type' => 2, 'description' => 'Professeur / Enseignant'],
            'student'     => ['user_type' => 3, 'description' => 'Apprenant / Élève'],
            'parent'      => ['user_type' => 4, 'description' => 'Parent ou tuteur légal'],
        ];

        $roles = [];
        foreach ($roleDefinitions as $name => $attrs) {
            $role = Role::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                $attrs
            );
            // Mettre à jour si les champs sont absents
            if (is_null($role->user_type)) {
                $role->update($attrs);
            }
            $roles[$name] = $role;
        }
        $this->command->info('  ✅ 5 rôles créés/vérifiés.');

        // ── 3. Assigner les permissions aux rôles ────────────────────────────
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // super_admin : toutes les permissions
        $allPerms = Permission::where('guard_name', 'web')->get();
        $roles['super_admin']->syncPermissions($allPerms);

        // admin : tout sauf RBAC
        $adminPerms = Permission::where('guard_name', 'web')
            ->whereNotIn('name', ['roles.view', 'roles.create', 'roles.edit', 'roles.delete',
                'permissions.view', 'permissions.create', 'permissions.edit',
                'permissions.delete', 'permissions.assign'])
            ->get();
        $roles['admin']->syncPermissions($adminPerms);

        $roles['teacher']->syncPermissions(self::TEACHER_PERMISSIONS);
        $roles['student']->syncPermissions(self::STUDENT_PERMISSIONS);
        $roles['parent']->syncPermissions(self::PARENT_PERMISSIONS);

        $this->command->info('  ✅ Permissions assignées aux rôles.');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
