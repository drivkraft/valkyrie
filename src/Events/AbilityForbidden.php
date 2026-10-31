<?php

declare(strict_types=1);

namespace Drivkraft\Valkyrie\Events;

use Drivkraft\Valkyrie\Models\Ability;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

final class AbilityForbidden implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(
        public readonly Model $actor,
        public readonly Ability $ability,
        public readonly ?Model $scope = null,
    ) {}
}
