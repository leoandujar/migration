<?php

namespace App\Connector\Github\Request;

use GuzzleHttp\Psr7\Request;

class PushCommentRequest extends Request
{
	public function __construct($username, $repository, $token, $ref, $shaNewCommit)
	{
		$url = sprintf(
			'%s/repos/%s/%s/git/refs',
			Constant::GITHUB_API_URL,
			$username,
			$repository
		);
		$body = [
			'ref' => sprintf('refs/heads/%s', $ref),
			'sha' => $shaNewCommit,
		];
		parent::__construct('POST', $url, [
			'Authorization' => ["token $token"],
			'Accept'        => 'application/vnd.github.v3+json',
		], json_encode($body));
	}
}
