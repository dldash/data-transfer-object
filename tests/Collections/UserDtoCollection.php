<?php

namespace Dldash\DataTransferObject\Tests\Collections;

use ArrayIterator;
use Dldash\DataTransferObject\Objects\DataTransferObjectCollection;
use Dldash\DataTransferObject\Tests\DTO\UserDto;

/** @method ArrayIterator|UserDto[] getIterator() */
class UserDtoCollection extends DataTransferObjectCollection
{
    protected function create(mixed $item): object
    {
        return UserDto::create($item);
    }
}
