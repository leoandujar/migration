<?php

namespace App\Connector\Cloudflared;

use App\Service\LoggerService;
use GuzzleHttp\RequestOptions;
use App\Connector\Cloudflared\Response\Response;
use App\Connector\Cloudflared\Request\Request;
use App\Connector\Cloudflared\Request\GetDirectUploadRequest;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CloudflaredConnector
{
	private string $url;
	private string $token;
	private ?GuzzleClient $client = null;
	private LoggerService $loggerSrv;

	public function __construct(
		ParameterBagInterface $bag,
		LoggerService $loggerSrv
	) {
		$this->url = $bag->get('clouflared.images.url');
		$this->token = $bag->get('clouflared.images.token');
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_CONNECTORS);
	}

	/**
	 * @throws GuzzleException
	 * @throws \Throwable
	 */
	protected function sendRequest(Request $request, string $responseClass, bool $skipLogs = false): ?Response
	{
		try {
			$options['body'] = json_encode($request->getParams());
			$options['headers'] = $request->getHeaders();
			if (Request::TYPE_JSON === $request->getType()) {
				$options['body'] = json_encode($request->getParams());
			} elseif (Request::TYPE_FORM === $request->getType()) {
				$options['form_params'] = $request->getParams();
			} else {
				$options['multipart'] = $request->getParams();
			}
			if (null === $this->client) {
				$this->client = new GuzzleClient([
					RequestOptions::CONNECT_TIMEOUT => 120,
					RequestOptions::READ_TIMEOUT => 120,
					RequestOptions::TIMEOUT => 120,
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

	/**
	 * @return ?Response
	 */
	public function getDirectUpload(): ?Response
	{
		$request = new GetDirectUploadRequest($this->token);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: Generating upload request', $thr);
		}

		return null;
	}
}
