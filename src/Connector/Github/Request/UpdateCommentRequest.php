<?php

namespace App\Connector\Github\Request;

use GuzzleHttp\Psr7\Request;

class UpdateCommentRequest extends Request
{
	public function __construct($username, $repository, $token, $ref, $shaNewCommit)
	{
		$url = sprintf(
			'%s/repos/%s/%s/git/refs/%s',
			Constant::GITHUB_API_URL,
			$username,
			$repository,
			sprintf('heads/%s', $ref)
		);
		$body = [
			'sha' => $shaNewCommit,
			'force' => true,
		];
		parent::__construct('POST', $url, [
			'Authorization' => ["token $token"],
			'Accept'        => 'application/vnd.github.v3+json',
		], json_encode($body));
	}
}
