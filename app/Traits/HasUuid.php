<?php

namespace App\Traits;


trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    // ✅ Do NOT override getKeyName() — leave id as primary key
    // UUID is only used for route model binding

    public function getRouteKeyName(): string
    {
        return 'uuid'; // only affects route binding, not Sanctum
    }
}