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
        if (!Schema::hasColumn('questions', 'subsection_id')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->foreignId('subsection_id')
                    ->nullable()
                    ->after('item_id')
                    ->constrained('subsections')
                    ->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('questions', 'is_main_question')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->boolean('is_main_question')->default(false)->after('question_type_id');
            });
        }

        if (Schema::hasColumn('questions', 'item_id') && Schema::hasTable('items')) {
            DB::table('questions')
                ->join('items', 'questions.item_id', '=', 'items.id')
                ->whereNull('questions.subsection_id')
                ->update([
                    'questions.subsection_id' => DB::raw('items.subsection_id'),
                ]);
        }

        $this->makeNullableUnsignedForeign('questions', 'item_id', 'questions_item_id_foreign');
        $this->makeNullableUnsignedForeign('answers', 'item_id', 'answers_item_id_foreign');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('questions', 'is_main_question')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropColumn('is_main_question');
            });
        }

        if (Schema::hasColumn('questions', 'subsection_id')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('subsection_id');
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
