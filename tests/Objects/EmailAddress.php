<?php

namespace Dldash\DataTransferObject\Tests\Objects;

use Dldash\DataTransferObject\Contracts\ValueObjectContract;
use JsonSerializable;

class EmailAddress implements ValueObjectContract, JsonSerializable
{
    public function __construct(
        private string $emailAddress
    )
    {
    }

    public function value(): string
    {
        return $this->emailAddress;
    }

    public function jsonSerialize()
    {
        return $this->emailAddress;
    }
}
