<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depotages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soute_id')->constrained('soutes')->onDelete('cascade'); // Soute ravitaillée
            $table->foreignId('personnel_id')->constrained('personnels')->onDelete('cascade'); // Pompiste/Opérateur qui a fait le dépotage

            // Informations Générales
            $table->date('date_depotage');
            $table->time('heure_depotage');
            $table->string('nom_operateur'); // Qui est sur le site, peut être différent du pompiste connecté

            // Informations sur le Transporteur
            $table->string('nom_societe_transporteur');
            $table->string('nom_chauffeur_transporteur');
            $table->string('immatriculation_vehicule_transporteur');

            // Informations sur le Dépôt (la livraison elle-même)
            $table->string('produit'); // 'essence', 'kerozen', 'diesel'
            $table->decimal('volume_transporte_l', 10, 2)->comment('Volume indiqué par le transporteur');
            $table->string('numero_bon_livraison')->nullable();

            // Informations sur la Cuve de Réception (Soute)
            $table->decimal('niveau_avant_depotage_l', 10, 2)->comment('Niveau dans la soute avant ce dépotage spécifique');
            $table->decimal('volume_recu_l', 10, 2)->comment('Volume réellement mesuré comme reçu dans la soute');
            // Optionnel: $table->decimal('niveau_apres_depotage_l', 10, 2); (peut être calculé ou stocké)
            // Optionnel: $table->decimal('ecart_l', 8, 2)->nullable()->comment('Différence entre volume_transporte et volume_recu');

            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depotages');
    }
};