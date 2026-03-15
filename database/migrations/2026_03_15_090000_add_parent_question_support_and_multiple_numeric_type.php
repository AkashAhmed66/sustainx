<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('parent_question_id')
                ->nullable()
                ->after('id')
                ->constrained('questions')
                ->cascadeOnDelete();

            $table->unsignedInteger('child_order_no')
                ->nullable()
                ->after('parent_question_id');

            $table->index(['parent_question_id', 'child_order_no'], 'questions_parent_order_index');
        });

        DB::table('question_types')->updateOrInsert(
            ['id' => 4],
            [
                'name' => 'multiple_numeric',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('questions_parent_order_index');
            $table->dropForeign(['parent_question_id']);
            $table->dropColumn(['parent_question_id', 'child_order_no']);
        });
    }
};
