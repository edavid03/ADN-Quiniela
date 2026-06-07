<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Prediccion extends Model
{
    protected $table = 'predicciones';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id', 'partido_id', 'usuario_id', 'goles_local', 'goles_visitante', 'acertado', 'puntos'];

    public function partido()
    {
        return $this->belongsTo(Partido::class, 'partido_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }








    public static function registrarApuesta(
        int $usuarioId,
        int $partidoId,
        int $golesLocal,
        int $golesVisitante
    ): EloquentModel|string
    {
        $partido = Partido::find($partidoId);

        if ($partido === null) {
            return 'Partido no encontrado.';
        }

        $limite = Partido::fechaLimiteApuestasUtc();

        if ($limite !== null && now()->utc()->greaterThanOrEqualTo($limite)) {
            return 'El plazo para registrar apuestas ha cerrado.';
        }

        return self::updateOrCreate(
            [
                'partido_id' => $partidoId,
                'usuario_id' => $usuarioId,
            ],
            [
                'goles_local' => $golesLocal,
                'goles_visitante' => $golesVisitante,
                'puntos' => null, 
                'acertado' => false
            ]
        );

    }

        public function evaluarResultado(
            int $rLocal,
            int $rVis,
            int $signoReal
        ): void
        {
            $pLocal = $this->goles_local;
            $pVis = $this->goles_visitante;

            $puntosObtenidos = 0;
            $marcadorAcertado = false;

            if($pLocal === $rLocal && $pVis === $rVis)
            {
                $marcadorAcertado = true;
                $puntosObtenidos = 3;
            }else {
                $signoPredicho = ($pLocal > $pVis) ? 1 : (($pLocal < $pVis) ? 2 : 0);
                if ($signoReal === $signoPredicho) {
                    $puntosObtenidos = 1;
            }

        }

        $this->update([
            'puntos' => $puntosObtenidos,
            'acertado' => $marcadorAcertado
        ]);

 
    }


}
