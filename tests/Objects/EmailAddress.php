<?php

namespace Dldash\DataTransferObject\Tests\Objects;

use Dldash\DataTransferObject\Contracts\ValueObjectContract;
use InvalidArgumentException;
use JsonSerializable;

class EmailAddress implements ValueObjectContract, JsonSerializable
{
    public function __construct(private string $emailAddress)
    {
        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email address [{$emailAddress}] is not valid.");
        }

        $this->emailAddress = strtolower($emailAddress);
    }

    public function value(): string
    {
        return $this->emailAddress;
    }

    public function jsonSerialize(): string
    {
        return $this->emailAddress;
    }
}
