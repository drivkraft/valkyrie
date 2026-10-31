<?php

declare(strict_types=1);

namespace Drivkraft\Valkyrie\Tests\Fakes;

use Drivkraft\Valkyrie\Contracts\CachePartition;

final class FakeCachePartition implements CachePartition
{
    public function __construct(public string $key = 'default') {}

    public function key(): string
    {
        return $this->key;
    }
}
