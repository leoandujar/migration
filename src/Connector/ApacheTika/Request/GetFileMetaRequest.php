<?php

namespace App\Connector\ApacheTika\Request;

class GetFileMetaRequest extends Request
{
	protected string $requestMethod = 'PUT';
	protected string $requestUri    = '/meta';
	protected string $type          = Request::TYPE_BINARY_FILE;

	public function __construct(string $filepath, string $mime)
	{
		$this->params                                   = fopen($filepath, 'r');
		$this->headers['Content-Type']                  = $mime;
		$this->headers['X-Tika-PDFextractInlineImages'] = true;
		$this->headers['Accept']                        = 'application/*';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
