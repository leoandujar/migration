<?php

namespace App\Connector\JsReport;

use App\Connector\JsReport\Request\RenderReportRequest;
use App\Service\LoggerService;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client as GuzzleClient;
use App\Connector\JsReport\Request\Request;
use App\Connector\JsReport\Response\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class JsReportConnector
{
	private string $url;
	private ?GuzzleClient $client = null;
	private LoggerService $loggerSrv;

	public function __construct(
		ParameterBagInterface $bag,
		LoggerService $loggerSrv
	) {
		$this->loggerSrv = $loggerSrv;
		$this->url = $bag->get('app.jsreport.url');
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_CONNECTORS);
	}

	protected function sendRequest(Request $request, string $responseClass, bool $skipLogs = false): Response
	{
		try {
			if (Request::TYPE_JSON === $request->getType()) {
				$options['body'] = json_encode($request->getParams());
			} elseif (Request::TYPE_BINARY_FILE === $request->getType()) {
				$options['body'] = $request->getParams();
			} elseif (Request::TYPE_FORM === $request->getType()) {
				$options['form_params'] = $request->getParams();
			} else {
				$options['multipart'] = $request->getParams();
			}
			$options['headers'] = $request->getHeaders();
			if (null === $this->client) {
				$this->client = new GuzzleClient([
					RequestOptions::CONNECT_TIMEOUT => 120,
					RequestOptions::READ_TIMEOUT => 120,
					RequestOptions::TIMEOUT => 120,
					RequestOptions::HTTP_ERRORS => false,
					RequestOptions::VERIFY => false,
				]);
			}
			$response = $this->client->request(
				$request->getRequestMethod(),
				"$this->url{$request->getRequestUri()}",
				$options
			);
			$responseString = $response->getBody()->getContents();
			$responseBody = json_decode($responseString, true);
			if ((JSON_ERROR_NONE !== json_last_error()) && !empty($responseString)) {
				$responseBody = [$responseString];
			}
			if (empty($responseBody)) {
				$responseBody = [];
			}
			if (!$skipLogs) {
				$this->loggerSrv->addInfo('Received response: '.$responseString);
			}

			return new $responseClass($response->getStatusCode(), $responseBody);
		} catch (\Throwable $thr) {
			throw $thr;
		}
	}

	public function render(string $templateId, array $data): ?Response
	{
		$request = new RenderReportRequest([
			'template' => [
				'shortid' => $templateId,
			],
			'data' => $data,
		]);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request render to REPORT API', $thr);
		}

		return null;
	}
}
