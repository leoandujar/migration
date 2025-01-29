<?php

namespace App\Connector\Github\Response;

class MergeResponse implements ResponseInterface
{
	public ?string $sha;
	public ?string $merged;
	public ?string $message;

	public static function decode($data): ResponseInterface
	{
		if (is_string($data)) {
			$data = json_decode($data);
		}
		$instance          = new self();
		$instance->sha     = $data->sha ?? null;
		$instance->merged  = $data->merged ?? null;
		$instance->message = $data->message ?? null;

		return $instance;
	}
}
