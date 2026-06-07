<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    protected $table = 'partidos';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id', 'local_id', 'visitante_id', 'fecha_utc', 'estadio', 'fase', 'goles_local', 'goles_visitante'];

    public function local()
    {
        return $this->belongsTo(Equipo::class, 'local_id');
    }

    public function visitante()
    {
        return $this->belongsTo(Equipo::class, 'visitante_id');
    }

    public function predicciones()
    {
        return $this->hasMany(Prediccion::class);
    }









    public static function fechaLimiteApuestasUtc(): ?Carbon
    {
        $primeraFecha = static::query()->min('fecha_utc');

        if ($primeraFecha === null) {
            return null;
        }

        return Carbon::parse($primeraFecha)->subMinutes(30);
    }



    public function finalizarPartido(int $golesLocal, int $golesVisitante): void
    {   
        $this->update([
            'goles_local' => $golesLocal,
            'goles_visitante' => $golesVisitante
        ]);

        $signoReal = ($golesLocal > $golesVisitante) ? 1 : (($golesLocal < $golesVisitante) ? 2 : 0);

        $this->predicciones()->get()->each(function (Prediccion $prediccion) use ($golesLocal, $golesVisitante, $signoReal) {
            $prediccion->evaluarResultado($golesLocal, $golesVisitante, $signoReal);
        });



    }





}
