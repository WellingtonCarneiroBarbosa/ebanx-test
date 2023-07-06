<?php

namespace App\Models\Concerns;

class MonetaryCast
{
    public function get($model, string $key, $value, array $attributes): int
    {
        return $value * 100;
    }

    public function set($model, string $key, int $value, array $attributes): float
    {
        return $value / 100;
    }
}
