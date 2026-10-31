<?php

namespace Workbench\App\Models;

use Drivkraft\Valkyrie\Contracts\ScopedEntity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'office_id', 'user_id'])]
class Report extends Model implements ScopedEntity
{
    public function accessScope(): ?Model
    {
        return $this->office;
    }

    /** @return BelongsTo<Office, $this> */
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }
}
