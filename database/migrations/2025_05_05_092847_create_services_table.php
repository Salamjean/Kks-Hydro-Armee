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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Nom du service (ex: "Dépôt Nord", "Transport Rapide")
            $table->string('localisation'); // Lieu physique du service
            // Clé étrangère vers corps_armes
            $table->foreignId('corps_arme_id')
                  ->constrained('corps_armes') // Fait référence à la table 'corps_armes'
                  ->onDelete('cascade'); // Si le corps est supprimé, ses services aussi
            $table->timestamps(); // created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};