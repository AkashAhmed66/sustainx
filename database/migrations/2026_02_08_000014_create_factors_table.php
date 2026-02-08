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
        Schema::create('factors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equation_id')->constrained('equations')->onDelete('cascade');
            $table->integer('sn'); // serial number (1, 2, 3...)
            $table->string('operation'); // multiply, add, subtract, divide
            $table->decimal('factor_value', 15, 4);
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factors');
    }
};
