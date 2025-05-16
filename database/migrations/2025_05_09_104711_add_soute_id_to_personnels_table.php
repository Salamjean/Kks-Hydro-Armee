<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('soutes', function (Blueprint $table) {
            $table->foreignId('personnel_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('soutes', function (Blueprint $table) {
            $table->dropForeign(['personnel_id']);
            $table->dropColumn('personnel_id');
        });
    }
};