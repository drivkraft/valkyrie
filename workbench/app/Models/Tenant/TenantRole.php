<?php

namespace Workbench\App\Models\Tenant;

use Drivkraft\Valkyrie\Models\Role;

class TenantRole extends Role
{
    protected $connection = 'tenant';
}
