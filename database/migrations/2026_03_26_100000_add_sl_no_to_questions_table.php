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
            if (!Schema::hasColumn('questions', 'sl_no')) {
                $table->unsignedInteger('sl_no')->nullable()->after('id');
                $table->unique('sl_no', 'questions_sl_no_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'sl_no')) {
                $table->dropUnique('questions_sl_no_unique');
                $table->dropColumn('sl_no');
            }
        });
    }
};