<?php

namespace App\Apis\AdminPortal\Http\Request\Users;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;

class UserListRequest extends ApiRequest
{
    #[ApiArrayConstraint]
    public mixed $roles = null;

    #[ApiArrayConstraint]
    public mixed $status = null;

    #[ApiBooleanConstraint]
    public mixed $xtrf = null;

    public function __construct(array $params)
    {
        $this->allowEmpty = true;
        parent::__construct($params);
    }
}
