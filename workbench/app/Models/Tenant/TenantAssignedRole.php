<?php

namespace Workbench\App\Models\Tenant;

use Drivkraft\Valkyrie\Models\AssignedRole;

class TenantAssignedRole extends AssignedRole
{
    protected $connection = 'tenant';
}
