<?php

declare(strict_types=1);

namespace Drivkraft\Valkyrie\Facades;

use Closure;
use Drivkraft\Valkyrie\Grants\GrantBuilder;
use Drivkraft\Valkyrie\Models\Ability;
use Drivkraft\Valkyrie\Models\Role;
use Drivkraft\Valkyrie\ValkyrieManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static GrantBuilder allow(Model $actor, ?Model $on = null)
 * @method static GrantBuilder forbid(Model $actor, ?Model $on = null)
 * @method static GrantBuilder disallow(Model $actor, ?Model $on = null)
 * @method static GrantBuilder unforbid(Model $actor, ?Model $on = null)
 * @method static void assign(string|Role $role, Model|array<int, Model> $actors, ?Model $on = null)
 * @method static void retract(string|Role $role, Model|array<int, Model> $actors, ?Model $on = null)
 * @method static Role defineRole(string $name, ?string $title = null, ?Model $on = null)
 * @method static Ability defineAbility(string $name, Model|string|null $entity = null, bool $onlyOwned = false, ?string $title = null)
 * @method static bool can(Model $actor, string $ability, Model|string|null $entity = null, ?Model $on = null)
 * @method static bool cannot(Model $actor, string $ability, Model|string|null $entity = null, ?Model $on = null)
 * @method static bool|null check(Model $actor, string $ability, Model|string|null $entity = null, ?Model $on = null)
 * @method static bool hasRole(Model $actor, string $role, ?Model $on = null)
 * @method static bool hasGlobalRole(Model $actor, string $role)
 * @method static void ownedVia(string $model, string|Closure $attribute)
 * @method static bool owns(Model $actor, Model $entity)
 * @method static void refresh()
 * @method static void refreshFor(Model $actor)
 *
 * @see ValkyrieManager
 */
final class Valkyrie extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ValkyrieManager::class;
    }
}
