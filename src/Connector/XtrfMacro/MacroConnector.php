<?php

namespace App\Connector\XtrfMacro;

use App\Connector\Xtrf\XtrfConnector;
use App\Connector\XtrfMacro\Request\MacroFileRequest;
use App\Connector\XtrfMacro\Request\MacroResultRequest;
use App\Connector\XtrfMacro\Request\MacroRunRequest;
use App\Connector\XtrfMacro\Request\MacroStatusRequest;
use App\Connector\XtrfMacro\Request\Request;
use App\Connector\XtrfMacro\Response\MacroResultResponse;
use App\Connector\XtrfMacro\Response\MacroRunResponse;
use App\Connector\XtrfMacro\Response\MacroStatusResponse;
use App\Connector\XtrfMacro\Response\Response;
use App\Service\LoggerService;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MacroConnector
{
	public const STATUS_COMPLETED = 'COMPLETED';
	public const STATUS_PENDING = 'PENDING';

	private int $countStatusCheck = 10;
	private XtrfConnector $xtrfConn;
	private ?string $url;
	private ?string $authToken;
	private ?GuzzleClient $client = null;
	private ?LoggerService $loggerSrv;

	public function __construct(XtrfConnector $xtrfConn, ParameterBagInterface $bag, LoggerService $loggerSrv)
	{
		$this->url = $bag->get('app.xtrf.api_url');
		$this->authToken = $bag->get('app.xtrf.auth_token');
		$this->loggerSrv = $loggerSrv;
		$this->xtrfConn = $xtrfConn;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_CONNECTORS);
	}

	protected function sendRequest(Request $request, string $responseClass, bool $skipLogs = false): ?Response
	{
		try {
			$headers = ['X-AUTH-ACCESS-TOKEN' => $this->authToken];
			$headers = array_merge($headers, $request->getHeaders());
			if (Request::TYPE_JSON === $request->getType()) {
				$options['body'] = json_encode($request->getParams());
			} elseif (Request::TYPE_FORM === $request->getType()) {
				$options['form_params'] = $request->getParams();
			} else {
				$options['multipart'] = $request->getParams();
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
			$response = $this->client->request(
				$request->getRequestMethod(),
				"$this->url{$request->getRequestUri()}",
				$options
			);
			$responseString = $response->getBody()->getContents();
			$responseBody = json_decode($responseString, true);
			if (empty($responseBody)) {
				$responseBody = [];
			}
			switch ($response->getStatusCode()) {
				case 400:
					$this->loggerSrv->addError(sprintf('An error has happened sending request to macro xtrf: %s', $responseString));
					break;
			}
			if (!$skipLogs) {
				$this->loggerSrv->addInfo('Received response: '.$responseString);
			}

			return new $responseClass($response->getStatusCode(), $responseBody);
		} catch (\Throwable $thr) {
			throw $thr;
		}
	}

	public function runMacro(string $macroId, array $ids = [], array $params = [], bool $async = true): ?Response
	{
		$data = [
			'ids' => $ids,
			'params' => $params,
			'async' => $async,
		];
		$request = new MacroRunRequest($macroId, $data);
		try {
			return $this->sendRequest($request, MacroRunResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request macro runMacro to Home Api', $thr);
		}

		return null;
	}

	public function checkStatus(string $actionId): ?Response
	{
		$request = new MacroStatusRequest($actionId);
		try {
			return $this->sendRequest($request, MacroStatusResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request macro checkStatus to Home Api', $thr);
		}

		return null;
	}

	public function checkStatusTilCompleted(string $actionId): mixed
	{
		$status = null;
		$countSend = $this->countStatusCheck;
		while ($countSend-- > 0) {
			$checkStatusResponse = $this->checkStatus($actionId);
			if ($checkStatusResponse->isSuccessfull()) {
				$status = $checkStatusResponse->state;
				if (self::STATUS_COMPLETED === $status) {
					return $status;
				}
			}
		}

		return $status;
	}

	public function getResult(string $actionId): ?Response
	{
		$request = new MacroResultRequest($actionId);
		try {
			return $this->sendRequest($request, MacroResultResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request macro getResult to Home Api', $thr);
		}

		return null;
	}

	public function getFile(string $token): ?Response
	{
		$request = new MacroFileRequest($token);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request macro getResult to Home Api', $thr);
		}

		return null;
	}

	public function getTokenFromUrl(string $url): ?string
	{
		$result = null;
		$queryStr = parse_url($url, PHP_URL_QUERY);
		parse_str($queryStr, $queryParams);
		if (count($queryParams)) {
			$result = array_shift($queryParams);
		}

		return $result;
	}
}
