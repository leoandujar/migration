<?php

namespace App\Connector\Github\Request;

use GuzzleHttp\Psr7\Request;

class GetLatestCommitRequest extends Request
{
	public function __construct($username, $repository, $token)
	{
		$url = sprintf(
			'%s/repos/%s/%s/git/refs/heads/master',
			Constant::GITHUB_API_URL,
			$username,
			$repository
		);
		parent::__construct('GET', $url, [
			'Authorization' => ["token $token"],
			'Accept'        => 'application/vnd.github.v3+json',
		]);
	}
}
