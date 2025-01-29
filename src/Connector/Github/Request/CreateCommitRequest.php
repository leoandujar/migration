<?php

namespace App\Connector\Github\Request;

use GuzzleHttp\Psr7\Request;

class CreateCommitRequest extends Request
{
	public function __construct($username, $repository, $token, $shaLatestCommit, $shaNewTree, $commitMessage)
	{
		$url = sprintf(
			'%s/repos/%s/%s/git/commits',
			Constant::GITHUB_API_URL,
			$username,
			$repository
		);
		$body = [
			'parents' => [$shaLatestCommit],
			'tree'    => $shaNewTree,
			'message' => $commitMessage,
		];
		parent::__construct('POST', $url, [
			'Authorization' => [sprintf('token %s', $token)],
			'Accept'        => 'application/vnd.github.v3+json',
		], json_encode($body));
	}
}
