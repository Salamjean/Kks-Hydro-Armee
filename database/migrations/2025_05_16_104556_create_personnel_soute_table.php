<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personnel_soute', function (Blueprint $table) {
            $table->id(); // Clé primaire pour la table pivot elle-même
            $table->foreignId('personnel_id')->constrained('personnels')->onDelete('cascade');
            $table->foreignId('soute_id')->constrained('soutes')->onDelete('cascade');
            $table->timestamps(); // Optionnel, mais souvent utile

            // Important pour éviter les doublons d'assignation
            //$table->unique(['personnel_id', 'soute_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personnel_soute');
    }
};