<?php

namespace Dldash\DataTransferObject\Models;

use Dldash\DataTransferObject\Contracts\DataTransferObjectContract;
use Dldash\DataTransferObject\Contracts\ValueObjectContract;
use Dldash\DataTransferObject\Objects\Undefined;
use JetBrains\PhpStorm\Pure;
use JsonSerializable;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionUnionType;
use RuntimeException;

abstract class DataTransferObject implements DataTransferObjectContract, JsonSerializable
{
    public static function create(array $items): static
    {
        $attributes = [];

        [$castable, $nullable, $properties] = static::getProperties();
        foreach ($items as $key => $value) {
            $exists = $properties[$key] ?? null;
            if (!$exists) {
                continue;
            }

            $cast = $castable[$key] ?? null;
            if (!$cast) {
                $attributes[$key] = $value;
                continue;
            }

            try {
                $reflect = new ReflectionClass($cast);
            } catch (ReflectionException $e) {
                throw new RuntimeException($e);
            }

            if ($reflect->implementsInterface(ValueObjectContract::class)) {
                $attributes[$key] = $value !== null ? new $cast($value) : null;
                continue;
            }

            if ($reflect->implementsInterface(DataTransferObjectContract::class)) {
                $attributes[$key] = $value !== null ? $cast::create($value) : null;
                continue;
            }

            $attributes[$key] = $value;
        }

        return new static(...array_merge($nullable, $attributes));
    }

    private static function getProperties(): array
    {
        // @todo add cache
        $castable = $nullable = $properties = [];

        $reflect = new ReflectionClass(static::class);
        foreach ($reflect->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $properties[$property->getName()] = true;

            $type = $property->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $castable[$property->getName()] = $type->getName();
            }

            if ($type instanceof ReflectionUnionType) {
                $nullable[$property->getName()] = Undefined::create();
                foreach ($type->getTypes() as $union) {
                    if (!$union->isBuiltin() && $union->getName() !== Undefined::class) {
                        $castable[$property->getName()] = $union->getName();
                    }
                }
            }
        }

        return [$castable, $nullable, $properties];
    }

    #[Pure] public function toArray(): array
    {
        // @todo return private properties too
        return get_object_vars($this);
    }

    #[Pure] public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
