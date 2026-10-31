<?php

declare(strict_types=1);

namespace Drivkraft\Valkyrie\Console\Commands;

use Drivkraft\Valkyrie\ValkyrieManager;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

/**
 * Lockout guard: grants the configured superuser role with every ability. Idempotent.
 */
#[Signature('valkyrie:grant-superuser {email : The user\'s email address, or the value of the configured lookup column}')]
#[Description('Grant the superuser role (every ability) to a user')]
final class GrantSuperuserCommand extends Command
{
    public function handle(ValkyrieManager $valkyrie): int
    {
        /** @var class-string<Model> $model */
        $model = config()->string('auth.providers.users.model');
        $lookup = config()->string('valkyrie.superuser.lookup', 'email');
        $value = (string) $this->argument('email');
        $user = $model::query()->where($lookup, $value)->first();

        if ($user === null) {
            $this->components->error("No user with {$lookup} [{$value}].");

            return self::FAILURE;
        }

        $name = config()->string('valkyrie.superuser.role', 'system-admin');
        $role = $valkyrie->defineRole($name, config()->string('valkyrie.superuser.title', 'System Administrator'));
        $valkyrie->allow($role)->everything();
        $valkyrie->assign($role, $user);

        $this->components->info("[{$value}] holds the {$name} role.");

        return self::SUCCESS;
    }
}
