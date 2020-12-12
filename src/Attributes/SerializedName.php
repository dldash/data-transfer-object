<?php

namespace Dldash\DataTransferObject\Attributes;

use Attribute;

#[Attribute]
final class SerializedName
{
    public function __construct(private string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
