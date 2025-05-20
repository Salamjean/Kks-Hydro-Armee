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
            if (Schema::hasColumn('soutes', 'personnel_id')) {
                // Assurez-vous de supprimer la contrainte de clé étrangère d'abord si elle existe
                // Le nom peut être 'soutes_personnel_id_foreign' ou similaire
                try {
                    $table->dropForeign(['personnel_id']);
                } catch (\Exception $e) {
                    // Peut échouer si la contrainte n'existe pas, c'est ok
                }
                $table->dropColumn('personnel_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soutes', function (Blueprint $table) {
            //
        });
    }
};
