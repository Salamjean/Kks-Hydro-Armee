<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personnel_soute', function (Blueprint $table) {
            // Le nom de la contrainte unique est souvent nomTable_col1_col2_unique
            // par ex: personnel_soute_personnel_id_soute_id_unique
            // Vous devrez peut-être trouver le nom exact dans votre base de données
            // ou essayer avec le tableau des colonnes.
            $table->dropUnique(['personnel_id', 'soute_id']);
            // Ou, si vous connaissez le nom exact :
            // $table->dropUnique('personnel_soute_personnel_id_soute_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('personnel_soute', function (Blueprint $table) {
            // Pour recréer la contrainte si besoin de rollback
            $table->unique(['personnel_id', 'soute_id']);
        });
    }
};