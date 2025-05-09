<?php
// dans database/migrations/xxxx_xx_xx_xxxxxx_change_service_id_to_soute_id_in_distributeurs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distributeurs', function (Blueprint $table) {
            // 1. Supprimer l'ancienne clé étrangère et la colonne service_id
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');

            // 2. Ajouter la nouvelle colonne soute_id et sa clé étrangère
            $table->foreignId('soute_id')
                  ->after('capacite') // Ou une autre position
                  ->constrained('soutes')
                  ->onDelete('cascade'); // Si la soute est supprimée, ses distributeurs aussi
        });
    }

    public function down(): void
    {
        Schema::table('distributeurs', function (Blueprint $table) {
            // Inverser les opérations pour le rollback
            $table->dropForeign(['soute_id']);
            $table->dropColumn('soute_id');

            $table->foreignId('service_id')
                  ->nullable() // Rétablir comme c'était (ou selon ta définition initiale)
                  ->constrained('services')
                  ->onDelete('cascade');
        });
    }
};