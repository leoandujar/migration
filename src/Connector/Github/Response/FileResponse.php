<?php

namespace App\Connector\Github\Response;

class FileResponse implements ResponseInterface
{
	public ?string $path;
	public ?string $mode;
	public ?string $type;
	public ?string $content;
	public ?string $size;
	public ?string $sha;
	public ?string $url;

	public static function decode($data): ResponseInterface
	{
		if (is_string($data)) {
			$data = json_decode($data);
		}
		$instance          = new self();
		$instance->path    = $data->path ?? null;
		$instance->mode    = $data->mode ?? null;
		$instance->type    = $data->type ?? null;
		$instance->content = $data->content ?? null;
		$instance->size    = $data->size ?? null;
		$instance->sha     = $data->sha ?? null;
		$instance->url     = $data->url ?? null;

		return $instance;
	}
}
