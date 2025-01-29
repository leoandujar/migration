<?php

namespace App\Connector\Github\Request;

use GuzzleHttp\Psr7\Request;

class CreateTreeRequest extends Request
{
	/**
	 * @param array $files
	 */
	public function __construct($username, $repository, $token, $shaBaseTree, $files = [])
	{
		$url = sprintf(
			'%s/repos/%s/%s/git/trees',
			Constant::GITHUB_API_URL,
			$username,
			$repository
		);
		$body = [
			'base_tree' => $shaBaseTree,
			'tree'      => $files,
		];
		parent::__construct('POST', $url, [
			'Authorization' => ["token $token"],
			'Accept'        => 'application/vnd.github.v3+json',
		], json_encode($body));
	}
}
