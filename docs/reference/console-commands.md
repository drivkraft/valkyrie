# Console commands

## `valkyrie:grant-superuser`

```bash
php artisan valkyrie:grant-superuser {email}
```

Grants a user every ability. Use it to regain access after an admin locks themselves out, or to set up the first admin.

| Argument | Description |
|---|---|
| `email` | The value to match against the configured lookup column, by default the user's email address. |

What it does:

1. Finds the user on the model in `auth.providers.users.model` by the column in `valkyrie.superuser.lookup` (default `email`).
2. Finds or creates the role `valkyrie.superuser.role` (default `system-admin`), titled `valkyrie.superuser.title` (default `System Administrator`).
3. Grants that role `everything()`.
4. Assigns the role to the user globally.

It's safe to run more than once.

| Exit code | When |
|---|---|
| `0` | The user now holds the role. |
| `1` | No user matches the argument in the lookup column. |

> [!NOTE]
> The superuser role grants `*` on `*`, but a direct `forbid()` on the user still wins.
