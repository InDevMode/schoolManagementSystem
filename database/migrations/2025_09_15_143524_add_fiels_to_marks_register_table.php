<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('marks_register', function (Blueprint $table) {
            // Ajouter les champs pour les quizzes (interrogations)
            $table->decimal('quiz_1', 5, 2)->nullable()->after('test_work');
            $table->decimal('quiz_2', 5, 2)->nullable()->after('quiz_1');
            $table->decimal('quiz_3', 5, 2)->nullable()->after('quiz_2');
            $table->decimal('quiz_4', 5, 2)->nullable()->after('quiz_3');
            $table->decimal('quiz_5', 5, 2)->nullable()->after('quiz_4');

            // Ajouter les champs pour les assignments (devoirs)
            $table->decimal('assignment_1', 5, 2)->nullable()->after('quiz_5');
            $table->decimal('assignment_2', 5, 2)->nullable()->after('assignment_1');
            $table->decimal('assignment_3', 5, 2)->nullable()->after('assignment_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marks_register', function (Blueprint $table) {
            // Supprimer les champs en cas de rollback
            $table->dropColumn([
                'quiz_1',
                'quiz_2',
                'quiz_3',
                'quiz_4',
                'quiz_5',
                'assignment_1',
                'assignment_2',
                'assignment_3'
            ]);
        });
    }
};
