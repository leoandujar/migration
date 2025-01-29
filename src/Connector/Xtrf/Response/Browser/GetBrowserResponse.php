<?php

namespace App\Connector\Xtrf\Response\Browser;

use App\Connector\Xtrf\Response\Response;

class GetBrowserResponse extends Response
{
	private array $processedRows = [];
	private int $pagesCount = 0;
	private int $currentPage = 0;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);
	}

	/**
	 * @return mixed
	 */
	public function translateRaw(): void
	{
		if ($this->isSuccessfull()) {
			$this->pagesCount  = $this->raw['header']['pagination']['pagesCount'];
			$this->currentPage = $this->raw['header']['pagination']['currentPage'];

			if (!empty($this->raw['rows'])) {
				$columns = [];
				foreach ($this->raw['header']['columns'] as $key => $val) {
					$columns[$key] = $val['name'];
				}

				foreach ($this->raw['rows'] as $row) {
					$newRow = [];
					foreach ($row['columns'] as $key => $val) {
						$newRow[$columns[$key]] = $val;
					}
					$this->processedRows[] = $newRow;
				}
			}
		}
	}

	public function getProcessedRows(): array
	{
		return $this->processedRows;
	}

	public function getPagesCount(): int
	{
		return $this->pagesCount;
	}

	public function getCurrentPage(): int
	{
		return $this->currentPage;
	}
}
