<?php

namespace App\Connector\Github\Response;

class LabelResponse implements ResponseInterface
{
	public $id;
	public $node;
	public $url;
	public $name;
	public $description;
	public $color;
	public $default;

	public static function decode($data): ResponseInterface
	{
		if (is_string($data)) {
			$data = json_decode($data);
		}
		$instance = new self();
		$instance->id = $data->id ?? null;
		$instance->node = $data->node_id ?? null;
		$instance->url = $data->url ?? null;
		$instance->name = $data->name ?? null;
		$instance->description = $data->description ?? null;
		$instance->color = $data->color ?? null;
		$instance->default = $data->default ?? null;

		return $instance;
	}
}
