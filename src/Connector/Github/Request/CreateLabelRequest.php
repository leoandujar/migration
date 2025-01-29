<?php

namespace App\Connector\Github\Request;

use GuzzleHttp\Psr7\Request;

class CreateLabelRequest extends Request
{
	/**
	 * @param string $name
	 */
	public function __construct($username, $repository, $token, $name, $color)
	{
		$url = sprintf(
			'%s/repos/%s/%s/labels',
			Constant::GITHUB_API_URL,
			$username,
			$repository
		);

		$body = [
			'name'  => $name,
			'color' => $color,
		];
		parent::__construct('POST', $url, [
			'Authorization' => ["token $token"],
			'Accept'        => 'application/vnd.github.v3+json',
		], json_encode($body));
	}
}
