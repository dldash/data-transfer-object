<?php

namespace Dldash\DataTransferObject\Models;

use Dldash\DataTransferObject\Attributes\SerializedName;
use Dldash\DataTransferObject\Contracts\DataTransferObjectContract;
use Dldash\DataTransferObject\Contracts\ValueObjectContract;
use Dldash\DataTransferObject\Objects\Undefined;
use JsonSerializable;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use RuntimeException;

abstract class DataTransferObject implements DataTransferObjectContract, JsonSerializable
{
    public static function create(array $items): static
    {
        $attributes = [];

        [$properties, $castable, $nullable, $map] = static::getProperties();
        foreach ($properties as $key) {
            $serialized = $map[$key] ?? $key;
            if (!array_key_exists($serialized, $items)) {
                continue;
            }

            $value = $items[$serialized];

            $cast = $castable[$key] ?? null;
            if (!$cast) {
                $attributes[$key] = $value;
                continue;
            }

            $reflect = static::reflect($cast);

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

    private static function reflect(string $cast): ReflectionClass
    {
        try {
            return new ReflectionClass($cast);
        } catch (ReflectionException $e) {
            throw new RuntimeException($e);
        }
    }

    private static function getProperties(): array
    {
        // @todo add cache
        $properties = $castable = $nullable = $map = [];

        $reflect = new ReflectionClass(static::class);
        foreach ($reflect->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            // Properties
            $properties[] = $property->getName();

            // Map
            $attribute = static::getSerializedNameAttribute($property);
            if ($attribute) {
                $map[$property->getName()] = $attribute->getName();
            }

            $type = $property->getType();

            // Castable
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $castable[$property->getName()] = $type->getName();
            }

            // Nullable and castable
            if ($type instanceof ReflectionUnionType) {
                $nullable[$property->getName()] = Undefined::create();
                foreach ($type->getTypes() as $union) {
                    if (!$union->isBuiltin() && $union->getName() !== Undefined::class) {
                        $castable[$property->getName()] = $union->getName();
                    }
                }
            }
        }

        return [$properties, $castable, $nullable, $map];
    }

    private static function getSerializedNameAttribute(ReflectionProperty $property): SerializedName|null
    {
        foreach ($property->getAttributes(SerializedName::class) as $attribute) {
            /** @var SerializedName $instance */
            $instance = $attribute->newInstance();
            return $instance;
        }
        return null;
    }

    public function toArray(): array
    {
        $items = [];

        $reflect = new ReflectionClass(static::class);
        foreach ($reflect->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $attribute = static::getSerializedNameAttribute($property);
            $key = $attribute ? $attribute->getName() : $property->getName();

            $property->setAccessible(true);
            $value = $property->getValue($this);

            $items[$key] = $value instanceof DataTransferObjectContract ? $value->toArray() : $value;
        }

        return $items;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
