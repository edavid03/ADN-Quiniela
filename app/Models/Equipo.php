<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $table = 'equipos';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id', 'name', 'code', 'grupo'];

    public function partidosLocal()
    {
        return $this->hasMany(Partido::class, 'local_id');
    }

    public function partidosVisitante()
    {
        return $this->hasMany(Partido::class, 'visitante_id');
    }

    public function flagEmojiHtml(): string
    {
        $code = $this->fifaFlagCode();

        if ($code === null) {
            return '';
        }

        $name = e($this->name ?? $code);
        $src = e("https://api.fifa.com/api/v3/picture/flags-sq-1/{$code}");

        return '<img src="'.$src.'" alt="Bandera de '.$name.'" class="h-4 w-4 rounded-sm object-cover" loading="lazy" decoding="async">';
    }

    private function fifaFlagCode(): ?string
    {
        $code = strtoupper((string) $this->code);

        return preg_match('/^[A-Z]{3}$/', $code) === 1 ? $code : null;
    }
}
