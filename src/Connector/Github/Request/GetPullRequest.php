<?php

namespace App\Connector\Github\Request;

use GuzzleHttp\Psr7\Request;

class GetPullRequest extends Request
{
	public function __construct($username, $repository, $token, $number)
	{
		$url = sprintf(
			'%s/repos/%s/%s/pulls/%d',
			Constant::GITHUB_API_URL,
			$username,
			$repository,
			$number
		);
		parent::__construct('GET', $url, [
			'Authorization' => ["token $token"],
			'Accept'        => 'application/vnd.github.v3+json',
		]);
	}
}
