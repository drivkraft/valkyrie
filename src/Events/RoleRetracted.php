<?php

declare(strict_types=1);

namespace Drivkraft\Valkyrie\Events;

use Drivkraft\Valkyrie\Models\Role;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

final class RoleRetracted implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(
        public readonly Model $actor,
        public readonly Role $role,
        public readonly ?Model $scope = null,
    ) {}
}
