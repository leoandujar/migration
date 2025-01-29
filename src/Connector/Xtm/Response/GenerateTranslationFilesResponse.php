<?php

namespace App\Connector\Xtm\Response;

class GenerateTranslationFilesResponse extends Response
{
	private string $fileID = '';

	public function __construct(int $httpCode, array $rawResponse, array $headers = [])
	{
		parent::__construct($httpCode, $rawResponse, $headers);

		if ($this->isSuccessfull()) {
			$this->fileID = $rawResponse['fileId'];
		}
	}

	public function getFileID(): string
	{
		return $this->fileID;
	}
}
