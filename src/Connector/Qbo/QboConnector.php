<?php

namespace App\Connector\Qbo;

use QuickBooksOnline\API\Facades\Invoice;
use App\Service\LoggerService;
use App\Model\Utils\ParameterHelper;
use App\Linker\Services\RedisClients;
use QuickBooksOnline\API\Facades\Payment;
use QuickBooksOnline\API\Data\IPPAttachable;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2AccessToken;
use QuickBooksOnline\API\Data\IPPIntuitEntity;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class QboConnector
{
	public const MAX_ITEMS = 100;

	private mixed $accessToken = null;
	private ParameterHelper $parameterHelper;
	private mixed $dataServiceConfig;
	private LoggerService $loggerSrv;
	private RedisClients $redisClients;

	public function __construct(
		LoggerService $loggerSrv,
		ParameterBagInterface $parameterBag,
		RedisClients $redisClients,
		ParameterHelper $parameterHelper
	) {
		$this->parameterHelper = $parameterHelper;

		$this->dataServiceConfig = [
			'auth_mode' => 'oauth2',
			'ClientID' => $parameterBag->get('qbo.oauth_client_id'),
			'ClientSecret' => $parameterBag->get('qbo.oauth_client_secret'),
			'RedirectURI' => $parameterBag->get('app.base_url').'/qbo/oauth/redirect',
			'scope' => $parameterBag->get('qbo.oauth_scopes'),
			'baseUrl' => $parameterBag->get('qbo.api_url'),
		];

		$redisToken = $redisClients->redisMainDB->get(RedisClients::SESSION_KEY_QBO_TOKEN);
		if ($redisToken){
			$this->accessToken = unserialize(base64_decode($redisToken));
		}
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_CONNECTORS);
	}

	public function getToken(string $code, string $realmId): void
	{
		try {
			$dataService = DataService::Configure($this->dataServiceConfig);
			$oAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

			// Update the OAuth2Token
			$accessToken = $oAuth2LoginHelper->exchangeAuthorizationCodeForToken($code, $realmId);
			$dataService->updateOAuth2Token($accessToken);

			// Setting the accessToken for session variable
			$this->accessToken = $accessToken;

			$this->redisClients->redisMainDB->set(RedisClients::SESSION_KEY_QBO_TOKEN, base64_encode(serialize($this->accessToken)));
		} catch (\Throwable $thr) {
		}
	}

	public function refreshToken(): bool
	{
		try {
			$dataService = DataService::Configure(array_merge($this->dataServiceConfig, [
				'refreshTokenKey' => $this->accessToken->getRefreshToken(),
				'QBORealmID' => ($this->accessToken instanceof OAuth2AccessToken) ? $this->accessToken->getRealmID() : '',
			]));

			$oAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
			$refreshedAccessTokenObj = $oAuth2LoginHelper->refreshToken();
			$dataService->updateOAuth2Token($refreshedAccessTokenObj);
			$this->accessToken = $refreshedAccessTokenObj;
			$this->redisClients->redisMainDB->set(RedisClients::SESSION_KEY_QBO_TOKEN, base64_encode(serialize($this->accessToken)));
			$this->parameterHelper->set('token', base64_encode(serialize($this->accessToken)), 'qbo');
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error refreshing QBO token', $thr);
		}

		return true;
	}

	public function isTokenExpired(): bool
	{
		try {
			if (!$this->accessToken) {
				return true;
			}
			$expireDate = $this->accessToken->getAccessTokenExpiresAt();
			if ((strtotime($expireDate) - 60) <= time()) {
				return true;
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error trying to check if QBO token is expired', $thr);

			return false;
		}

		return false;
	}

	/**
	 * @return string|null
	 */
	public function getAuthUrl()
	{
		try {
			$dataService = DataService::Configure($this->dataServiceConfig);

			$oAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

			return $oAuth2LoginHelper->getAuthorizationCodeURL();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting QBO Auth Url', $thr);
		}

		return null;
	}

	public function makeApiCall(string $method, array $params = []): ?object
	{
		if ($this->accessToken instanceof OAuth2AccessToken) {
			try {
				if ($this->isTokenExpired()) {
					$this->refreshToken();
				}
				$dataService = DataService::Configure($this->dataServiceConfig);
				$dataService->updateOAuth2Token($this->accessToken);
				if (method_exists($dataService, $method)) {
					return call_user_func_array([$dataService, $method], $params);
				}
				throw new \LogicException('No such method');
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError("Error calling QBO API function $method", $thr);
			}
		}

		return null;
	}

	public function findById(string $entity, string $id): ?object
	{
		return $this->makeApiCall('FindById', [$entity, $id]);
	}

	public function createInvoicePayment(array $createData): ?IPPIntuitEntity
	{
		if ($this->accessToken instanceof OAuth2AccessToken) {
			try {
				if ($this->isTokenExpired()) {
					$this->refreshToken();
				}
				$dataService = DataService::Configure($this->dataServiceConfig);
				$dataService->updateOAuth2Token($this->accessToken);
				$paymentObj = Payment::create($createData);
				$createResponse = $dataService->Add($paymentObj);
				$error = $dataService->getLastError();
				if ($error) {
					$this->loggerSrv->addCritical('Error while creating Payment', [
						'code' => $error->getHttpStatusCode(),
						'message' => $error->getOAuthHelperError(),
						'body' => $error->getResponseBody(),
					]);

					return null;
				}

				return $createResponse;
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError('Error calling QBO API function createInvoicePayment', $thr);
			}
		}

		return null;
	}

	public function createInvoice(array $createData): ?IPPIntuitEntity
	{
		if ($this->accessToken instanceof OAuth2AccessToken) {
			try {
				if ($this->isTokenExpired()) {
					$this->refreshToken();
				}
				$dataService = DataService::Configure($this->dataServiceConfig);
				$dataService->updateOAuth2Token($this->accessToken);
				$invoiceObj = Invoice::create($createData);
				$createResponse = $dataService->Add($invoiceObj);
				$error = $dataService->getLastError();
				if ($error) {
					$this->loggerSrv->addCritical('Error while creating Invoice', [
						'code' => $error->getHttpStatusCode(),
						'message' => $error->getOAuthHelperError(),
						'body' => $error->getResponseBody(),
					]);

					return $error;
				}

				return $createResponse;
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError('Error calling QBO API function createInvoice', $thr);
			}
		}

		return null;
	}

	public function createAttachment(array $file, IPPAttachable $objAttachable): ?array
	{
		if ($this->accessToken instanceof OAuth2AccessToken) {
			try {
				if ($this->isTokenExpired()) {
					$this->refreshToken();
				}
				$dataService = DataService::Configure($this->dataServiceConfig);
				$dataService->updateOAuth2Token($this->accessToken);
				$createResponse = $dataService->Upload(
					$file['contents'],
					$file['name'],
					$file['mimeType'],
					$objAttachable
				);

				$error = $dataService->getLastError();
				if ($error) {
					$this->loggerSrv->addCritical('Error while creating Attachment', [
						'code' => $error->getHttpStatusCode(),
						'message' => $error->getOAuthHelperError(),
						'body' => $error->getResponseBody(),
					]);

					return $error;
				}

				return $createResponse;
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError('Error calling QBO API function createAttachment', $thr);
			}
		}

		return null;
	}

	public function findAll(string $entity, int $pageNumber = 0, int $pageSize = 500): ?object
	{
		return $this->makeApiCall('FindAll', [$entity, $pageNumber, $pageSize]);
	}
}
