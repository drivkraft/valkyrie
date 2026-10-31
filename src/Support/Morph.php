<?php

declare(strict_types=1);

namespace Drivkraft\Valkyrie\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Translates accepted entity forms (instance, class string, '*' or null) into morph values.
 */
final class Morph
{
    /**
     * @param  Model|class-string<Model>|string|null  $entity
     */
    public static function key(Model|string|null $entity): int|string|null
    {
        return $entity instanceof Model ? $entity->getKey() : null;
    }

    /**
     * @param  Model|class-string<Model>|string|null  $entity
     */
    public static function type(Model|string|null $entity): ?string
    {
        return match (true) {
            $entity instanceof Model => $entity->getMorphClass(),
            $entity === self::Everything => self::Everything,
            is_string($entity) && is_a($entity, Model::class, true) => self::classType($entity),
            default => null,
        };
    }

    /**
     * A mapped class resolves through the morph map without instantiation; an unmapped
     * one keeps the model's own getMorphClass() semantics, including enforcement.
     *
     * @param  class-string<Model>  $class
     */
    private static function classType(string $class): string
    {
        $alias = Relation::getMorphAlias($class);

        return $alias === $class ? (new $class)->getMorphClass() : (string) $alias;
    }

    public const string Everything = '*';
}
