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
        Schema::create('carburants', function (Blueprint $table) {
            $table->id();
            $table->string('type_carburant'); // Ex: "Diesel", "Essence SP95"
            $table->decimal('quantite', 10, 2); // Quantité distribuée (ex: 100.50 litres)
            // Immatriculation du véhicule qui reçoit (très utile !)
            $table->string('vehicule_receveur_immat')->nullable();
            // Kilométrage au moment de la prise (utile pour suivi conso/km)
            $table->integer('kilometrage_receveur')->unsigned()->nullable();
            $table->timestamp('date_transaction')->useCurrent(); // Date et heure
            // Clé étrangère vers le corps d'armée (pour reporting global)
            $table->foreignId('corps_arme_id')
                  ->constrained('corps_armes')
                  ->onDelete('cascade');
             // Clé étrangère vers le personnel qui a fait la distribution
             $table->foreignId('personnel_id')
                   ->constrained('personnels')
                   ->onDelete('cascade'); // Ou restrict/set null selon logique métier
             // Clé étrangère vers le distributeur utilisé
            //$table->foreignId('distributeur_id')->constrained('distributeurs')->onDelete('cascade'); // Ou restrict/set null
            $table->text('notes')->nullable(); // Pour ajouter des commentaires
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carburants');
    }
};