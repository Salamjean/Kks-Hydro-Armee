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
        Schema::create('distributeurs', function (Blueprint $table) {
            $table->id();
            // Identifiant unique de la pompe ou immatriculation du camion
            $table->string('identifiant')->unique();
            // Type de distributeur ('Pompe Fixe', 'Camion Citerne', etc.)
            $table->string('type');
            // Capacité (optionnel, en litres)
            $table->decimal('capacite', 10, 2)->nullable();
             // Niveau actuel (pour suivi inventaire, idée d'amélioration)
            $table->decimal('niveau_actuel', 10, 2)->nullable();
            // Clé étrangère vers services (le distributeur appartient à un service)
            $table->foreignId('service_id')
                  ->constrained('services')
                  ->onDelete('cascade');
            // On pourrait ajouter une clé étrangère vers le chauffeur principal assigné (Personnel)
            // $table->foreignId('personnel_assigne_id')->nullable()->constrained('personnels')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributeurs');
    }
};