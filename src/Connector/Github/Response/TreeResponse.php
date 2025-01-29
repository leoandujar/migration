<?php

namespace App\Connector\Github\Response;

class TreeResponse implements ResponseInterface
{
	public ?string $sha;
	public ?string $url;
	public ?array $tree;

	public static function decode($data): ResponseInterface
	{
		if (is_string($data)) {
			$data = json_decode($data);
		}
		$instance      = new self();
		$instance->sha = $data->sha ?? null;
		$instance->url = $data->url ?? null;
		if (isset($data->tree) && is_array($data->tree)) {
			foreach ($data->tree as $item) {
				$instance->tree[] = FileResponse::decode($item);
			}
		}

		return $instance;
	}
}
