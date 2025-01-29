<?php

namespace App\Connector\Github\Response;

class ReferenceResponse implements ResponseInterface
{
	public ?string $ref;
	public ?string $nodeID;
	public ?string $url;
	public ?ResponseInterface $object;

	public static function decode($data): ResponseInterface
	{
		if (is_string($data)) {
			$data = json_decode($data);
		}
		$instance         = new self();
		$instance->ref    = $data->ref ?? null;
		$instance->nodeID = $data->node_id ?? null;
		$instance->url    = $data->url ?? null;
		$instance->object = ObjectResponse::decode($data->object);

		return $instance;
	}
}
