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
    Schema::create('distributions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('personnel_id')->constrained('personnels')->onDelete('cascade');
        $table->foreignId('soute_id')->constrained('soutes')->onDelete('cascade');
        $table->string('nom_chauffeur');
        $table->string('immatriculation_vehicule');
        $table->enum('type_carburant', ['essence', 'diesel', 'kerozen']);
        $table->decimal('quantite', 10, 2);
        $table->date('date_depotage');
        $table->time('heure_depotage');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};
