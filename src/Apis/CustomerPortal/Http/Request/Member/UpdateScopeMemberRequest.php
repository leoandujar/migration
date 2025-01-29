<?php

namespace App\Apis\CustomerPortal\Http\Request\Member;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiChoiceConstraint;
use App\Model\Entity\SystemAccount;

class UpdateScopeMemberRequest extends ApiRequest
{
	#[ApiChoiceConstraint(choices: [SystemAccount::OFFICE_ALL_OFFICE, SystemAccount::OFFICE_DEPARTMENT, SystemAccount::OFFICE_OFFICE, SystemAccount::OFFICE_ONLY_RELATED])]
	public mixed $scope = null;

	#[ApiBooleanConstraint]
	public mixed $allow = null;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
