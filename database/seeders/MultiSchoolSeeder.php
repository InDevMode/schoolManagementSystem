<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * MultiSchoolSeeder — Données complètes pour 3 écoles.
 * Aucune colonne nullable laissée vide.
 * Max 20 enregistrements par table.
 */
class MultiSchoolSeeder extends Seeder
{
    // ── UUIDs fixes pour les admins (référencés dans d'autres seeders) ─────────
    public const LMC_ADMIN_ID  = 'a1000001-bb01-4000-8000-000000000001';
    public const CSM_ADMIN_ID  = 'a1000001-bb02-4000-8000-000000000002';
    public const EPE_ADMIN_ID  = 'a1000001-bb03-4000-8000-000000000003';

    public const LMC_PROF1_ID  = 'a2000001-bb01-4000-8000-000000000001';
    public const LMC_PROF2_ID  = 'a2000001-bb02-4000-8000-000000000002';
    public const CSM_PROF1_ID  = 'a2000001-bb03-4000-8000-000000000003';
    public const CSM_PROF2_ID  = 'a2000001-bb04-4000-8000-000000000004';
    public const EPE_PROF1_ID  = 'a2000001-bb05-4000-8000-000000000005';
    public const EPE_PROF2_ID  = 'a2000001-bb06-4000-8000-000000000006';

    // ── UUIDs fixes pour les classes ──────────────────────────────────────────
    public const LMC_CLASS1_ID = 'c1000001-cc01-4000-8000-000000000001';
    public const LMC_CLASS2_ID = 'c1000001-cc02-4000-8000-000000000002';
    public const CSM_CLASS1_ID = 'c1000001-cc03-4000-8000-000000000003';
    public const CSM_CLASS2_ID = 'c1000001-cc04-4000-8000-000000000004';
    public const EPE_CLASS1_ID = 'c1000001-cc05-4000-8000-000000000005';
    public const EPE_CLASS2_ID = 'c1000001-cc06-4000-8000-000000000006';

    // ── UUIDs fixes pour les matières (partagées) ─────────────────────────────
    public const SUBJ_MATHS_ID   = 'da000001-aa01-4000-8000-000000000001';
    public const SUBJ_FRENCH_ID  = 'da000001-aa02-4000-8000-000000000002';
    public const SUBJ_PHYS_ID    = 'da000001-aa03-4000-8000-000000000003';
    public const SUBJ_SVT_ID     = 'da000001-aa04-4000-8000-000000000004';
    public const SUBJ_HIST_ID    = 'da000001-aa05-4000-8000-000000000005';
    public const SUBJ_ENGLISH_ID = 'da000001-aa06-4000-8000-000000000006';
    public const SUBJ_PHILO_ID   = 'da000001-aa07-4000-8000-000000000007';
    public const SUBJ_EPS_ID     = 'da000001-aa08-4000-8000-000000000008';
    public const SUBJ_SCIENCES_ID= 'da000001-aa09-4000-8000-000000000009';
    public const SUBJ_CIVIQUE_ID = 'da000001-aa10-4000-8000-000000000010';
    public const SUBJ_CALCUL_ID  = 'da000001-aa11-4000-8000-000000000011';
    public const SUBJ_LECTURE_ID = 'da000001-aa12-4000-8000-000000000012';

    // ── Permissions admin ─────────────────────────────────────────────────────
    private const ADMIN_PERMISSIONS = [
        'view.dashboard.admin',
        'view.users.all','view.users.admins','view.users.teachers','view.users.students','view.users.parents',
        'action.users.edit','action.users.delete','action.users.reset_password',
        'action.admins.view','action.admins.create','action.admins.edit','action.admins.delete','action.admins.reset_password','action.admins.export',
        'action.teachers.view','action.teachers.create','action.teachers.edit','action.teachers.delete','action.teachers.reset_password','action.teachers.export',
        'action.students.view','action.students.create','action.students.edit','action.students.delete','action.students.reset_password','action.students.export',
        'action.parents.view','action.parents.create','action.parents.edit','action.parents.delete','action.parents.reset_password','action.parents.export',
        'action.parents.manage_children',
        'view.academics.classes','view.academics.subjects','view.academics.assign_subjects','view.academics.assign_classes','view.academics.timetable',
        'action.classes.create','action.classes.edit','action.classes.delete',
        'action.subjects.create','action.subjects.edit','action.subjects.delete',
        'view.exams.periods','view.exams.list','view.exams.marks',
        'action.exams.create','action.exams.edit','action.exams.delete','action.marks.manage',
        'view.bulletins.list','action.bulletins.generate','action.bulletins.publish',
        'view.attendance.manage','view.attendance.report','action.attendance.save',
        'view.homework.list','view.homework.reports','action.homework.create','action.homework.edit','action.homework.delete',
        'view.fees.collect','view.fees.reports','action.fees.collect','action.fees.delete',
        'view.communicate.noticeboard','view.communicate.mail','action.noticeboard.manage','action.mail.send',
        'view.staff.list','view.staff.leaves','view.staff.events',
        'action.staff.create','action.staff.edit','action.staff.delete','action.staff.leaves','action.staff.events',
        'view.settings','action.settings.manage','chat.access',
    ];

