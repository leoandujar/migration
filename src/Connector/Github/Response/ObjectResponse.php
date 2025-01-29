<?php

namespace App\Connector\Github\Response;

class ObjectResponse implements ResponseInterface
{
	public ?string $type;
	public ?string $sha;
	public ?string $url;

	public static function decode($data): ResponseInterface
	{
		if (is_string($data)) {
			$data = json_decode($data);
		}
		$instance       = new self();
		$instance->type = $data->type ?? null;
		$instance->sha  = $data->sha ?? null;
		$instance->url  = $data->url ?? null;

		return $instance;
	}
}
