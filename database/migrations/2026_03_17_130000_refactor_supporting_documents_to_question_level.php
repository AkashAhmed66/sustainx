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
        if (!Schema::hasTable('supporting_documents')) {
            return;
        }

        if (!Schema::hasColumn('supporting_documents', 'question_id')) {
            Schema::table('supporting_documents', function (Blueprint $table) {
                $table->foreignId('question_id')
                    ->nullable()
                    ->after('assessment_id')
                    ->constrained('questions')
                    ->onDelete('cascade');
            });
        }

        $this->makeNullableUnsignedForeign('supporting_documents', 'item_id', 'supporting_documents_item_id_foreign');
        $this->makeNullableUnsignedForeign('supporting_documents', 'subsection_id', 'supporting_documents_subsection_id_foreign');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('supporting_documents')) {
            return;
        }

        if (Schema::hasColumn('supporting_documents', 'question_id')) {
            Schema::table('supporting_documents', function (Blueprint $table) {
                $table->dropForeign(['question_id']);
                $table->dropColumn('question_id');
            });
        }
    }

    private function makeNullableUnsignedForeign(string $table, string $column, string $foreignKey): void
    {
        if (!Schema::hasColumn($table, $column)) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            $this->dropForeignKeyIfExists($table, $foreignKey);
            DB::statement("ALTER TABLE {$table} MODIFY {$column} BIGINT UNSIGNED NULL");
            return;
        }

        if ($driver === 'pgsql') {
            $this->dropForeignKeyIfExists($table, $foreignKey);
            DB::statement("ALTER TABLE {$table} ALTER COLUMN {$column} DROP NOT NULL");
        }
    }

    private function dropForeignKeyIfExists(string $table, string $foreignKey): void
    {
        try {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$foreignKey}");
        } catch (\Throwable $e) {
            // Ignore when foreign key does not exist.
        }

        try {
            DB::statement("ALTER TABLE {$table} DROP CONSTRAINT {$foreignKey}");
        } catch (\Throwable $e) {
            // Ignore when constraint does not exist.
        }
    }
};