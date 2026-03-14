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
        Schema::table('supporting_documents', function (Blueprint $table) {
            $table->dropForeign(['subsection_id']);
            $table->dropColumn('subsection_id');

            $table->foreignId('item_id')
                ->after('assessment_id')
                ->constrained()
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supporting_documents', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropColumn('item_id');

            $table->foreignId('subsection_id')
                ->after('assessment_id')
                ->constrained()
                ->onDelete('cascade');
        });
    }
};
