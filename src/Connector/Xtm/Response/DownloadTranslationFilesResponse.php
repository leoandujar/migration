<?php

namespace App\Connector\Xtm\Response;

class DownloadTranslationFilesResponse extends Response
{
	private string $filePath = '/tmp/xtm_file.zip';

	/**
	 * @throws \Exception
	 */
	public function __construct(int $httpCode, array $rawResponse, array $headers = [])
	{
		parent::__construct($httpCode, $rawResponse, $headers);

		if ($this->isSuccessfull()) {
			$this->filePath = sprintf('/tmp/xtm_file_%s.zip', md5(random_bytes(2)));
			file_put_contents($this->filePath, $rawResponse[0]);
		}
	}

	public function getFilePath(): string
	{
		return $this->filePath;
	}
}
