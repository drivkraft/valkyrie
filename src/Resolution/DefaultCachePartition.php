<?php

declare(strict_types=1);

namespace Drivkraft\Valkyrie\Resolution;

use Drivkraft\Valkyrie\Contracts\CachePartition;

/**
 * Single partition; correct whenever one application owns the cache store.
 */
final class DefaultCachePartition implements CachePartition
{
    public function key(): string
    {
        return 'default';
    }
}
