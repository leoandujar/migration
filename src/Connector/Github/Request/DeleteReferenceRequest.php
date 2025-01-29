<?php

namespace App\Connector\Github\Request;

use GuzzleHttp\Psr7\Request;

class DeleteReferenceRequest extends Request
{
	public function __construct($username, $repo, $token, $ref)
	{
		$url = sprintf(
			'%s/repos/%s/%s/git/%s',
			Constant::GITHUB_API_URL,
			$username,
			$repo,
			$ref
		);

		parent::__construct('DELETE', $url, [
			'Authorization' => ["token $token"],
			'Accept'        => 'application/vnd.github.v3+json',
		]);
	}
}
