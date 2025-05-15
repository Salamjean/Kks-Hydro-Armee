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
    Schema::create('personnel_soute', function (Blueprint $table) {
        $table->id();
        $table->foreignId('personnel_id')
              ->constrained('personnels')
              ->onDelete('cascade');
        $table->foreignId('soute_id')
              ->constrained('soutes')
              ->onDelete('cascade');
        $table->timestamps();
      
        $table->unique(['personnel_id','soute_id']); // pour éviter les doublons
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel_soute');
    }
};
