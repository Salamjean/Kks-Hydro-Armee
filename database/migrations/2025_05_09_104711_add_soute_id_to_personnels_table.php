<?php
// dans database/migrations/xxxx_xx_xx_xxxxxx_add_soute_id_to_personnels_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->foreignId('soute_id')
                  ->nullable() // Un personnel peut être créé avant d'être assigné, ou ne pas être assigné
                  ->after('service_id') // Ou après une autre colonne pertinente
                  ->constrained('soutes')
                  ->onDelete('set null'); // Si la soute est supprimée, le personnel n'est plus assigné
        });
    }

    public function down(): void
    {
        Schema::table('personnels', function (Blueprint $table) {
            // Pour faire marche arrière, il faut d'abord supprimer la contrainte de clé étrangère
            $table->dropForeign(['soute_id']); // Nom de la contrainte par défaut: table_colonne_foreign
            $table->dropColumn('soute_id');
        });
    }
};