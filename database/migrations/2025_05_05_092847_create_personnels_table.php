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
        Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique()->nullable(); // Email unique, peut être null si non requis
            $table->string('matricule')->unique(); // Matricule unique
            // Clé étrangère vers corps_armes
            $table->foreignId('corps_arme_id')
                  ->constrained('corps_armes')
                  ->onDelete('cascade');
            // Clé étrangère vers services (un personnel peut appartenir à un service)
             $table->foreignId('service_id')
                   ->nullable() // Peut être assigné plus tard ou pas du tout
                   ->constrained('services')
                   ->onDelete('set null'); // Si le service est supprimé, met l'ID à null
            // Clé étrangère vers distributeurs (pompiste assigné à une pompe/véhicule ?)
            // C'est discutable, peut-être pas nécessaire ici si géré par transaction
            // $table->foreignId('distributeur_id')->nullable()->constrained('distributeurs')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnels');
    }
};