<?php

declare(strict_types=1);

use Drivkraft\Valkyrie\Grants\GrantMode;

it('creates rows for allow and forbid only', function (): void {
    expect(GrantMode::Allow->creates())->toBeTrue()
        ->and(GrantMode::Forbid->creates())->toBeTrue()
        ->and(GrantMode::Disallow->creates())->toBeFalse()
        ->and(GrantMode::Unforbid->creates())->toBeFalse();
});

it('targets forbidden rows for forbid and unforbid only', function (): void {
    expect(GrantMode::Forbid->forbidden())->toBeTrue()
        ->and(GrantMode::Unforbid->forbidden())->toBeTrue()
        ->and(GrantMode::Allow->forbidden())->toBeFalse()
        ->and(GrantMode::Disallow->forbidden())->toBeFalse();
});
