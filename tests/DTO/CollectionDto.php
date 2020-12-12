<?php

namespace Dldash\DataTransferObject\Tests\DTO;

use Dldash\DataTransferObject\Models\DataTransferObject;
use Dldash\DataTransferObject\Objects\Undefined;
use Dldash\DataTransferObject\Tests\Collections\UserDtoCollection;

class CollectionDto extends DataTransferObject
{
    public function __construct(
        public array $ids,
        public array|null $numbers,
        public UserDtoCollection $users,
        public UserDtoCollection|null|Undefined $admins
    )
    {
    }
}
