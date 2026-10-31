<?php

namespace Workbench\App\Models\Tenant;

use Drivkraft\Valkyrie\Models\Ability;

class TenantAbility extends Ability
{
    protected $connection = 'tenant';
}
