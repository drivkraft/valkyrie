<?php

declare(strict_types=1);

namespace Drivkraft\Valkyrie;

use Closure;
use Drivkraft\Valkyrie\Assignments\RoleAssignment;
use Drivkraft\Valkyrie\Grants\GrantBuilder;
use Drivkraft\Valkyrie\Grants\GrantMode;
use Drivkraft\Valkyrie\Models\Ability;
use Drivkraft\Valkyrie\Models\AssignedRole;
use Drivkraft\Valkyrie\Models\Role;
use Drivkraft\Valkyrie\Ownership\OwnershipResolver;
use Drivkraft\Valkyrie\Resolution\AbilityCache;
use Drivkraft\Valkyrie\Resolution\AbilityResolver;
use Drivkraft\Valkyrie\Support\Models;
use Drivkraft\Valkyrie\Support\Morph;
use Drivkraft\Valkyrie\Support\ScopeConstraint;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Entry point behind the Valkyrie facade; every write goes through here or its builders.
 */
#[Singleton]
final class ValkyrieManager
{
    public function __construct(
        private readonly AbilityResolver $resolver,
        private readonly AbilityCache $cache,
        private readonly OwnershipResolver $ownership,
    ) {}

    /**
     * Start an allow for a user, service account or role, optionally inside one scope.
     */
    public function allow(Model $actor, ?Model $on = null): GrantBuilder
    {
        return new GrantBuilder(GrantMode::Allow, $actor, $on, $this);
    }

    /**
     * Give one or more actors a role, globally or inside one scope. Idempotent.
     *
     * @param  Model|list<Model>  $actors
     */
    public function assign(string|Role $role, Model|array $actors, ?Model $on = null): void
    {
        $this->assignments()->assign($role, $actors instanceof Model ? [$actors] : $actors, $on);
    }

    /**
     * @param  Model|class-string<Model>|null  $entity
     */
    public function can(Model $actor, string $ability, Model|string|null $entity = null, ?Model $on = null): bool
    {
        return $this->check($actor, $ability, $entity, $on) === true;
    }

    /**
     * @param  Model|class-string<Model>|null  $entity
     */
    public function cannot(Model $actor, string $ability, Model|string|null $entity = null, ?Model $on = null): bool
    {
        return ! $this->can($actor, $ability, $entity, $on);
    }

    /**
     * Raw resolution: true, false, or null when Valkyrie has no opinion.
     *
     * @param  Model|class-string<Model>|null  $entity
     */
    public function check(Model $actor, string $ability, Model|string|null $entity = null, ?Model $on = null): ?bool
    {
        return $this->resolver->check($actor, $ability, $entity, $on);
    }

    /**
     * Find or create an ability; names are free-form.
     *
     * @param  Model|class-string<Model>|string|null  $entity
     */
    public function defineAbility(string $name, Model|string|null $entity = null, bool $onlyOwned = false, ?string $title = null): Ability
    {
        return DB::transaction(function () use ($name, $entity, $onlyOwned, $title): Ability {
            $ability = Models::ability()::query()->firstOrCreate([
                'name' => $name,
                'entity_type' => Morph::type($entity),
                'entity_id' => Morph::key($entity),
                'only_owned' => $onlyOwned,
            ], ['title' => $title]);

            if ($ability->wasRecentlyCreated) {
                $this->refresh();
            }

            return $ability;
        });
    }

    /**
     * Find or create a role definition; a scope makes it a definition for that scope only.
     */
    public function defineRole(string $name, ?string $title = null, ?Model $on = null): Role
    {
        return DB::transaction(function () use ($name, $title, $on): Role {
            $role = Models::role()::query()->firstOrCreate([
                'name' => $name,
                'scope_type' => Morph::type($on),
                'scope_id' => Morph::key($on),
            ], ['title' => $title]);

            if ($role->wasRecentlyCreated) {
                $this->refresh();
            }

            return $role;
        });
    }

    public function disallow(Model $actor, ?Model $on = null): GrantBuilder
    {
        return new GrantBuilder(GrantMode::Disallow, $actor, $on, $this);
    }

    public function forbid(Model $actor, ?Model $on = null): GrantBuilder
    {
        return new GrantBuilder(GrantMode::Forbid, $actor, $on, $this);
    }

    /**
     * Has the role through a global assignment; scoped assignments do not count.
     */
    public function hasGlobalRole(Model $actor, string $role): bool
    {
        $assignedRoles = config()->string('valkyrie.tables.assigned_roles');

        return $this->assignmentsOf($actor, $role)
            ->tap(fn (Builder $query) => ScopeConstraint::apply($query, $assignedRoles, null))
            ->exists();
    }

    /**
     * Without a scope: has the role anywhere. With one: globally or in that scope.
     */
    public function hasRole(Model $actor, string $role, ?Model $on = null): bool
    {
        $assignedRoles = config()->string('valkyrie.tables.assigned_roles');

        return $this->assignmentsOf($actor, $role)
            ->when($on !== null, fn (Builder $query) => ScopeConstraint::apply($query, $assignedRoles, $on))
            ->exists();
    }

    /**
     * @param  class-string<Model>  $model
     * @param  string|Closure(Model, Model): bool  $attribute
     */
    public function ownedVia(string $model, string|Closure $attribute): void
    {
        $this->ownership->register($model, $attribute);
    }

    public function owns(Model $actor, Model $entity): bool
    {
        return $this->ownership->owns($actor, $entity);
    }

    /**
     * Invalidate every cached ability set.
     */
    public function refresh(): void
    {
        $this->cache->bump();
    }

    /**
     * Invalidate one actor's cache; a role actor affects many actors, so bump everything.
     */
    public function refreshFor(Model $actor): void
    {
        $actor instanceof Role
            ? $this->cache->bump()
            : $this->cache->bumpActor($actor->getMorphClass(), $actor->getKey());
    }

    /**
     * Take a role from one or more actors. Unknown role names are a no-op.
     *
     * @param  Model|list<Model>  $actors
     */
    public function retract(string|Role $role, Model|array $actors, ?Model $on = null): void
    {
        $this->assignments()->retract($role, $actors instanceof Model ? [$actors] : $actors, $on);
    }

    public function unforbid(Model $actor, ?Model $on = null): GrantBuilder
    {
        return new GrantBuilder(GrantMode::Unforbid, $actor, $on, $this);
    }

    /**
     * @return EloquentBuilder<AssignedRole>
     */
    private function assignmentsOf(Model $actor, string $role): EloquentBuilder
    {
        $roles = config()->string('valkyrie.tables.roles');
        $assignedRoles = config()->string('valkyrie.tables.assigned_roles');

        return Models::assignedRole()::query()
            ->join($roles, "{$roles}.id", '=', "{$assignedRoles}.role_id")
            ->where("{$assignedRoles}.entity_type", $actor->getMorphClass())
            ->where("{$assignedRoles}.entity_id", $actor->getKey())
            ->where("{$roles}.name", $role);
    }

    private function assignments(): RoleAssignment
    {
        return new RoleAssignment($this);
    }
}
