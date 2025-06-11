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
            $table->decimal('seuil_alert_diesel', 10, 2)->nullable();
            $table->decimal('seuil_alert_kerozen', 10, 2)->nullable();
            $table->decimal('seuil_alert_essence', 10, 2)->nullable();
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