    // ── Données des 3 écoles ──────────────────────────────────────────────────
    private array $schools = [
        [
            'school_id'  => SchoolSeeder::LMC_ID,
            'admin_id'   => self::LMC_ADMIN_ID,
            'class1_id'  => self::LMC_CLASS1_ID,
            'class2_id'  => self::LMC_CLASS2_ID,
            'prof1_id'   => self::LMC_PROF1_ID,
            'prof2_id'   => self::LMC_PROF2_ID,
            'admin' => [
                'name' => 'KOFFI', 'last_name' => 'Arnaud', 'email' => 'admin@lmc.bj',
                'password' => 'Admin@LMC2025', 'gender' => 'Male',
                'mobile_number' => '+229 97 10 20 30', 'address' => 'Cotonou, Bénin',
                'date_of_birth' => '1980-05-15', 'blood_group' => 'A+',
                'religion' => 'Chrétien', 'occupation' => 'Directeur administratif',
                'qualification' => 'Master en Administration Scolaire',
                'work_experience' => '15 ans dans l\'éducation nationale',
                'marital_status' => 'Marié', 'note' => 'Admin principal du Lycée Moderne',
            ],
            'teachers' => [
                [
                    'id' => self::LMC_PROF1_ID, 'class_idx' => 0,
                    'name' => 'ADJAÏ', 'last_name' => 'Rodrigue', 'email' => 'prof1@lmc.bj',
                    'gender' => 'Male', 'mobile_number' => '+229 97 11 11 11',
                    'date_of_birth' => '1985-03-20', 'blood_group' => 'B+',
                    'religion' => 'Chrétien', 'qualification' => 'CAPES Mathématiques',
                    'work_experience' => '10 ans', 'marital_status' => 'Marié',
                    'address' => 'Akpakpa, Cotonou', 'occupation' => 'Professeur de Mathématiques',
                    'note' => 'Responsable filière scientifique',
                ],
                [
                    'id' => self::LMC_PROF2_ID, 'class_idx' => 1,
                    'name' => 'SOGLO', 'last_name' => 'Marlène', 'email' => 'prof2@lmc.bj',
                    'gender' => 'Female', 'mobile_number' => '+229 97 22 22 22',
                    'date_of_birth' => '1988-07-12', 'blood_group' => 'O+',
                    'religion' => 'Catholique', 'qualification' => 'Licence Lettres Modernes',
                    'work_experience' => '7 ans', 'marital_status' => 'Célibataire',
                    'address' => 'Fidjrossè, Cotonou', 'occupation' => 'Professeure de Français',
                    'note' => 'Coordinatrice club de lecture',
                ],
            ],
            'classes' => [
                ['id' => self::LMC_CLASS1_ID, 'name' => '3ème A', 'amount' => 45000],
                ['id' => self::LMC_CLASS2_ID, 'name' => '2nde B', 'amount' => 50000],
            ],
            'subjects' => [
                ['id' => self::SUBJ_MATHS_ID,   'name' => 'Mathématiques',   'coeff' => 4],
                ['id' => self::SUBJ_PHYS_ID,     'name' => 'Physique-Chimie', 'coeff' => 3],
                ['id' => self::SUBJ_SVT_ID,      'name' => 'SVT',             'coeff' => 2],
                ['id' => self::SUBJ_FRENCH_ID,   'name' => 'Français',        'coeff' => 4],
                ['id' => self::SUBJ_HIST_ID,     'name' => 'Histoire-Géo',    'coeff' => 2],
                ['id' => self::SUBJ_ENGLISH_ID,  'name' => 'Anglais',         'coeff' => 2],
                ['id' => self::SUBJ_PHILO_ID,    'name' => 'Philosophie',     'coeff' => 2],
                ['id' => self::SUBJ_EPS_ID,      'name' => 'EPS',             'coeff' => 1],
            ],
            'students' => [
                ['name' => 'HOUNSOU',    'last_name' => 'Michel',    'email' => 'eleve1@lmc.bj', 'class_idx' => 0, 'gender' => 'Male',   'dob' => '2007-03-14', 'blood_group' => 'A+',  'religion' => 'Chrétien'],
                ['name' => 'DOSSOU',     'last_name' => 'Patricia',  'email' => 'eleve2@lmc.bj', 'class_idx' => 0, 'gender' => 'Female', 'dob' => '2007-08-22', 'blood_group' => 'B+',  'religion' => 'Catholique'],
                ['name' => 'AZONNOU',    'last_name' => 'François',  'email' => 'eleve3@lmc.bj', 'class_idx' => 0, 'gender' => 'Male',   'dob' => '2008-01-05', 'blood_group' => 'O+',  'religion' => 'Protestant'],
                ['name' => 'AHOUA',      'last_name' => 'Estelle',   'email' => 'eleve4@lmc.bj', 'class_idx' => 1, 'gender' => 'Female', 'dob' => '2006-11-30', 'blood_group' => 'AB+', 'religion' => 'Catholique'],
                ['name' => 'GNONLONFOUN','last_name' => 'Kévin',     'email' => 'eleve5@lmc.bj', 'class_idx' => 1, 'gender' => 'Male',   'dob' => '2006-06-18', 'blood_group' => 'A-',  'religion' => 'Chrétien'],
                ['name' => 'AGOSSOU',    'last_name' => 'Béatrice',  'email' => 'eleve6@lmc.bj', 'class_idx' => 1, 'gender' => 'Female', 'dob' => '2007-04-09', 'blood_group' => 'O-',  'religion' => 'Catholique'],
            ],
            'parents' => [
                ['name' => 'HOUNSOU',  'last_name' => 'Jean',       'email' => 'parent1@lmc.bj', 'student_idx' => 0, 'gender' => 'Male',   'occupation' => 'Fonctionnaire',  'mobile_number' => '+229 97 30 10 01'],
                ['name' => 'DOSSOU',   'last_name' => 'Marie',      'email' => 'parent2@lmc.bj', 'student_idx' => 1, 'gender' => 'Female', 'occupation' => 'Commerçante',    'mobile_number' => '+229 97 30 10 02'],
                ['name' => 'AZONNOU',  'last_name' => 'Christophe', 'email' => 'parent3@lmc.bj', 'student_idx' => 2, 'gender' => 'Male',   'occupation' => 'Ingénieur',       'mobile_number' => '+229 97 30 10 03'],
            ],
        ],
        [
            'school_id'  => SchoolSeeder::CSM_ID,
            'admin_id'   => self::CSM_ADMIN_ID,
            'class1_id'  => self::CSM_CLASS1_ID,
            'class2_id'  => self::CSM_CLASS2_ID,
            'prof1_id'   => self::CSM_PROF1_ID,
            'prof2_id'   => self::CSM_PROF2_ID,
            'admin' => [
                'name' => 'AGBOSSOU', 'last_name' => 'Christine', 'email' => 'admin@csm.bj',
                'password' => 'Admin@CSM2025', 'gender' => 'Female',
                'mobile_number' => '+229 97 40 50 60', 'address' => 'Abomey-Calavi, Bénin',
                'date_of_birth' => '1975-11-08', 'blood_group' => 'B+',
                'religion' => 'Catholique', 'occupation' => 'Directrice pédagogique',
                'qualification' => 'Master en Sciences de l\'Éducation',
                'work_experience' => '20 ans dans l\'enseignement',
                'marital_status' => 'Mariée', 'note' => 'Admin principal du Collège Saint-Michel',
            ],
            'teachers' => [
                [
                    'id' => self::CSM_PROF1_ID, 'class_idx' => 0,
                    'name' => 'TOSSOU', 'last_name' => 'Pascal', 'email' => 'prof1@csm.bj',
                    'gender' => 'Male', 'mobile_number' => '+229 97 41 41 41',
                    'date_of_birth' => '1982-09-15', 'blood_group' => 'A+',
                    'religion' => 'Protestant', 'qualification' => 'CAPES Sciences',
                    'work_experience' => '12 ans', 'marital_status' => 'Marié',
                    'address' => 'Abomey-Calavi centre', 'occupation' => 'Professeur de Sciences',
                    'note' => 'Chef de département sciences',
                ],
                [
                    'id' => self::CSM_PROF2_ID, 'class_idx' => 1,
                    'name' => 'GBAGUIDI', 'last_name' => 'Noëlle', 'email' => 'prof2@csm.bj',
                    'gender' => 'Female', 'mobile_number' => '+229 97 42 42 42',
                    'date_of_birth' => '1990-02-28', 'blood_group' => 'O+',
                    'religion' => 'Catholique', 'qualification' => 'Licence Anglais',
                    'work_experience' => '5 ans', 'marital_status' => 'Célibataire',
                    'address' => 'Calavi, Bénin', 'occupation' => 'Professeure d\'Anglais',
                    'note' => 'Responsable club d\'anglais',
                ],
            ],
            'classes' => [
                ['id' => self::CSM_CLASS1_ID, 'name' => '5ème C', 'amount' => 35000],
                ['id' => self::CSM_CLASS2_ID, 'name' => '4ème D', 'amount' => 38000],
            ],
            'subjects' => [
                ['id' => self::SUBJ_MATHS_ID,   'name' => 'Mathématiques',    'coeff' => 3],
                ['id' => self::SUBJ_FRENCH_ID,   'name' => 'Français',         'coeff' => 3],
                ['id' => self::SUBJ_ENGLISH_ID,  'name' => 'Anglais',          'coeff' => 2],
                ['id' => self::SUBJ_SCIENCES_ID, 'name' => 'Sciences',         'coeff' => 2],
                ['id' => self::SUBJ_CIVIQUE_ID,  'name' => 'Éducation Civique','coeff' => 1],
                ['id' => self::SUBJ_EPS_ID,      'name' => 'EPS',              'coeff' => 1],
            ],
            'students' => [
                ['name' => 'VODOUNOU',   'last_name' => 'Clément',  'email' => 'eleve1@csm.bj', 'class_idx' => 0, 'gender' => 'Male',   'dob' => '2010-04-20', 'blood_group' => 'B+',  'religion' => 'Catholique'],
                ['name' => 'AHOTON',     'last_name' => 'Sandra',   'email' => 'eleve2@csm.bj', 'class_idx' => 0, 'gender' => 'Female', 'dob' => '2010-09-03', 'blood_group' => 'A+',  'religion' => 'Chrétienne'],
                ['name' => 'MISSINHOUN', 'last_name' => 'Kevin',    'email' => 'eleve3@csm.bj', 'class_idx' => 0, 'gender' => 'Male',   'dob' => '2009-12-15', 'blood_group' => 'O+',  'religion' => 'Protestant'],
                ['name' => 'AKAKPO',     'last_name' => 'Vanessa',  'email' => 'eleve4@csm.bj', 'class_idx' => 1, 'gender' => 'Female', 'dob' => '2009-07-28', 'blood_group' => 'AB-', 'religion' => 'Catholique'],
                ['name' => 'KAKPO',      'last_name' => 'Dimitri',  'email' => 'eleve5@csm.bj', 'class_idx' => 1, 'gender' => 'Male',   'dob' => '2009-02-11', 'blood_group' => 'A-',  'religion' => 'Chrétien'],
            ],
            'parents' => [
                ['name' => 'VODOUNOU', 'last_name' => 'Thomas',   'email' => 'parent1@csm.bj', 'student_idx' => 0, 'gender' => 'Male',   'occupation' => 'Médecin',       'mobile_number' => '+229 97 50 10 01'],
                ['name' => 'AHOTON',   'last_name' => 'Béatrice', 'email' => 'parent2@csm.bj', 'student_idx' => 1, 'gender' => 'Female', 'occupation' => 'Enseignante',   'mobile_number' => '+229 97 50 10 02'],
            ],
        ],
        [
            'school_id'  => SchoolSeeder::EPE_ID,
            'admin_id'   => self::EPE_ADMIN_ID,
            'class1_id'  => self::EPE_CLASS1_ID,
            'class2_id'  => self::EPE_CLASS2_ID,
            'prof1_id'   => self::EPE_PROF1_ID,
            'prof2_id'   => self::EPE_PROF2_ID,
            'admin' => [
                'name' => 'PARAISO', 'last_name' => 'Théodore', 'email' => 'admin@epe.bj',
                'password' => 'Admin@EPE2025', 'gender' => 'Male',
                'mobile_number' => '+229 97 70 80 90', 'address' => 'Parakou, Bénin',
                'date_of_birth' => '1978-09-22', 'blood_group' => 'O+',
                'religion' => 'Musulman', 'occupation' => 'Directeur d\'école',
                'qualification' => 'Diplôme d\'Instituteur Principal',
                'work_experience' => '18 ans dans l\'éducation primaire',
                'marital_status' => 'Marié', 'note' => 'Admin principal de l\'École Primaire Les Étoiles',
            ],
            'teachers' => [
                [
                    'id' => self::EPE_PROF1_ID, 'class_idx' => 0,
                    'name' => 'BIAO', 'last_name' => 'Alphonse', 'email' => 'prof1@epe.bj',
                    'gender' => 'Male', 'mobile_number' => '+229 97 71 71 71',
                    'date_of_birth' => '1983-06-10', 'blood_group' => 'B-',
                    'religion' => 'Chrétien', 'qualification' => 'Certificat d\'Aptitude Pédagogique',
                    'work_experience' => '14 ans', 'marital_status' => 'Marié',
                    'address' => 'Parakou nord', 'occupation' => 'Instituteur CE2',
                    'note' => 'Maître de classe CE2',
                ],
                [
                    'id' => self::EPE_PROF2_ID, 'class_idx' => 1,
                    'name' => 'ZANNOU', 'last_name' => 'Cécile', 'email' => 'prof2@epe.bj',
                    'gender' => 'Female', 'mobile_number' => '+229 97 72 72 72',
                    'date_of_birth' => '1987-12-03', 'blood_group' => 'A+',
                    'religion' => 'Catholique', 'qualification' => 'BEPC + Formation Pédagogique',
                    'work_experience' => '8 ans', 'marital_status' => 'Mariée',
                    'address' => 'Parakou centre', 'occupation' => 'Institutrice CM1',
                    'note' => 'Maîtresse de classe CM1',
                ],
            ],
            'classes' => [
                ['id' => self::EPE_CLASS1_ID, 'name' => 'CE2', 'amount' => 20000],
                ['id' => self::EPE_CLASS2_ID, 'name' => 'CM1', 'amount' => 22000],
            ],
            'subjects' => [
                ['id' => self::SUBJ_CALCUL_ID,  'name' => 'Calcul',   'coeff' => 3],
                ['id' => self::SUBJ_LECTURE_ID,  'name' => 'Lecture',  'coeff' => 3],
                ['id' => self::SUBJ_FRENCH_ID,   'name' => 'Français', 'coeff' => 2],
                ['id' => self::SUBJ_SCIENCES_ID, 'name' => 'Sciences', 'coeff' => 2],
                ['id' => self::SUBJ_CIVIQUE_ID,  'name' => 'Éducation Civique', 'coeff' => 1],
                ['id' => self::SUBJ_EPS_ID,      'name' => 'EPS',      'coeff' => 1],
            ],
            'students' => [
                ['name' => 'OROU',  'last_name' => 'Ibrahim',  'email' => 'eleve1@epe.bj', 'class_idx' => 0, 'gender' => 'Male',   'dob' => '2015-02-14', 'blood_group' => 'O+', 'religion' => 'Musulman'],
                ['name' => 'SABI',  'last_name' => 'Fatima',   'email' => 'eleve2@epe.bj', 'class_idx' => 0, 'gender' => 'Female', 'dob' => '2015-07-30', 'blood_group' => 'A+', 'religion' => 'Musulmane'],
                ['name' => 'ALABI', 'last_name' => 'Moussa',   'email' => 'eleve3@epe.bj', 'class_idx' => 1, 'gender' => 'Male',   'dob' => '2014-11-05', 'blood_group' => 'B+', 'religion' => 'Musulman'],
                ['name' => 'GUIWA', 'last_name' => 'Aïssatou', 'email' => 'eleve4@epe.bj', 'class_idx' => 1, 'gender' => 'Female', 'dob' => '2014-04-19', 'blood_group' => 'O-', 'religion' => 'Musulmane'],
                ['name' => 'SANNI', 'last_name' => 'Rachidou', 'email' => 'eleve5@epe.bj', 'class_idx' => 1, 'gender' => 'Male',   'dob' => '2014-08-23', 'blood_group' => 'A-', 'religion' => 'Chrétien'],
            ],
            'parents' => [
                ['name' => 'OROU', 'last_name' => 'Mounirou', 'email' => 'parent1@epe.bj', 'student_idx' => 0, 'gender' => 'Male',   'occupation' => 'Commerçant',  'mobile_number' => '+229 97 60 10 01'],
                ['name' => 'SABI', 'last_name' => 'Raïnatou', 'email' => 'parent2@epe.bj', 'student_idx' => 1, 'gender' => 'Female', 'occupation' => 'Agricultrice','mobile_number' => '+229 97 60 10 02'],
            ],
        ],
    ];

