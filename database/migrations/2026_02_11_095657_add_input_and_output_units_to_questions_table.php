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
            // Rename existing unit column to input_unit
            $table->renameColumn('unit', 'input_unit');
        });
        
        Schema::table('questions', function (Blueprint $table) {
            // Add output_unit column after input_unit
            $table->string('output_unit')->nullable()->after('input_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Drop output_unit column
            $table->dropColumn('output_unit');
        });
        
        Schema::table('questions', function (Blueprint $table) {
            // Rename input_unit back to unit
            $table->renameColumn('input_unit', 'unit');
        });
    }
};
