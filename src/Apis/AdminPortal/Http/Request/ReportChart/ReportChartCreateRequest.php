<?php

namespace App\Apis\AdminPortal\Http\Request\ReportChart;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiNotBlankConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class ReportChartCreateRequest extends ApiRequest
{
    #[ApiNotBlankConstraint]
    #[ApiStringConstraint]
    public mixed $name = null;

    #[ApiStringConstraint]
    public mixed $description = null;

    #[ApiNotBlankConstraint]
    #[ApiStringConstraint]
    public mixed $slug = null;

    #[ApiNotBlankConstraint]
    #[ApiStringConstraint]
    public mixed $category = null;

    #[ApiNotBlankConstraint]
    #[ApiBooleanConstraint]
    public mixed $active = null;

    #[ApiNotBlankConstraint]
    #[ApiIntegerConstraint]
    public mixed $report_type_id = null;

    #[ApiIntegerConstraint]
    public mixed $size = null;

    #[ApiArrayConstraint]
    public mixed $options = null;

    #[ApiStringConstraint]
    public mixed $return_y = null;

    #[ApiNotBlankConstraint]
    #[ApiIntegerConstraint]
    public mixed $type = null;

    public function __construct(array $values)
    {
        $this->allowEmpty = false;
        parent::__construct($values);
    }
}
