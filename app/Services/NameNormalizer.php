<?php

namespace App\Services;

class NameNormalizer
{
    public static function normalize(string $name): string
    {
        $name = mb_strtolower(trim($name));
        $name = preg_replace('/\s+/', ' ', $name);
        return $name ?? '';
    }
}
