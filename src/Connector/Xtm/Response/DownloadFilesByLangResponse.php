<?php

namespace App\Connector\Xtm\Response;

class DownloadFilesByLangResponse extends Response
{
	private const HEADER_FILE_DESCRIPTION_KEY = 'xtm-file-descrption';

	private array $filesData;

	public function __construct(int $httpCode, array $rawResponse, array $headers = [])
	{
		parent::__construct($httpCode, $rawResponse, $headers);

		if ($this->isSuccessfull()) {
			if ($headers[self::HEADER_FILE_DESCRIPTION_KEY]) {
				$data = json_decode(array_shift($headers[self::HEADER_FILE_DESCRIPTION_KEY]), true);
				foreach ($data as $datum) {
					$this->filesData[] = [
						'fileName'       => $datum['fileName'] ?? null,
						'targetLanguage' => $datum['targetLanguage'] ?? null,
					];
				}
			}
		}
	}

	public function getFilesData(): array
	{
		return $this->filesData;
	}
}
