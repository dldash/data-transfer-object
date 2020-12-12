<?php

namespace Dldash\DataTransferObject\Tests\DTO;

use Dldash\DataTransferObject\Attributes\SerializedName;
use Dldash\DataTransferObject\Models\DataTransferObject;

class ProjectDto extends DataTransferObject
{
    public function __construct(
        #[SerializedName('project_id')]
        public int $id,

        #[SerializedName('project_name')]
        public string|null $name
    )
    {
    }
}
