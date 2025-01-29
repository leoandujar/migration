<?php

namespace App\Apis\AdminPortal\Http\Request\Quality;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class QualityCategorySearchRequest extends ApiRequest
{
    #[ApiStringConstraint]
    public mixed $type = null;

    public function __construct(array $values)
    {
        $this->allowEmpty = true;
        parent::__construct($values);
    }
}
