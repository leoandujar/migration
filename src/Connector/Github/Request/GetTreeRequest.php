<?php

namespace App\Connector\Github\Request;

use GuzzleHttp\Psr7\Request;

class GetTreeRequest extends Request
{
	public function __construct($username, $repository, $shaLatestCommit, $token)
	{
		$url = sprintf(
			'%s/repos/%s/%s/git/commits/%s',
			Constant::GITHUB_API_URL,
			$username,
			$repository,
			$shaLatestCommit
		);
		parent::__construct('GET', $url, [
			'Authorization' => ["token $token"],
			'Accept'        => 'application/vnd.github.v3+json',
		]);
	}
}
