<?php

namespace App\Apis\AdminPortal\Http\Request\Users;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;

class UserOptionsListRequest extends ApiRequest
{
    
    #[ApiStringConstraint]
    public mixed $department = null;

    #[ApiStringConstraint]
    public mixed $group = null;

    #[ApiStringConstraint]
    public mixed $position = null;

    public function __construct(array $params)
    {
        $this->allowEmpty = true;
        parent::__construct($params);
    }
}
