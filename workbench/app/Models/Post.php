<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'user_id', 'author_id', 'owner_token'])]
class Post extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'owner_token' => 'encrypted',
        ];
    }

    /**
     * An accessor-only attribute with no backing column.
     *
     * @return Attribute<mixed, never>
     */
    protected function ownerKey(): Attribute
    {
        return Attribute::get(fn (mixed $value, array $attributes): mixed => $attributes['author_id'] ?? null);
    }
}
