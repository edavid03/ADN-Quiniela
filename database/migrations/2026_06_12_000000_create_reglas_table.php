<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reglas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('contenido');
            $table->unsignedInteger('orden')->default(0);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        DB::table('reglas')->insert([
            [
                'titulo' => 'Marcador exacto',
                'contenido' => 'Obtienes 3 puntos cuando aciertas los goles de ambos equipos.',
                'orden' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Resultado correcto',
                'contenido' => 'Obtienes 1 punto cuando aciertas al ganador o el empate, aunque el marcador no sea exacto.',
                'orden' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Cierre de pronosticos',
                'contenido' => 'Cada pronostico debe registrarse antes de su hora de cierre. Despues del cierre no podra modificarse.',
                'orden' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('reglas');
    }
};
