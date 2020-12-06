<?php

namespace Dldash\DataTransferObject\Objects;

use JetBrains\PhpStorm\Pure;
use JsonSerializable;
use Stringable;

final class Undefined implements JsonSerializable, Stringable
{
    #[Pure] public static function create(): Undefined
    {
        return new Undefined();
    }

    public function jsonSerialize(): array|null
    {
        return null;
    }

    public function __toString(): string
    {
        return '';
    }
}
