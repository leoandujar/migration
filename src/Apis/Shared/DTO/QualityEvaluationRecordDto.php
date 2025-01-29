<?php

namespace App\Apis\Shared\DTO;

class QualityEvaluationRecordDto
{
    public function __construct(
        public ?string $id,
        public ?string $name,
        public ?int    $value,
        public ?string $comment
    )
    {
    }
}
