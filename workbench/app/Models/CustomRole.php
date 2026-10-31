<?php

namespace Workbench\App\Models;

use Drivkraft\Valkyrie\Models\Role;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Workbench\App\Observers\CustomRoleObserver;

#[ObservedBy([CustomRoleObserver::class])]
class CustomRole extends Role
{
    public function isSystemRole(): bool
    {
        return str_starts_with($this->name, 'system-');
    }
}
