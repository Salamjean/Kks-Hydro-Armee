<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('soutes', function (Blueprint $table) {
            $table->decimal('seuil_indisponibilite_diesel', 10, 2)->nullable()->after('seuil_alert_diesel');
            $table->decimal('seuil_indisponibilite_kerozen', 10, 2)->nullable()->after('seuil_alert_kerozen');
            $table->decimal('seuil_indisponibilite_essence', 10, 2)->nullable()->after('seuil_alert_essence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('soutes', function (Blueprint $table) {
        $table->dropColumn([
            'seuil_indisponibilite_diesel',
            'seuil_indisponibilite_kerozen',
            'seuil_indisponibilite_essence'
        ]);
    });
}
};
