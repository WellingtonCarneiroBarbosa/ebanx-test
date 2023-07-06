<?php

namespace App\Models\Concerns;

class TitleCast
{
    public function get($model, string $key, string $value, array $attributes): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    public function set($model, string $key, string $value, array $attributes): string
    {
        return mb_strtolower($value, 'UTF-8');
    }
}
