<?php

namespace App\Connector\Xtrf\Request\Dictionaries;

use App\Connector\Xtrf\Request\Request;

class GetDictionaryRequest extends Request
{
	public const TYPE_CATEGORY          = 'category';
	public const TYPE_COUNTRY           = 'country';
	public const TYPE_CURRENCY          = 'currency';
	public const TYPE_INDUSTRY          = 'industry';
	public const TYPE_LANGUAGE          = 'language';
	public const TYPE_LEAD_SOURCE       = 'leadSource';
	public const TYPE_PERSON_DEPARTMENT = 'personDepartment';
	public const TYPE_PERSON_POSITION   = 'personPosition';
	public const TYPE_PROVINCE          = 'province';
	public const TYPE_SPECIALIZATION    = 'specialization';

	protected string $requestMethod = 'GET';
	protected string $requestUri = '/dictionaries';

	public function __construct($dictionaryId, $activeOnly = false)
	{
		$sufix = $activeOnly ? 'active' : 'all';
		$this->requestUri .= "/$dictionaryId/$sufix";

		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
