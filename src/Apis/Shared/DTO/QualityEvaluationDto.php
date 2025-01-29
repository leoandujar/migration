<?php

namespace App\Apis\Shared\DTO;

class QualityEvaluationDto
{
    /**
     * QualityEvaluationDto constructor.
     */
    public function __construct(
        public ?string           $id,
        public ?GenericPersonDto $evaluatee,
        public ?GenericPersonDto $evaluator,
        public ?float            $score,
        public ?string           $createdAt,
        public ?string           $type,
        public bool              $excellent,
        public ?string           $comment,
        public ?array            $records
    )
    {
    }
}
