<?php

namespace Workbench\App\Models;

use Drivkraft\Valkyrie\Concerns\IsAccessScope;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name'])]
class Office extends Model
{
    use IsAccessScope;
}
