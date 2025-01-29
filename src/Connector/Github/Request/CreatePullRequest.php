<?php

namespace App\Connector\Github\Request;

use GuzzleHttp\Psr7\Request;

class CreatePullRequest extends Request
{
	public function __construct($username, $repository, $token, $head, $title)
	{
		$url = sprintf(
			'%s/repos/%s/%s/pulls',
			Constant::GITHUB_API_URL,
			$username,
			$repository
		);
		$body = [
			'head'   => $head,
			'base'   => 'master',
			'title'  => sprintf('Translation - %s', $title),
			'labels' => 'Translated Content',
		];
		parent::__construct('POST', $url, [
			'Authorization' => ["token $token"],
			'Accept'        => 'application/vnd.github.v3+json',
		], json_encode($body));
	}
}
