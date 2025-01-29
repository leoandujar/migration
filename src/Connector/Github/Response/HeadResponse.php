<?php

namespace App\Connector\Github\Response;

class HeadResponse implements ResponseInterface
{
	public ?string $ref;
	public ?string $node;
	public ?string $url;
	public ?ResponseInterface $commit;

	public static function decode($data): ResponseInterface
	{
		if (is_string($data)) {
			$data = json_decode($data);
		}
		$instance         = new self();
		$instance->ref    = $data->ref ?? null;
		$instance->node   = $data->node_id ?? null;
		$instance->url    = $data->url ?? null;
		$instance->commit = CommitResponse::decode($data->object ?? []);

		return $instance;
	}
}
