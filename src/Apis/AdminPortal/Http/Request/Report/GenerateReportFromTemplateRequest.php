<?php

namespace App\Apis\AdminPortal\Http\Request\Report;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class GenerateReportFromTemplateRequest extends ApiRequest
{
    #[ApiNotBlankConstraint]
    #[ApiStringConstraint]
    public mixed $customer_id = null;

    #[ApiNotBlankConstraint]
    #[ApiStringConstraint]
    public mixed $id = null;

    #[ApiIntegerConstraint]
    public mixed $format = null;

    #[ApiArrayConstraint]
    public mixed $charts = null;

    #[ApiArrayConstraint]
    public mixed $filters = null;

    #[ApiArrayConstraint]
    public mixed $predefined_data = null;

    #[ApiBooleanConstraint]
    public mixed $debug = null;

    public function __construct(array $values)
    {
        $this->allowEmpty = false;
        parent::__construct($values);
    }
}
