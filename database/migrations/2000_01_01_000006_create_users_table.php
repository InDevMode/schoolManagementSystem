<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table users — version consolidée avec toutes les colonnes finales.
 * Fusionne toutes les migrations add_*_to_users_table en un seul fichier.
 *
 * user_type : 0 = super_admin, 1 = admin, 2 = teacher, 3 = student, 4 = parent
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // ── Identité ────────────────────────────────────────────────────
            $table->id();
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // ── Type & accès ────────────────────────────────────────────────
            $table->tinyInteger('user_type')->default(1)
                  ->comment('0:super_admin 1:admin 2:teacher 3:student 4:parent');
            $table->tinyInteger('status')->default(0)->comment('0: Inactive, 1: Active');
            $table->tinyInteger('must_change_password')->default(0)
                  ->comment('1 = doit changer son MDP à la prochaine connexion');
            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');

            // ── Multi-tenant ────────────────────────────────────────────────
            $table->unsignedBigInteger('school_id')->nullable()
                  ->comment('FK → schools.id — null pour le super admin');

            // ── Profil personnel ────────────────────────────────────────────
            $table->string('profile_picture')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('religion')->nullable();
            $table->string('caste')->nullable();
            $table->string('mobile_number')->nullable();
            $table->text('address')->nullable();
            $table->string('permanent_address')->nullable();
            $table->text('marital_status')->nullable();

            // ── Académique / scolaire ───────────────────────────────────────
            $table->string('admission_number')->nullable();
            $table->date('admission_date')->nullable();
            $table->string('roll_number')->nullable();
            $table->unsignedBigInteger('class_id')->nullable()
                  ->comment('Pour les étudiants — FK vers class.id');
            $table->unsignedBigInteger('parent_id')->nullable()
                  ->comment('Pour les étudiants — FK vers users.id (parent)');

            // ── Professionnel / RH ──────────────────────────────────────────
            $table->string('occupation')->nullable();
            $table->string('qualification')->nullable();
            $table->string('work_experience')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('note')->nullable();

            // ── Auth sociale ────────────────────────────────────────────────
            $table->string('provider')->nullable()->comment('google, facebook, ...');
            $table->string('provider_id')->nullable();

            // ── Audit ───────────────────────────────────────────────────────
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('last_login')->nullable();

            $table->timestamps();

            // ── Index ────────────────────────────────────────────────────────
            $table->index('school_id');
            $table->index('user_type');
            $table->index(['school_id', 'user_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
