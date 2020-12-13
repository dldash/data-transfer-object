<?php

namespace Dldash\DataTransferObject\Tests\Objects;

use Dldash\DataTransferObject\Contracts\ValueObjectContract;
use InvalidArgumentException;
use JsonSerializable;

class EmailAddress implements ValueObjectContract, JsonSerializable
{
    public function __construct(private string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email address [{$value}] is not valid.");
        }

        $this->value = strtolower($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
