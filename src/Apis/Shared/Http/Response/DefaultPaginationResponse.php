<?php

namespace App\Apis\Shared\Http\Response;

class DefaultPaginationResponse extends ApiResponse
{
	protected mixed $raw;
	protected bool $enablePagination = true;

	public function __construct(array $data = null)
	{
		parent::__construct();
		$this->raw = $data['entities'] ?? $data;
		$this->updateData($data);
	}

	public function marshall(mixed $data = null): array
	{
		return $data['entities'] ?? $data ?? [];
	}

	public function getRaw(): mixed
	{
		return $this->raw ?? [];
	}
}