    // ── run() ─────────────────────────────────────────────────────────────────

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $adminRole   = Role::firstOrCreate(['name' => 'admin',   'guard_name' => 'web'], ['user_type' => 1]);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web'], ['user_type' => 2]);
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web'], ['user_type' => 3]);
        $parentRole  = Role::firstOrCreate(['name' => 'parent',  'guard_name' => 'web'], ['user_type' => 4]);

        $this->seedSubjects();

        foreach ($this->schools as $data) {
            $school = DB::table('schools')->where('id', $data['school_id'])->first();
            if (!$school) { $this->command->error("École manquante : {$data['school_id']}"); continue; }

            $this->command->info("\n  🏫 {$school->school_name}");

            $adminId   = $this->seedAdmin($data, $school, $adminRole);
            $classIds  = $this->seedClasses($data, $school, $adminId);
            $subjIds   = $this->seedClassSubjects($data, $classIds, $adminId);
            $teacherIds= $this->seedTeachers($data, $school, $classIds, $adminId, $teacherRole);
            $studentIds= $this->seedStudents($data, $school, $classIds, $adminId, $studentRole);
            $this->seedParents($data, $school, $studentIds, $adminId, $parentRole);
            $this->seedTimetable($classIds, $subjIds, $teacherIds, $school->id, $adminId);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->newLine();
        $this->command->table(
            ['École', 'Admin', 'MDP'],
            [
                ['Lycée Moderne de Cotonou',   'admin@lmc.bj', 'Admin@LMC2025'],
                ['Collège Saint-Michel',        'admin@csm.bj', 'Admin@CSM2025'],
                ['École Primaire Les Étoiles',  'admin@epe.bj', 'Admin@EPE2025'],
            ]
        );
    }

