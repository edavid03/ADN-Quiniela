<?php

namespace App\Models\Concerns;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    /**
     * @var list<string>
     */
    private static array $auditExcludedAttributes = [
        'password',
        'remember_token',
        'admin_key',
        'created_at',
        'updated_at',
    ];

    public static function bootAuditable(): void
    {
        static::created(function (Model $model): void {
            $model->writeAudit('created', null, $model->auditValues($model->getAttributes()));
        });

        static::updated(function (Model $model): void {
            $changes = $model->auditValues($model->getChanges());

            if ($changes === []) {
                return;
            }

            $oldValues = [];

            foreach (array_keys($changes) as $attribute) {
                $oldValues[$attribute] = $model->getRawOriginal($attribute);
            }

            $model->writeAudit('updated', $oldValues, $changes);
        });

        static::deleted(function (Model $model): void {
            $model->writeAudit('deleted', $model->auditValues($model->getAttributes()), null);
        });
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function auditValues(array $values): array
    {
        return array_diff_key($values, array_flip(self::$auditExcludedAttributes));
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    private function writeAudit(string $action, ?array $oldValues, ?array $newValues): void
    {
        $actor = auth()->user();
        $request = app()->bound('request') && request()->route() !== null ? request() : null;

        Auditoria::query()->create([
            'actor_id' => $actor?->getAuthIdentifier(),
            'actor_name' => $actor?->username ?? $actor?->name,
            'actor_type' => $actor !== null ? 'usuario' : ($request !== null ? 'invitado' : 'sistema'),
            'action' => $action,
            'table_name' => $this->getTable(),
            'record_id' => (string) $this->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
