<?php

namespace App\Connector\Ocr;

use GuzzleHttp\Client;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class OcrConnector
{
	private Client $client;
	private ParameterBagInterface $bag;
	private LoggerService $loggerSrv;

	/**
	 * Connector constructor.
	 */
	public function __construct(LoggerService $loggerSrv, ParameterBagInterface $bag)
	{
		$this->client = new Client([
			'base_uri' => $bag->get('ocr.url'),
			'timeout' => 200,
		]);
		$this->bag = $bag;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_CONNECTORS);
	}

	/**
	 * @param string $languages
	 * @param string $outputFormat
	 *
	 * @return array|string
	 *
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function send($file, $languages = 'english,german,spanish', $outputFormat = 'pdf,txt', $newline = false, $gettext = 'true', $pagerange = null, $retry = false)
	{
		$params = [
			'gettext' => "$gettext",
			'newline' => $newline,
		];
		if ($pagerange) {
			$params['pagerange'] = $pagerange;
		}
		if ($languages) {
			$params['languages'] = $languages;
		}
		if ($outputFormat) {
			$params['outputformat'] = $outputFormat;
		}
		$rsp = $this->proccess($file, $params);

		if (Response::HTTP_BAD_REQUEST === $rsp['status']) {
			if ($retry) {
				$params['pagerange'] = '1';
				$rsp = $this->proccess($file, $params);
			}
		}

		if (Response::HTTP_OK === $rsp['status']) {
			return $rsp['data'];
		}

		return [];
	}

	private function proccess($file, $params): array
	{
		$url = sprintf('%s?%s', $this->bag->get('ocr.url'), http_build_query($params));
		$fp = fopen($file, 'r');
		$response = $this->client->post($url, [
			'auth' => [$this->bag->get('ocr.username'), $this->bag->get('ocr.license.code')],
			'http_errors' => false,
			'multipart' => [
				[
					'name' => 'file',
					'contents' => $fp,
					'filename' => basename($file),
				],
			],
		]);
		$responseData = $response->getBody()->getContents();
		$rsp = [];
		switch ($response->getStatusCode()) {
			case Response::HTTP_OK:
				$rsp['status'] = Response::HTTP_OK;
				$data = json_decode($responseData, true);
				$this->loggerSrv->addInfo("OCR Web service Available pages: {$data['AvailablePages']}");
				if (!empty($data['OCRText'])) {
					$rsp['data'] = $data['OCRText'];

					return $rsp;
				}
				foreach ($data as $key => $datum) {
					if (str_contains($key, 'OutputFileUrl') && '' !== $datum) {
						$rsp['data'][basename($datum)] = file_get_contents($datum);
					}
				}

				return $rsp;
			case Response::HTTP_BAD_REQUEST:
				$rsp['status'] = Response::HTTP_BAD_REQUEST;
				$data = json_decode($response->getBody(), true);
				$this->loggerSrv->addError("bad request sent to ocr api with error: {$data['ErrorMessage']}");
				break;
			case Response::HTTP_UNAUTHORIZED:
				$rsp['status'] = Response::HTTP_UNAUTHORIZED;
				$this->loggerSrv->addError('unauthorized request to ocr. Check user and license code.');
				break;
			case Response::HTTP_PAYMENT_REQUIRED:
				$rsp['status'] = Response::HTTP_PAYMENT_REQUIRED;
				$this->loggerSrv->addError('OCR payment is required');
				break;
			default:
				$rsp['status'] = Response::HTTP_INTERNAL_SERVER_ERROR;
				$this->loggerSrv->addError('internal server error in communication with ocr');
		}

		return $rsp;
	}
}