    // ── Matières globales ─────────────────────────────────────────────────────

    private function seedSubjects(): void
    {
        $allSubjects = array_merge(
            $this->schools[0]['subjects'],
            $this->schools[1]['subjects'],
            $this->schools[2]['subjects']
        );
        $seen = [];
        foreach ($allSubjects as $s) {
            if (isset($seen[$s['id']])) continue;
            $seen[$s['id']] = true;
            if (!DB::table('subject')->where('id', $s['id'])->exists()) {
                DB::table('subject')->insert([
                    'id'         => $s['id'],
                    'name'       => $s['name'],
                    'type'       => 'general',
                    'status'     => 1,
                    'is_delete'  => 0,
                    'created_by' => SuperAdminSeeder::SUPER_ADMIN_ID,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    // ── Admin ─────────────────────────────────────────────────────────────────

    private function seedAdmin(array $data, object $school, Role $role): string
    {
        $d = $data['admin'];
        $admin = User::firstOrCreate(
            ['email' => $d['email']],
            [
                'id'              => $data['admin_id'],
                'name'            => $d['name'],
                'last_name'       => $d['last_name'],
                'password'        => Hash::make($d['password']),
                'user_type'       => 1,
                'status'          => 1,
                'is_delete'       => 0,
                'school_id'       => $school->id,
                'gender'          => $d['gender'],
                'mobile_number'   => $d['mobile_number'],
                'address'         => $d['address'],
                'permanent_address'=> $d['address'],
                'date_of_birth'   => $d['date_of_birth'],
                'blood_group'     => $d['blood_group'],
                'religion'        => $d['religion'],
                'occupation'      => $d['occupation'],
                'qualification'   => $d['qualification'],
                'work_experience' => $d['work_experience'],
                'marital_status'  => $d['marital_status'],
                'note'            => $d['note'],
                'admission_date'  => '2020-09-01',
            ]
        );
        $admin->school_id = $school->id;
        $admin->save();

        if (!$admin->hasRole('admin')) $admin->assignRole('admin');

        $perms = Permission::where('guard_name', 'web')
            ->whereIn('name', self::ADMIN_PERMISSIONS)->get();
        $admin->syncPermissions($perms);

        $this->command->info("    ✅ Admin : {$d['email']} / {$d['password']}");
        return $admin->id;
    }

    // ── Classes ───────────────────────────────────────────────────────────────

    private function seedClasses(array $data, object $school, string $adminId): array
    {
        $ids = [];
        foreach ($data['classes'] as $c) {
            if (!DB::table('class')->where('id', $c['id'])->exists()) {
                DB::table('class')->insert([
                    'id'         => $c['id'],
                    'school_id'  => $school->id,
                    'name'       => $c['name'],
                    'amount'     => $c['amount'],
                    'status'     => 1,
                    'is_delete'  => 0,
                    'created_by' => $adminId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $ids[] = $c['id'];
        }
        $this->command->info("    ✅ " . count($ids) . " classes");
        return $ids;
    }

    // ── Matières → classes ────────────────────────────────────────────────────

    private function seedClassSubjects(array $data, array $classIds, string $adminId): array
    {
        $subjectIds = [];
        foreach ($classIds as $classId) {
            foreach ($data['subjects'] as $s) {
                $exists = DB::table('class_subject')
                    ->where('class_id', $classId)
                    ->where('subject_id', $s['id'])
                    ->exists();
                if (!$exists) {
                    DB::table('class_subject')->insert([
                        'id'          => (string) Str::uuid(),
                        'class_id'    => $classId,
                        'subject_id'  => $s['id'],
                        'coefficient' => $s['coeff'],
                        'status'      => 1,
                        'is_delete'   => 0,
                        'created_by'  => $adminId,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
                if (!in_array($s['id'], $subjectIds)) $subjectIds[] = $s['id'];
            }
        }
        $this->command->info("    ✅ " . count($data['subjects']) . " matières assignées");
        return array_unique($subjectIds);
    }

    // ── Professeurs ───────────────────────────────────────────────────────────

    private function seedTeachers(array $data, object $school, array $classIds, string $adminId, Role $role): array
    {
        $ids = [];
        foreach ($data['teachers'] as $t) {
            $prefix = strtoupper(substr($school->school_code, 0, 3));
            $teacher = User::firstOrCreate(
                ['email' => $t['email']],
                [
                    'id'              => $t['id'],
                    'name'            => $t['name'],
                    'last_name'       => $t['last_name'],
                    'password'        => Hash::make('Prof@1234'),
                    'user_type'       => 2,
                    'status'          => 1,
                    'is_delete'       => 0,
                    'school_id'       => $school->id,
                    'gender'          => $t['gender'],
                    'mobile_number'   => $t['mobile_number'],
                    'date_of_birth'   => $t['date_of_birth'],
                    'blood_group'     => $t['blood_group'],
                    'religion'        => $t['religion'],
                    'qualification'   => $t['qualification'],
                    'work_experience' => $t['work_experience'],
                    'marital_status'  => $t['marital_status'],
                    'address'         => $t['address'],
                    'permanent_address'=> $t['address'],
                    'occupation'      => $t['occupation'],
                    'note'            => $t['note'],
                    'admission_date'  => '2020-09-01',
                ]
            );
            $teacher->school_id = $school->id;
            $teacher->save();

            if (!$teacher->hasRole('teacher')) $teacher->assignRole('teacher');

            $classId = $classIds[$t['class_idx']] ?? $classIds[0];
            if (!DB::table('class_teacher')->where('class_id', $classId)->where('teacher_id', $teacher->id)->exists()) {
                DB::table('class_teacher')->insert([
                    'id'         => (string) Str::uuid(),
                    'class_id'   => $classId,
                    'teacher_id' => $teacher->id,
                    'status'     => 1,
                    'is_delete'  => 0,
                    'created_by' => $adminId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $ids[] = $teacher->id;
        }
        $this->command->info("    ✅ " . count($ids) . " professeurs (Prof@1234)");
        return $ids;
    }

    // ── Apprenants ────────────────────────────────────────────────────────────

    private function seedStudents(array $data, object $school, array $classIds, string $adminId, Role $role): array
    {
        $ids = [];
        $prefix = strtoupper(substr($school->school_code, 0, 3));
        foreach ($data['students'] as $i => $s) {
            $classId = $classIds[$s['class_idx']] ?? $classIds[0];
            $admNum  = $prefix . '-2025-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

            $student = User::firstOrCreate(
                ['email' => $s['email']],
                [
                    'name'             => $s['name'],
                    'last_name'        => $s['last_name'],
                    'password'         => Hash::make('Eleve@1234'),
                    'user_type'        => 3,
                    'status'           => 1,
                    'is_delete'        => 0,
                    'school_id'        => $school->id,
                    'class_id'         => $classId,
                    'gender'           => $s['gender'],
                    'date_of_birth'    => $s['dob'],
                    'blood_group'      => $s['blood_group'],
                    'religion'         => $s['religion'],
                    'admission_number' => $admNum,
                    'roll_number'      => 'ROLL-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'admission_date'   => '2025-09-01',
                    'address'          => $school->address,
                    'permanent_address'=> $school->address,
                    'mobile_number'    => '+229 97 00 ' . str_pad(($i + 1) * 11, 4, '0', STR_PAD_LEFT),
                    'note'             => 'Apprenant inscrit pour l\'année 2025-2026',
                    'marital_status'   => 'Célibataire',
                    'occupation'       => 'Apprenant',
                ]
            );
            $student->school_id = $school->id;
            $student->class_id  = $classId;
            $student->save();

            if (!$student->hasRole('student')) $student->assignRole('student');
            $ids[] = $student->id;
        }
        $this->command->info("    ✅ " . count($ids) . " apprenants (Eleve@1234)");
        return $ids;
    }

    // ── Parents ───────────────────────────────────────────────────────────────

    private function seedParents(array $data, object $school, array $studentIds, string $adminId, Role $role): void
    {
        foreach ($data['parents'] as $p) {
            $parent = User::firstOrCreate(
                ['email' => $p['email']],
                [
                    'name'             => $p['name'],
                    'last_name'        => $p['last_name'],
                    'password'         => Hash::make('Parent@1234'),
                    'user_type'        => 4,
                    'status'           => 1,
                    'is_delete'        => 0,
                    'school_id'        => $school->id,
                    'gender'           => $p['gender'],
                    'mobile_number'    => $p['mobile_number'],
                    'address'          => $school->address,
                    'permanent_address'=> $school->address,
                    'occupation'       => $p['occupation'],
                    'marital_status'   => 'Marié(e)',
                    'note'             => 'Parent ou tuteur légal',
                    'admission_date'   => '2025-09-01',
                ]
            );
            $parent->school_id = $school->id;
            $parent->save();

            if (!$parent->hasRole('parent')) $parent->assignRole('parent');

            if (isset($studentIds[$p['student_idx']])) {
                User::where('id', $studentIds[$p['student_idx']])->update(['parent_id' => $parent->id]);
            }
        }
        $this->command->info("    ✅ " . count($data['parents']) . " parents (Parent@1234)");
    }

    // ── Emploi du temps ───────────────────────────────────────────────────────

    private function seedTimetable(array $classIds, array $subjIds, array $teacherIds, string $schoolId, string $adminId): void
    {
        $weekIds = array_values(WeekSeeder::WEEK_IDS);
        $slots   = [
            ['start' => '07:30', 'end' => '09:30', 'room' => 'Salle A101'],
            ['start' => '09:45', 'end' => '11:45', 'room' => 'Salle A102'],
            ['start' => '12:00', 'end' => '14:00', 'room' => 'Salle B201'],
            ['start' => '14:15', 'end' => '16:15', 'room' => 'Salle B202'],
        ];

        foreach ($classIds as $cIdx => $classId) {
            $teacherId = $teacherIds[$cIdx] ?? $teacherIds[0];
            foreach (array_slice($subjIds, 0, 4) as $sIdx => $subjId) {
                $weekId = $weekIds[$sIdx % count($weekIds)];
                $slot   = $slots[$sIdx % count($slots)];
                if (!DB::table('class_timetable')->where('class_id', $classId)->where('subject_id', $subjId)->where('week_id', $weekId)->exists()) {
                    DB::table('class_timetable')->insert([
                        'id'           => (string) Str::uuid(),
                        'school_id'    => $schoolId,
                        'class_id'     => $classId,
                        'subject_id'   => $subjId,
                        'week_id'      => $weekId,
                        'teacher_id'   => $teacherId,
                        'start_time'   => $slot['start'],
                        'end_time'     => $slot['end'],
                        'room_number'  => $slot['room'],
                        'color'        => '#3b82f6',
                        'session_type' => 'cours',
                        'notes'        => 'Cours hebdomadaire régulier',
                        'is_delete'    => 0,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }
        }
        $this->command->info("    ✅ Emploi du temps créé");
    }
}
