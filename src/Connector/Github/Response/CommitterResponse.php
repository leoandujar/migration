<?php

namespace App\Connector\Github\Response;

class CommitterResponse implements ResponseInterface
{
	public ?string $name;
	public ?string $email;
	public ?string $date;

	public static function decode($data): ResponseInterface
	{
		if (is_string($data)) {
			$data = json_decode($data);
		}
		$instance        = new self();
		$instance->name  = $data->name ?? null;
		$instance->email = $data->email ?? null;
		$instance->date  = $data->date ?? null;

		return $instance;
	}
}
