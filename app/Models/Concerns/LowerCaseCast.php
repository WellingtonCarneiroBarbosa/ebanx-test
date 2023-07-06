<?php

namespace App\Models\Concerns;

class LowerCaseCast
{
    public function get($model, string $key, string $value, array $attributes): string
    {
        return $value;
    }

    public function set($model, string $key, string $value, array $attributes): string
    {
        return mb_strtolower($value, 'UTF-8');
    }
}
