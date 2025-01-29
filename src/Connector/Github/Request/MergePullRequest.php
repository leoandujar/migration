<?php

namespace App\Connector\Github\Request;

use GuzzleHttp\Psr7\Request;

class MergePullRequest extends Request
{
	public function __construct($username, $repository, $token, $pullNumber, $commitTitle)
	{
		$url = sprintf(
			'%s/repos/%s/%s/pulls/%d/merge',
			Constant::GITHUB_API_URL,
			$username,
			$repository,
			$pullNumber
		);
		$body = [
			'commit_title' => $commitTitle,
		];
		parent::__construct('PUT', $url, [
			'Authorization' => ["token $token"],
			'Accept'        => 'application/vnd.github.v3+json',
		], json_encode($body));
	}
}
