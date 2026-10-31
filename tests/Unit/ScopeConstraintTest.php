<?php

declare(strict_types=1);

use Drivkraft\Valkyrie\Contracts\ScopedEntity;
use Drivkraft\Valkyrie\Support\ScopeConstraint;
use Illuminate\Database\Eloquent\Model;
use Workbench\App\Models\Office;
use Workbench\App\Models\Team;

function scopedEntity(?Model $scope): Model&ScopedEntity
{
    return new class($scope) extends Model implements ScopedEntity
    {
        public function __construct(private readonly ?Model $scope = null)
        {
            parent::__construct();
        }

        public function accessScope(): ?Model
        {
            return $this->scope;
        }
    };
}

function office(int $id): Office
{
    $office = new Office;
    $office->setAttribute('id', $id);

    return $office;
}

it('matches a null scope type only against no scope', function (): void {
    expect(ScopeConstraint::matches(null, null, null))->toBeTrue()
        ->and(ScopeConstraint::matches(null, null, office(1)))->toBeFalse();
});

it('matches a scoped row only against the same type and key', function (): void {
    $office = office(1);

    expect(ScopeConstraint::matches($office->getMorphClass(), 1, $office))->toBeTrue()
        ->and(ScopeConstraint::matches($office->getMorphClass(), '1', $office))->toBeTrue()
        ->and(ScopeConstraint::matches($office->getMorphClass(), 2, $office))->toBeFalse()
        ->and(ScopeConstraint::matches((new Team)->getMorphClass(), 1, $office))->toBeFalse()
        ->and(ScopeConstraint::matches($office->getMorphClass(), 1, null))->toBeFalse();
});

it('lets a scoped entity decide the effective scope', function (): void {
    $office = office(1);
    $other = office(2);

    expect(ScopeConstraint::effective(scopedEntity($office), $other))->toBe($office)
        ->and(ScopeConstraint::effective(scopedEntity(null), $other))->toBeNull();
});

it('falls back to the given scope for other entities', function (): void {
    $office = office(1);

    expect(ScopeConstraint::effective(new Team, $office))->toBe($office)
        ->and(ScopeConstraint::effective(Team::class, $office))->toBe($office)
        ->and(ScopeConstraint::effective(null, null))->toBeNull();
});
