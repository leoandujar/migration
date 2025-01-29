<?php

namespace App\Apis\CustomerPortal\Http\Request\Dashboard;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiJsonConstraint;
use App\Apis\Shared\Http\Validator\ApiCollectionConstraint;
use App\Apis\Shared\Http\Validator\ApiUuidConstraint;
use Symfony\Component\Validator\Constraints as Assert;

class DashboardAddRequest extends ApiRequest
{
	#[ApiArrayConstraint]
	#[Assert\All([
		new ApiCollectionConstraint(
			fields: [
				'id' => new ApiUuidConstraint(),
				'options' => new ApiJsonConstraint(),
			],
			allowMissingFields: false
		),
	])]
	public mixed $graphs;

	public function __construct(array $values)
	{
		$this->enablePagination = false;
		$this->allowEmpty = false;
		parent::__construct($values);
	}
}
