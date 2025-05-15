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
        Schema::table('soutes', function (Blueprint $table) {
            // Supprimer les anciens champs si tu ne les utilises plus
            // $table->dropColumn('type_carburant_principal');
            // $table->dropColumn('capacite_totale');

            // Ajouter les types de carburant que la soute peut contenir (champ JSON)
            $table->json('types_carburants_stockes')->nullable()->after('localisation'); // Stocke un tableau comme ['Diesel', 'Essence']

            // Ajouter des colonnes pour les capacités spécifiques
            $table->decimal('capacite_diesel', 12, 2)->nullable()->after('types_carburants_stockes');
            $table->decimal('capacite_kerozen', 12, 2)->nullable()->after('capacite_diesel');
            $table->decimal('capacite_essence', 12, 2)->nullable()->after('capacite_kerozen');

            // Optionnel: Niveau actuel pour chaque type
            $table->decimal('niveau_actuel_diesel', 12, 2)->nullable()->after('capacite_essence');
            $table->decimal('niveau_actuel_kerozen', 12, 2)->nullable()->after('niveau_actuel_diesel');
            $table->decimal('niveau_actuel_essence', 12, 2)->nullable()->after('niveau_actuel_kerozen');
        });
    }

    public function down(): void
    {
        Schema::table('soutes', function (Blueprint $table) {
            $table->dropColumn([
                'types_carburants_stockes',
                'capacite_diesel', 'capacite_kerozen', 'capacite_essence',
                'niveau_actuel_diesel', 'niveau_actuel_kerozen', 'niveau_actuel_essence'
            ]);
            // Rétablir les anciens champs si tu les avais supprimés
            // $table->string('type_carburant_principal')->nullable();
            // $table->decimal('capacite_totale', 12, 2)->nullable();
        });
    }
};
