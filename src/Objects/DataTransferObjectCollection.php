<?php

namespace Dldash\DataTransferObject\Objects;

use ArrayIterator;
use Dldash\DataTransferObject\Contracts\ValueObjectContract;
use IteratorAggregate;
use JetBrains\PhpStorm\Pure;
use JsonSerializable;

abstract class DataTransferObjectCollection implements ValueObjectContract, IteratorAggregate, JsonSerializable
{
    protected array $items = [];

    public function __construct(array $items)
    {
        foreach ($items as $item) {
            $this->items[] = $this->create($item);
        }
    }

    abstract protected function create(mixed $item): object;

    public function toArray(): array
    {
        return $this->items;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    #[Pure] public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
