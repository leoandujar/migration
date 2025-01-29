<?php

namespace App\Apis\AdminPortal\Http\Request\Projects;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class ProjectListRequest extends ApiRequest
{
    #[ApiNotBlankConstraint]
    #[ApiStringConstraint]
    public mixed $name = null;

    #[ApiNotBlankConstraint]
    #[ApiIntegerConstraint]
    public mixed $limit = null;

    #[ApiIntegerConstraint]
    public mixed $customer_id = null;

    #[ApiBooleanConstraint]
    public mixed $archived = null;

    #[ApiStringConstraint]
    public mixed $status = null;

    public function __construct(array $values)
    {
        $this->allowEmpty = false;
        parent::__construct($values);
    }
}
