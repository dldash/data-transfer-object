<?php

namespace Dldash\DataTransferObject\Tests\DTO;

use Dldash\DataTransferObject\Models\DataTransferObject;
use Dldash\DataTransferObject\Objects\Undefined;
use Dldash\DataTransferObject\Tests\Objects\EmailAddress;

class OrderDto extends DataTransferObject
{
    public function __construct(
        public int $orderId,
        public UserDto $user,
        public EmailAddress $emailAddress,
        public int|null|Undefined $undefined
    )
    {
    }
}
