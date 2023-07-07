<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (! $model->uuid) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    public static function findByUuid(string $uuid, bool $onlyCheckIfExists = false, bool $throws = true, string $column = 'uuid'): self|bool|null
    {
        $baseQuery = self::query()
            ->where($column, $uuid);

        if ($onlyCheckIfExists) {
            $exists = $baseQuery->exists();

            if ($throws && ! $exists) {
                throw new \InvalidArgumentException('UUID already exists');
            }

            return $exists;
        }

        if (! $throws) {
            return $baseQuery->first();
        }

        return $baseQuery->firstOrFail();
    }
}
