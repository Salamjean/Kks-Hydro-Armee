<?php
// dans database/migrations/xxxx_xx_xx_xxxxxx_add_password_to_personnels_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->string('password')
                  ->after('email') // Ou une autre position pertinente
                  ->nullable(); // Nullable car le mot de passe sera défini par le personnel lors de sa première connexion
            $table->rememberToken()->after('password'); // Optionnel mais utile pour "Se souvenir de moi"
        });
    }

    public function down(): void
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->dropColumn(['password', 'remember_token']); // Supprime les deux si rememberToken a été ajouté
        });
    }
};