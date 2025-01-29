<?php

namespace App\Apis\AdminPortal\Http\Request\ContactPerson;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class CustomerContactListRequest extends ApiRequest
{

    #[ApiBooleanConstraint]
    public mixed $onboarding = null;

    #[ApiStringConstraint]
    public mixed $status = null;

    public function __construct(array $params)
    {
        $this->allowEmpty = true;
        parent::__construct($params);
    }
}
