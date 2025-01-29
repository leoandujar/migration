<?php

namespace App\Connector\Github\Request;

use GuzzleHttp\Psr7\Request;

class GetLabelRequest extends Request
{
	public function __construct($username, $repository, $token, $label)
	{
		$url = sprintf(
			'%s/repos/%s/%s/labels/%s',
			Constant::GITHUB_API_URL,
			$username,
			$repository,
			$label
		);
		parent::__construct('GET', $url, [
			'Authorization' => ["token $token"],
			'Accept'        => 'application/vnd.github.v3+json',
		]);
	}
}
