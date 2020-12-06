<?php

namespace Dldash\DataTransferObject\Tests\DTO;

use Dldash\DataTransferObject\Models\DataTransferObject;

class UserDto extends DataTransferObject
{
    public function __construct(
        public int $userId,
        public string|null $username
    )
    {
    }
}
