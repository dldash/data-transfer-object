<?php

namespace Dldash\DataTransferObject\Tests\DTO;

use Dldash\DataTransferObject\Attributes\SerializedName;
use Dldash\DataTransferObject\Models\DataTransferObject;

class ProjectDto extends DataTransferObject
{
    public function __construct(
        #[SerializedName('project_id')]
        private int $id,

        #[SerializedName('project_name')]
        private string|null $name
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string|null
    {
        return $this->name;
    }
}
