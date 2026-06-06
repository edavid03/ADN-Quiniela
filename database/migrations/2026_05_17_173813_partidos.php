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
        Schema::create('partidos', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedInteger('local_id');
            $table->unsignedInteger('visitante_id');
            $table->dateTime('fecha_utc');
            $table->string('estadio')->nullable();
            $table->string('fase', 30)->default('Grupos'); 
            $table->integer('goles_local')->nullable();
            $table->integer('goles_visitante')->nullable();
            $table->timestamps();

            $table->foreign('local_id')->references('id')->on('equipos');
            $table->foreign('visitante_id')->references('id')->on('equipos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidos');
    }
};
