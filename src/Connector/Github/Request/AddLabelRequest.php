<?php

namespace App\Connector\Github\Request;

use GuzzleHttp\Psr7\Request;

class AddLabelRequest extends Request
{
	public function __construct($username, $repository, $token, $label, $pullNumber)
	{
		$url = sprintf(
			'%s/repos/%s/%s/issues/%d/labels',
			Constant::GITHUB_API_URL,
			$username,
			$repository,
			$pullNumber
		);
		$body = [
			'labels' => [$label],
		];
		parent::__construct('POST', $url, [
			'Authorization' => ["token $token"],
			'Accept'        => 'application/vnd.github.v3+json',
		], json_encode($body));
	}
}
