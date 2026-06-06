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
        Schema::create('predicciones', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('partido_id');
            $table->unsignedBigInteger('usuario_id');
            
            $table->integer('goles_local');
            $table->integer('goles_visitante');
            $table->boolean('acertado')->default(false);
            
            $table->integer('puntos')->nullable(); 
            
            $table->timestamps();

            $table->foreign('partido_id')->references('id')->on('partidos')->onDelete('restrict');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
            
            // Se puede declarar el índice único aquí mismo, más limpio
            $table->unique(['partido_id', 'usuario_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predicciones');
    }
};