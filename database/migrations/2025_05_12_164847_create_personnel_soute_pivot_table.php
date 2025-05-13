<?php

// database/migrations/xxxx_create_personnel_soute_pivot_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('personnel_soute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personnel_id')->constrained('personnels')->onDelete('cascade');
            $table->foreignId('soute_id')->constrained('soutes')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['personnel_id', 'soute_id']); // Évite les doublons
        });
    }
    public function down(): void {
        Schema::dropIfExists('personnel_soute');
    }
};