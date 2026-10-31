<?php

namespace Workbench\App\Models\Tenant;

use Drivkraft\Valkyrie\Models\Permission;

class TenantPermission extends Permission
{
    protected $connection = 'tenant';
}
