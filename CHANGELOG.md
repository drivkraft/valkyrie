# Release Notes

## v1.0.0 - 2026-10-31

Valkyrie 1.0 is the first stable release of a roles-and-abilities package for Laravel. Rules live in the database, and every Gate check is answered through a `Gate::before` bridge, so `$user->can()`, `@can`, policies and route middleware work without changes.

### What you get

**Grants that read like sentences.** Allow or forbid abilities on a name, a model class or a single record, directly on a user or through a role.

```php
$user->allow()->to('view', Report::class);
$user->forbid()->to('delete', Report::class);

Valkyrie::allow(Valkyrie::defineRole('manager'))->toManage(Report::class);
$user->assign('manager', $office);

$user->can('update', $report);

```
**Forbids always win.** An explicit forbid beats any allow, no matter where the allow came from.

**Scopes.** Limit any grant or role assignment to one model, such as an office, a team or a project. Matching is exact and never inherited. An entity can carry its own scope by implementing `ScopedEntity`.

**Ownership.** Allow an ability only on records the actor owns. The default owner column is `user_id`; `Valkyrie::ownedVia()` switches it to another attribute or a closure.

**Roles, global or per scope.** Define a role once and assign it anywhere, or define it for a single scope.

**Routes and views.** `role` and `ability` middleware aliases are pre-registered. `@role` and `@unlessrole` Blade directives are included.

**A cache that stays correct.** Resolved rules are cached per actor with versioned keys. Invalidation touches only what changed, waits for the surrounding transaction to commit, and survives rollbacks and cache eviction. Writes that bypass the API are caught by model events.

**Multi-tenancy, your way.** Database-per-tenant needs no configuration. Shared-table setups get a configurable cache partition so tenants never share cache entries.

**Your models, your tables.** Subclass any of the four models and register it in config. Table names and morph aliases are configurable. Migrations load automatically and can be published to customise the schema.

### Also included

- After-commit events for every grant, forbid, revoke, assignment and retraction.
- `valkyrie:grant-superuser` to set up the first admin or recover from a lockout. The lookup column is configurable.
- `whereCan()` and `whereIs()` query scopes to find actors by ability or role.
- Factories for roles, abilities, permissions and assignments.
- Automatic cleanup when an actor or scope model is deleted.

### Requirements

PHP 8.4 or newer and Laravel 13.

### Installation

```bash
composer require drivkraft/valkyrie
php artisan migrate

```
Then add `Drivkraft\Valkyrie\Concerns\HasRolesAndAbilities` to your `User` model.

### Documentation

Start with the [getting started tutorial](https://github.com/drivkraft/valkyrie/blob/main/docs/tutorials/getting-started.md), or browse the [full index](https://github.com/drivkraft/valkyrie/blob/main/docs/README.md) of how-to guides, reference pages and design notes.
