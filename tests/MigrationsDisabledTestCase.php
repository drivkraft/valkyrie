<?php

declare(strict_types=1);

namespace Drivkraft\Valkyrie\Tests;

abstract class MigrationsDisabledTestCase extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('valkyrie.migrations', false);
    }
}
