<?php

declare(strict_types = 1);

namespace App;

trait CacheTrait
{
    protected function getCacheKey(string|int ...$keys) : string
    {
        return preg_replace('/[^a-z0-9_]+/', '-', strtolower(implode(' ', $keys)));
    }
}
