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
        Schema::table('personnels', function (Blueprint $table) {
            // Vérifier si la colonne existe avant de tenter de la supprimer
            if (Schema::hasColumn('personnels', 'soute_id')) {
                // IMPORTANT: Si 'soute_id' avait une contrainte de clé étrangère,
                // vous devez d'abord supprimer la contrainte.
                // Le nom de la contrainte est souvent 'nom_table_nom_colonne_foreign'.
                // Exemple: $table->dropForeign('personnels_soute_id_foreign');
                // Si vous n'êtes pas sûr ou si elle n'en avait pas, vous pouvez omettre dropForeign
                // ou le mettre dans un try-catch.
                try {
                    // Tente de supprimer une éventuelle contrainte de clé étrangère
                    // Le nom peut varier, Laravel génère souvent qqch comme personnels_soute_id_foreign
                    // Si vous connaissez le nom exact de la contrainte, utilisez-le.
                    // Sinon, la méthode dropForeign peut prendre un tableau du nom de la colonne.
                    $table->dropForeign(['soute_id']);
                } catch (\Exception $e) {
                    // La contrainte n'existait probablement pas, on peut ignorer l'erreur.
                    // Vous pouvez logguer si vous voulez : \Log::info("Could not drop foreign key for soute_id on personnels: " . $e->getMessage());
                }
                $table->dropColumn('soute_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnels', function (Blueprint $table) {
            //
        });
    }
};
