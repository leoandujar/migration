<?php

namespace App\Apis\Shared\Http\Request;

class EmptyPaginationRequest extends ApiRequest
{
	public function __construct(array $params)
	{
		$this->enablePagination = true;
		$this->allowEmpty = false;
		parent::__construct($params);
	}
}
