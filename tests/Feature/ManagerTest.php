<?php

declare(strict_types=1);

use Drivkraft\Valkyrie\Facades\Valkyrie;
use Drivkraft\Valkyrie\Support\Models;
use Workbench\App\Models\Office;
use Workbench\App\Models\Post;
use Workbench\App\Models\User;

it('finds or creates an ability once', function (): void {
    $first = Valkyrie::defineAbility('edit-posts', title: 'Edit posts');
    $second = Valkyrie::defineAbility('edit-posts');

    expect($second->is($first))->toBeTrue()
        ->and($first->title)->toBe('Edit posts')
        ->and(Models::ability()::query()->count())->toBe(1);
});

it('separates abilities by entity and only_owned', function (): void {
    Valkyrie::defineAbility('edit');
    Valkyrie::defineAbility('edit', Post::class);
    Valkyrie::defineAbility('edit', Post::class, onlyOwned: true);

    expect(Models::ability()::query()->count())->toBe(3);
});

it('finds or creates a role once', function (): void {
    $first = Valkyrie::defineRole('editor', 'Editor');
    $second = Valkyrie::defineRole('editor');

    expect($second->is($first))->toBeTrue()
        ->and($first->title)->toBe('Editor')
        ->and($first->isScoped())->toBeFalse();
});

it('creates scoped role definitions apart from global ones', function (): void {
    $office = Office::query()->create(['name' => 'HQ']);
    $global = Valkyrie::defineRole('editor');
    $scoped = Valkyrie::defineRole('editor', on: $office);

    expect($scoped->is($global))->toBeFalse()
        ->and($scoped->isScoped())->toBeTrue()
        ->and($scoped->scope->is($office))->toBeTrue();
});

it('assigns and retracts for multiple actors at once', function (): void {
    [$a, $b] = User::factory()->count(2)->create();

    Valkyrie::assign('editor', [$a, $b]);
    expect($a->isA('editor'))->toBeTrue()
        ->and($b->isA('editor'))->toBeTrue();

    Valkyrie::retract('editor', [$a, $b]);
    expect($a->isNotA('editor'))->toBeTrue()
        ->and($b->isNotA('editor'))->toBeTrue();
});

it('reports hasRole globally and per scope', function (): void {
    $user = User::factory()->create();
    $office = Office::query()->create(['name' => 'HQ']);
    $user->assign('editor', $office);

    expect(Valkyrie::hasRole($user, 'editor'))->toBeTrue()
        ->and(Valkyrie::hasRole($user, 'editor', on: $office))->toBeTrue()
        ->and(Valkyrie::hasRole($user, 'other'))->toBeFalse()
        ->and(Valkyrie::hasGlobalRole($user, 'editor'))->toBeFalse();
});

it('retracting an unknown role is a no-op', function (): void {
    $user = User::factory()->create();

    Valkyrie::retract('missing', $user);

    expect(Models::assignedRole()::query()->count())->toBe(0);
});
