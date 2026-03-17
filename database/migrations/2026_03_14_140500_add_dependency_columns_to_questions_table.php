<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('depends_on_question_id')
                ->nullable()
                ->after('question_type_id')
                ->constrained('questions')
                ->nullOnDelete();

            $table->foreignId('depends_on_option_id')
                ->nullable()
                ->after('depends_on_question_id')
                ->constrained('options')
                ->nullOnDelete();

            $table->index(['depends_on_question_id', 'depends_on_option_id'], 'questions_dependency_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('questions_dependency_index');
            $table->dropForeign(['depends_on_option_id']);
            $table->dropForeign(['depends_on_question_id']);
            $table->dropColumn(['depends_on_option_id', 'depends_on_question_id']);
        });
    }
};
