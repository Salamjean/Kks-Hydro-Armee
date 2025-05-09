<?php
// dans database/migrations/xxxx_xx_xx_xxxxxx_add_matricule_soute_to_soutes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soutes', function (Blueprint $table) {
            $table->string('matricule_soute')
                  ->unique() // Le matricule doit être unique
                  ->after('nom') // Positionne-le après le nom, par exemple
                  ->nullable(); // On le générera, mais il pourrait être nullable au début
        });
    }

    public function down(): void
    {
        Schema::table('soutes', function (Blueprint $table) {
            $table->dropColumn('matricule_soute');
        });
    }
};