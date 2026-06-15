<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Prediccion extends Model
{
    use Auditable;

    protected $table = 'predicciones';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id', 'partido_id', 'usuario_id', 'goles_local', 'goles_visitante', 'acertado', 'puntos'];
    protected $casts = [
        'partido_id' => 'integer',
        'usuario_id' => 'integer',
        'goles_local' => 'integer',
        'goles_visitante' => 'integer',
        'acertado' => 'boolean',
        'puntos' => 'integer',
    ];

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

        if (! $partido->aceptaPronosticos()) {
            return 'El plazo para registrar apuestas ha cerrado.';
        }

        $prediccion = self::firstOrNew([
            'partido_id' => $partidoId,
            'usuario_id' => $usuarioId,
        ]);

        $prediccion->fill([
            'goles_local' => $golesLocal,
            'goles_visitante' => $golesVisitante,
        ]);

        if ($prediccion->exists && ! $prediccion->isDirty(['goles_local', 'goles_visitante'])) {
            return $prediccion;
        }

        $prediccion->fill([
            'puntos' => null,
            'acertado' => false,
        ])->save();

        return $prediccion;
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

        $this->fill([
            'puntos' => $puntosObtenidos,
            'acertado' => $marcadorAcertado,
        ]);

        if ($this->isDirty(['puntos', 'acertado'])) {
            $this->save();
        }
    }


}
