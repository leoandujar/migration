<?php

namespace App\Connector\AzureCognitive;

use App\Connector\AzureCognitive\Request\AnalyzeDocumentRequest;
use App\Connector\AzureCognitive\Request\AnalyzeResultsRequest;
use App\Connector\AzureCognitive\Request\Request;
use App\Connector\AzureCognitive\Response\AnalyzeDocumentResponse;
use App\Connector\AzureCognitive\Response\AnalyzeResultsResponse;
use App\Service\LoggerService;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AzureVisionConnector
{
	private string $url;
	private string $token;
	private ?GuzzleClient $client = null;
	private LoggerService $loggerSrv;

	/**
	 * Connector constructor.
	 */
	public function __construct(LoggerService $loggerSrv, ParameterBagInterface $bag)
	{
		$this->url = $bag->get('az.cognitive.vision.endpoint');
		$this->token = $bag->get('az.cognitive.vision.key');
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_CONNECTORS);
	}

	/**
	 * @throws GuzzleException
	 * @throws \Throwable
	 */
	protected function sendRequest(Request $request, string $responseClass, bool $skipLogs = false, bool $returnHeaders = false, bool $absoluteUrl = false): mixed
	{
		try {
			$headers = ['Ocp-Apim-Subscription-Key' => $this->token];
			$headers = array_merge($headers, $request->getHeaders());
			switch ($request->getType()) {
				case Request::TYPE_JSON:
					$options['body'] = json_encode($request->getParams());
					break;
				case Request::TYPE_FORM:
					$options['form_params'] = $request->getParams();
					break;
				case Request::TYPE_BINARY:
					$options['body'] = $request->getBody();
					break;
				default:
					$options['multipart'] = $request->getParams();
					break;
			}

			$options['headers'] = $headers;
			if (null === $this->client) {
				$this->client = new GuzzleClient([
					RequestOptions::CONNECT_TIMEOUT => $request->getTimeout(),
					RequestOptions::READ_TIMEOUT => $request->getTimeout(),
					RequestOptions::TIMEOUT => $request->getTimeout(),
					RequestOptions::HTTP_ERRORS => false,
				]);
			}
			$url = $absoluteUrl ? $request->getRequestUri() : "$this->url{$request->getRequestUri()}";
			$response = $this->client->request(
				$request->getRequestMethod(),
				$url,
				$options
			);
			$responseString = $response->getBody()->getContents();
			$responseBody = json_decode($responseString, true);
			if ($returnHeaders) {
				$responseBody = $response->getHeaders();
			}
			$this->loggerSrv->addInfo(sprintf('Request sent to Azure Cognitive Vision: %s, request: %s', $request->getRequestUri(), json_encode($request->getParams())));
			if (empty($responseBody)) {
				$responseBody = [];
			}
			switch ($response->getStatusCode()) {
				case 400:
				case 404:
					$this->loggerSrv->addError(sprintf('An error has happened sending request to Azure Cognitive Vision: %s, request: %s, response: %s', $request->getRequestUri(), json_encode($request->getParams()), $responseString));
					break;
				case 200:
					break;
				default:
					$this->loggerSrv->addInfo(sprintf('Response from Azure Cognitive Vision: %s, request: %s, response: %s: %s', $request->getRequestUri(), json_encode($request->getParams()), $response->getStatusCode(), $responseString));
					break;
			}
			if (!$skipLogs) {
				$this->loggerSrv->addInfo(sprintf('Succesful response to Azure Cognitive Vision: %s, response: %s', $request->getRequestUri(), $responseString));
			}

			return new $responseClass($response->getStatusCode(), $responseBody);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error sending request to Azure Cognitive Vision Api', $thr);
			throw $thr;
		}
	}

	public function analyzeDocument(string $url, string $pages = null, string $modelId = 'prebuilt-read', string $locale = 'en-US'): AnalyzeDocumentResponse|AnalyzeResultsResponse|null
	{
		$queryParams = [
			'pages' => $pages,
			'locale' => $locale,
			'api-version' => '2023-07-31',
		];
		$bodyParams = [
			'urlSource' => $url,
		];
		$request = new AnalyzeDocumentRequest($modelId, $bodyParams, $queryParams);
		try {
			$response = $this->sendRequest($request, AnalyzeDocumentResponse::class, false, true);
			$data = $response->getData();
			if (isset($data['apim-request-id'])) {
				return $this->getAnalyzeResults($modelId, $data['apim-request-id'][0]);
			}

			return $response;
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request analyzeDocument to Azure Cognitive Vision Api', $thr);
		}

		return null;
	}

	public function getAnalyzeResults(string $modelId, string $resultId): AnalyzeResultsResponse|null
	{
		$request = new AnalyzeResultsRequest($modelId, $resultId);
		try {
			$response = $this->sendRequest($request, AnalyzeResultsResponse::class, true);
			if ($response->isSuccessfull()) {
				$data = $response->getData();
				if (isset($data['status'])) {
					if ('succeeded' == $data['status']) {
						return $response;
					}
					if ('failed' == $data['status']) {
						$this->loggerSrv->addError('Error: sending request getAnalyzeResults to Azure Cognitive Vision Api', $data);

						return null;
					}
					usleep(5000000);

					return $this->getAnalyzeResults($modelId, $resultId);
				}

				return $response;
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getAnalyzeResults to Azure Cognitive Vision Api', $thr);
		}

		return null;
	}
}
