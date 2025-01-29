<?php

namespace App\Connector\Boostlingo;

use App\Connector\Boostlingo\Request\AppointmentDictionariesRequest;
use App\Connector\Boostlingo\Request\AppointmentLogRequest;
use App\Connector\Boostlingo\Request\CallLogRequest;
use App\Connector\Boostlingo\Request\ClientRequest;
use App\Connector\Boostlingo\Request\ClientsUserListRequest;
use App\Connector\Boostlingo\Request\DictionariesRequest;
use App\Connector\Boostlingo\Request\InvoicesRequest;
use App\Connector\Boostlingo\Request\RefreshTokenRequest;
use App\Connector\Boostlingo\Request\Request;
use App\Connector\Boostlingo\Request\RetrieveClientRequest;
use App\Connector\Boostlingo\Request\RetrieveInvoiceRequest;
use App\Connector\Boostlingo\Request\SigninRequest;
use App\Connector\Boostlingo\Response\Response;
use App\Linker\Services\RedisClients;
use App\Service\LoggerService;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BoostlingoConnector
{
	private string $url;
	private string $blEmail;
	private string $blPassword;
	private ?GuzzleClient $client = null;
	private LoggerService $loggerSrv;
	private RedisClients $redisClients;

	public function __construct(
		ParameterBagInterface $bag,
		LoggerService $loggerSrv,
		RedisClients $redisClients,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->url = $bag->get('app.boostlingo.url');
		$this->blEmail = $bag->get('app.boostlingo.email');
		$this->blPassword = $bag->get('app.boostlingo.password');
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
			$options = ['headers' => $request->getHeaders()];
			if (Request::TYPE_JSON === $request->getType()) {
				if (null !== $request->getParams()) {
					$options['body'] = json_encode($request->getParams());
				}
			} elseif (Request::TYPE_FORM === $request->getType()) {
				$options['form_params'] = $request->getParams();
			} else {
				$options['multipart'] = $request->getParams();
			}

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
					$this->loggerSrv->addError(sprintf('An error has happened sending request to Boostlingo: %s', $responseString));
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

	public function signIn(): ?Response
	{
		$request = new SigninRequest($this->blEmail, $this->blPassword);
		try {
			$responseSignin = $this->sendRequest($request, Response::class);
			if (isset($responseSignin->getRaw()['token'])) {
				$this->updateSessionToken($responseSignin->getRaw()['token'], $responseSignin->getRaw()['refreshToken'], $responseSignin->getRaw()['expiresAt'], $responseSignin->getRaw()['companyAccountId']);

				return $responseSignin;
			}

			return null;
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request signin to Boostlingo', $thr);
		}

		return null;
	}

	public function refreshAccessToken(string $refreshToken): ?Response
	{
		$request = new RefreshTokenRequest($refreshToken);
		try {
			$responseRefreshToken = $this->sendRequest($request, Response::class);
			$this->updateSessionToken($responseRefreshToken->getRaw()['token'], $responseRefreshToken->getRaw()['refreshToken'], $responseRefreshToken->getRaw()['expiresAt'], $responseRefreshToken->getRaw()['companyAccountId']);

			return $responseRefreshToken;
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request refresh token to Boostlingo', $thr);
		}

		return null;
	}

	public function dictionaries(): ?Response
	{
		$token = $this->getToken();
		if (!$token) {
			return null;
		}
		$request = new DictionariesRequest($token);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request dictionaries to Boostlingo', $thr);
		}

		return null;
	}

	public function appointmentDictionaries(): ?Response
	{
		$token = $this->getToken();
		if (!$token) {
			return null;
		}
		$request = new AppointmentDictionariesRequest($token);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request appointment dictionaries to Boostlingo', $thr);
		}

		return null;
	}

	public function log(string $queryString): ?Response
	{
		$token = $this->getToken();
		if (!$token) {
			return null;
		}
		$request = new AppointmentLogRequest($queryString, $token);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request log to Boostlingo', $thr);
		}

		return null;
	}

	public function callLog(string $queryString): ?Response
	{
		$token = $this->getToken();
		if (!$token) {
			return null;
		}
		$request = new CallLogRequest($queryString, $token);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request call log to Boostlingo', $thr);
		}

		return null;
	}

	public function clientsUserList(string $queryString): ?Response
	{
		$token = $this->getToken();
		if (!$token) {
			return null;
		}
		$request = new ClientsUserListRequest($queryString, $token);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request clients user list to Boostlingo', $thr);
		}

		return null;
	}

	public function clientsClient(string $queryString): ?Response
	{
		$token = $this->getToken();
		if (!$token) {
			return null;
		}
		$request = new ClientRequest($queryString, $token);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request client to Boostlingo', $thr);
		}

		return null;
	}

	public function getInvoices(string $queryString): ?Response
	{
		$token = $this->getToken();
		if (!$token) {
			return null;
		}
		$request = new InvoicesRequest($queryString, $token);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request invoices to Boostlingo', $thr);
		}

		return null;
	}

	public function retrieveInvoice(int $invoiceId): ?Response
	{
		$token = $this->getToken();
		if (!$token) {
			return null;
		}
		$request = new RetrieveInvoiceRequest($invoiceId, $token);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request retrieve provider invoice to Boostlingo', $thr);
		}

		return null;
	}

	public function retrieveClient(string $clientId): ?Response
	{
		$token = $this->getToken();
		if (!$token) {
			return null;
		}
		$request = new RetrieveClientRequest($clientId, $token);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request retrieve client to Boostlingo', $thr);
		}

		return null;
	}

	public function getToken(): ?string
	{
		$token = $this->redisClients->redisMainDB->hmget(
			RedisClients::SESSION_KEY_BOOSTLINGO_TOKEN,
			['token']
		);
		if (!$token || (is_array($token) && 0 == count($token))) {
			return null;
		}

		return $token['token'];
	}

	public function getTokenExpiresAt(): ?string
	{
		$token = $this->redisClients->redisMainDB->hmget(
			RedisClients::SESSION_KEY_BOOSTLINGO_TOKEN,
			['expiresAt']
		);

		return $token['expiresAt'];
	}

	public function getCompanyAccountId(): ?string
	{
		$token = $this->redisClients->redisMainDB->hmget(
			RedisClients::SESSION_KEY_BOOSTLINGO_TOKEN,
			['companyAccountId']
		);
		if (!$token || (is_array($token) && 0 == count($token))) {
			return null;
		}

		return $token['companyAccountId'];
	}

	public function updateSessionToken(string $token, string $refreshToken, string $expiresAt, int $companyAccountId): void
	{
		$this->redisClients->redisMainDB->hmset(
			RedisClients::SESSION_KEY_BOOSTLINGO_TOKEN,
			[
				'token' => $token,
				'refreshToken' => $refreshToken,
				'expiresAt' => $expiresAt,
				'companyAccountId' => $companyAccountId,
			]
		);
	}
}
