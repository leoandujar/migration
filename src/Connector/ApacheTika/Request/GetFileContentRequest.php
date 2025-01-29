<?php

namespace App\Connector\ApacheTika\Request;

class GetFileContentRequest extends Request
{
	protected string $requestMethod 	= 'PUT';
	protected string $requestUri    	= '/tika';
	protected string $type          	= Request::TYPE_BINARY_FILE;

	public function __construct(string $filepath, ?string $mime, ?bool $ocr = false)
	{
		$this->params                                   = fopen($filepath, 'r');
		$this->headers['X-Tika-PDFextractInlineImages'] = true;
		$this->headers['X-Tika-OCRTimeoutseconds'] 		= 600;
		$this->headers['Accept']                        = '*/*';

		if (!$ocr) {
			$this->headers['X-Tika-OCRskipOcr'] = true;
		}
		if ($mime) {
			$this->headers['Content-Type'] = $mime;
		}

		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
