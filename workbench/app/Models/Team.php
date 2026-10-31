<?php

namespace Workbench\App\Models;

use Drivkraft\Valkyrie\Concerns\IsAccessScope;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name'])]
class Team extends Model
{
    use IsAccessScope, SoftDeletes;
}
