<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    use Auditable;

    public const MINUTOS_ANTICIPACION_PRONOSTICO = 60;

    protected $table = 'partidos';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id', 'local_id', 'visitante_id', 'fecha_utc', 'estadio', 'fase', 'goles_local', 'goles_visitante'];
    protected $casts = [
        'fecha_utc' => 'datetime',
    ];

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

    public function fechaCaracas(): Carbon
    {
        return Carbon::parse($this->fecha_utc, 'UTC')->setTimezone('America/Caracas');
    }








    public function fechaLimitePronosticoUtc(): Carbon
    {
        return $this->fecha_utc->copy()->utc()->subMinutes(self::MINUTOS_ANTICIPACION_PRONOSTICO);
    }

    public function aceptaPronosticos(): bool
    {
        return now()->utc()->lessThan($this->fechaLimitePronosticoUtc());
    }

    public function scopeAbiertosParaPronosticos(Builder $query): Builder
    {
        return $query->where(
            'fecha_utc',
            '>',
            now()->utc()->addMinutes(self::MINUTOS_ANTICIPACION_PRONOSTICO)
        );
    }

    public function scopeCerradosParaPronosticos(Builder $query): Builder
    {
        return $query->where(
            'fecha_utc',
            '<=',
            now()->utc()->addMinutes(self::MINUTOS_ANTICIPACION_PRONOSTICO)
        );
    }


    public function finalizarPartido(int $golesLocal, int $golesVisitante): void
    {
        $cambios = [];

        if ($this->goles_local === null || (int) $this->goles_local !== $golesLocal) {
            $cambios['goles_local'] = $golesLocal;
        }

        if ($this->goles_visitante === null || (int) $this->goles_visitante !== $golesVisitante) {
            $cambios['goles_visitante'] = $golesVisitante;
        }

        if ($cambios === []) {
            return;
        }

        $this->fill($cambios);
        $this->save();

        $signoReal = ($golesLocal > $golesVisitante) ? 1 : (($golesLocal < $golesVisitante) ? 2 : 0);

        $this->predicciones()->get()->each(function (Prediccion $prediccion) use ($golesLocal, $golesVisitante, $signoReal) {
            $prediccion->evaluarResultado($golesLocal, $golesVisitante, $signoReal);
        });
    }





}
