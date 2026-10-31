# Configuration

Publish the file with:

```bash
php artisan vendor:publish --tag=valkyrie-config
```

The package merges its defaults, so a published file only needs the keys you change.

## `cache`

| Key | Type | Default | Description |
|---|---|---|---|
| `cache.enabled` | `bool` | `env('VALKYRIE_CACHE', true)` | Turns the cache store layer on or off. The per-request memo always runs. |
| `cache.store` | `?string` | `env('VALKYRIE_CACHE_STORE')` (`null`) | The cache store name. `null` uses the application's default store. |
| `cache.ttl` | `int` | `86400` | Seconds that cached ability rows live. This is only a backstop, because invalidation is versioned. |
| `cache.partition` | `class-string<CachePartition>` | `DefaultCachePartition::class` | Resolved through the container and bound as a singleton. Its `key()` is part of every cache key. |

## `migrations`

| Key | Type | Default | Description |
|---|---|---|---|
| `migrations` | `bool` | `true` | Loads the package migration. Set it to `false` after publishing the migration with `--tag=valkyrie-migrations`. |

## `models`

Each value must be the base class or a subclass of it. Otherwise resolving it throws `InvalidArgumentException`.

| Key | Default |
|---|---|
| `models.role` | `Drivkraft\Valkyrie\Models\Role` |
| `models.ability` | `Drivkraft\Valkyrie\Models\Ability` |
| `models.permission` | `Drivkraft\Valkyrie\Models\Permission` |
| `models.assigned_role` | `Drivkraft\Valkyrie\Models\AssignedRole` |

## `tables`

| Key | Default |
|---|---|
| `tables.abilities` | `valkyrie_abilities` |
| `tables.roles` | `valkyrie_roles` |
| `tables.assigned_roles` | `valkyrie_assigned_roles` |
| `tables.permissions` | `valkyrie_permissions` |

## `morphs`

These are morph-map aliases for Valkyrie's own models. They're stored in `entity_type` columns, for example when a permission is granted to a role.

| Key | Default |
|---|---|
| `morphs.role` | `role` |
| `morphs.ability` | `ability` |

> [!WARNING]
> Don't change these once rows exist. Existing rows keep the old alias and stop matching.

## `superuser`

Used by [`valkyrie:grant-superuser`](console-commands.md).

| Key | Default |
|---|---|
| `superuser.role` | `system-admin` |
| `superuser.title` | `System Administrator` |
| `superuser.lookup` | `email` |

`superuser.lookup` is the column on `auth.providers.users.model` that the command matches its argument against.

## Environment variables

| Variable | Config key |
|---|---|
| `VALKYRIE_CACHE` | `cache.enabled` |
| `VALKYRIE_CACHE_STORE` | `cache.store` |

## Publish tags

| Tag | Publishes |
|---|---|
| `valkyrie-config` | `config/valkyrie.php` |
| `valkyrie-migrations` | `database/migrations/2026_09_24_101538_create_valkyrie_tables.php` |
| `valkyrie` | Both of the above |
