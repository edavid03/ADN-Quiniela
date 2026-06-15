<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class Auditoria extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'auditorias';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Los registros de auditoria no se pueden modificar.'));
        static::deleting(fn () => throw new LogicException('Los registros de auditoria no se pueden eliminar.'));
    }
}
