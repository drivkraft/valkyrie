<?php

namespace Workbench\App\Models;

use Drivkraft\Valkyrie\Concerns\HasRolesAndAbilities;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name'])]
class Member extends Model
{
    use HasRolesAndAbilities, SoftDeletes;
}
