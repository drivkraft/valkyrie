<?php

declare(strict_types=1);

use Drivkraft\Valkyrie\Models\Ability;
use Drivkraft\Valkyrie\Models\Role;
use Drivkraft\Valkyrie\Support\Morph;
use Illuminate\Database\ClassMorphViolationException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Workbench\App\Models\Office;
use Workbench\App\Models\Post;

beforeEach(function (): void {
    $this->morphMap = Relation::morphMap();
    $this->requiresMorphMap = Relation::requiresMorphMap();
});

afterEach(function (): void {
    Relation::morphMap($this->morphMap, false);
    Relation::requireMorphMap($this->requiresMorphMap);
});

it('returns the morph alias for a mapped class string', function (): void {
    Relation::morphMap(['post' => Post::class]);

    expect(Morph::type(Post::class))->toBe('post')
        ->and(Morph::type(Post::class))->toBe((new Post)->getMorphClass());
});

it('returns the class name for an unmapped class string', function (): void {
    expect(Morph::type(Office::class))->toBe(Office::class);
});

it('returns the morph class and key of an instance', function (): void {
    Relation::morphMap(['post' => Post::class]);
    $post = new Post;
    $post->setAttribute('id', 7);

    expect(Morph::type($post))->toBe('post')
        ->and(Morph::key($post))->toBe(7);
});

it('passes the wildcard through and resolves everything else to null', function (): void {
    expect(Morph::type(Morph::Everything))->toBe('*')
        ->and(Morph::type(null))->toBeNull()
        ->and(Morph::type('not-a-model'))->toBeNull()
        ->and(Morph::key(Post::class))->toBeNull()
        ->and(Morph::key(null))->toBeNull();
});

it('throws for an unmapped class string when the morph map is enforced', function (): void {
    Relation::enforceMorphMap(['post' => Post::class]);

    Morph::type(Office::class);
})->throws(ClassMorphViolationException::class);

it('uses the configured aliases for the package models', function (): void {
    expect(Morph::type(Role::class))->toBe('role')
        ->and(Morph::type(Ability::class))->toBe('ability');
});
