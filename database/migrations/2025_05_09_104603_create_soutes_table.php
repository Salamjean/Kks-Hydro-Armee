<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soutes', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Nom de la soute (ex: "Soute Principale Alpha", "Dépôt Sud")
            $table->string('localisation')->nullable();
            $table->foreignId('corps_arme_id')
                  ->constrained('corps_armes')
                  ->onDelete('cascade');
            $table->string('type_carburant_principal')->nullable(); // Ex: "Diesel", "Essence"
            $table->decimal('capacite_totale', 12, 2)->nullable(); // Capacité en litres
            $table->decimal('niveau_actuel_global', 12, 2)->nullable(); // Pour suivi global, si géré à ce niveau
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soutes');
    }
};