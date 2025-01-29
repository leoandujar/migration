<?php

namespace App\Apis\Shared\DTO;

class PaginationDto
{
	public int $from = 0;
	public int $to = 0;
	public int $perPage = 0;
	public int $currentPage = 0;
	public int $totalPages = 0;
	public int $total = 0;

	public string $sortBy = 'id';
	public string $sortOrder = 'DESC';

	public function __construct(int $currentPage, int $perPage, int $total, string $sortBy, string $sortOrder)
	{
		$this->currentPage = $currentPage;
		$this->perPage = $perPage;
		$this->total = $total;
		$this->sortBy = $sortBy;
		$this->sortOrder = $sortOrder;
		$this->calculate();
	}

	private function calculate()
	{
		if (0 !== $this->total) {
			$this->totalPages = ceil($this->total / $this->perPage);
		}
		$this->to = $this->currentPage * $this->perPage;
		$this->from = $this->to - $this->perPage;
	}
}
