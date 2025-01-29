<?php

namespace App\Connector\CustomerPortal;

use App\Apis\CustomerPortal\Security\RedisUserTrait;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Exception\XtrfSessionException;
use App\Connector\CustomerPortal\Dto\ContactPersonXtrfDto;
use App\Connector\CustomerPortal\Dto\FeedbackDto;
use App\Connector\CustomerPortal\Request\ChangePasswordRequest;
use App\Connector\CustomerPortal\Request\DeleteFilesTaskReviewRequest;
use App\Connector\CustomerPortal\Request\DeleteProjectFileRequest;
use App\Connector\CustomerPortal\Request\DownloadProjectFileRequest;
use App\Connector\CustomerPortal\Request\GetContactPersonRequest;
use App\Connector\CustomerPortal\Request\GetFilesTasksReviewRequest;
use App\Connector\CustomerPortal\Request\GetProjectFeedbackRequest;
use App\Connector\CustomerPortal\Request\GetProjectFilesRequest;
use App\Connector\CustomerPortal\Request\GetProjectsReviewRequest;
use App\Connector\CustomerPortal\Request\GetTasksReviewRequest;
use App\Connector\CustomerPortal\Request\LanguagesListRequest;
use App\Connector\CustomerPortal\Request\LoginRequest;
use App\Connector\CustomerPortal\Request\LoginWithTokenRequest;
use App\Connector\CustomerPortal\Request\LogoutRequest;
use App\Connector\CustomerPortal\Request\PriceprofileListRequest;
use App\Connector\CustomerPortal\Request\ProjectCommentTaskReviewRequest;
use App\Connector\CustomerPortal\Request\ProjectConfirmationFileRequest;
use App\Connector\CustomerPortal\Request\ProjectCreateRequest;
use App\Connector\CustomerPortal\Request\ProjectDownloadFileByIdRequest;
use App\Connector\CustomerPortal\Request\ProjectDownloadOutFileRequest;
use App\Connector\CustomerPortal\Request\ProjectDownloadTaskReviewFileRequest;
use App\Connector\CustomerPortal\Request\ProjectSubmitFeedbackRequest;
use App\Connector\CustomerPortal\Request\QuoteAcceptDeclineRequest;
use App\Connector\CustomerPortal\Request\QuoteAcceptRequest;
use App\Connector\CustomerPortal\Request\QuoteConfirmationFileRequest;
use App\Connector\CustomerPortal\Request\QuoteCreateRequest;
use App\Connector\CustomerPortal\Request\QuoteGeneratePdfRequest;
use App\Connector\CustomerPortal\Request\RecoveryPasswordSendEmailRequest;
use App\Connector\CustomerPortal\Request\Request;
use App\Connector\CustomerPortal\Request\ResetPasswordRequest;
use App\Connector\CustomerPortal\Request\ServicesListRequest;
use App\Connector\CustomerPortal\Request\SessionRequest;
use App\Connector\CustomerPortal\Request\SpecializationsListRequest;
use App\Connector\CustomerPortal\Request\SubmitComplaintRequest;
use App\Connector\CustomerPortal\Request\TaskDownloadInFileRequest;
use App\Connector\CustomerPortal\Request\TaskDownloadOutFileRequest;
use App\Connector\CustomerPortal\Request\TokenValidateRequest;
use App\Connector\CustomerPortal\Request\UpdateContactPersonRequest;
use App\Connector\CustomerPortal\Request\UploadFilesTaskReviewRequest;
use App\Connector\CustomerPortal\Request\UploadProjectFileRequest;
use App\Connector\CustomerPortal\Response\CreateProjectResponse;
use App\Connector\CustomerPortal\Response\CreateQuoteResponse;
use App\Connector\CustomerPortal\Response\GetContactPersonResponse;
use App\Connector\CustomerPortal\Response\LanguagesListResponse;
use App\Connector\CustomerPortal\Response\LoginResponse;
use App\Connector\CustomerPortal\Response\PriceprofileListResponse;
use App\Connector\CustomerPortal\Response\Response;
use App\Connector\CustomerPortal\Response\RetrieveProjectFileResponse;
use App\Connector\CustomerPortal\Response\ServicesListResponse;
use App\Connector\CustomerPortal\Response\SessionResponse;
use App\Connector\CustomerPortal\Response\SpecializationsListResponse;
use App\Connector\CustomerPortal\Response\TokenValidateResponse;
use App\Connector\CustomerPortal\Response\UploadProjectFileResponse;
use App\Connector\Xtrf\XtrfConnector;
use App\Linker\Services\RedisClients;
use App\Service\LoggerService;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class CustomerPortalConnector
{
	use RedisUserTrait;

	private string $url;
	private ?GuzzleClient $client = null;
	private XtrfConnector $xtrfConn;
	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private RequestStack $requestStack;

	public function __construct(
		ParameterBagInterface $bag,
		LoggerService $loggerSrv,
		RequestStack $requestStack,
		RedisClients $redisClients,
		XtrfConnector $xtrfConn,
	) {
		$this->xtrfConn = $xtrfConn;
		$this->url = $bag->get('app.xtrf.customer.url');
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_CONNECTORS);
		$this->redisClients = $redisClients;
		$this->requestStack = $requestStack;
	}

	protected function sendRequest(Request $request, string $responseClass, bool $skipLogs = false): ?Response
	{
		try {
			$options = ['headers' => $request->getHeaders()];
			if (Request::TYPE_JSON === $request->getType()) {
				$options['body'] = json_encode($request->getParams());
			} else {
				if (Request::TYPE_FORM === $request->getType()) {
					$options['form_params'] = $request->getParams();
				} else {
					$options['multipart'] = $request->getParams();
				}
			}
			if (null === $this->client) {
				$this->client = new GuzzleClient(
					[
						RequestOptions::CONNECT_TIMEOUT => $request->getTimeout(),
						RequestOptions::READ_TIMEOUT => $request->getTimeout(),
						RequestOptions::TIMEOUT => $request->getTimeout(),
						RequestOptions::HTTP_ERRORS => false,
					]
				);
			}
			$response = $this->client->request(
				$request->getRequestMethod(),
				"$this->url{$request->getRequestUri()}",
				$options
			);
			$responseString = $response->getBody()->getContents();
			$responseBody = json_decode($responseString, true);
			if (empty($responseBody)) {
				if (!empty($responseString) && '[]' !== $responseString) {
					$responseBody = $responseString;
				} else {
					$responseBody = [];
				}
			}

			if (!is_array($responseBody)) {
				$responseBody = [$responseBody];
			}
			if (!$skipLogs) {
				$this->loggerSrv->addInfo(sprintf('Successful request to xtrf: %s, request: %s', $request->getRequestUri(), json_encode($request->getParams())));
				$this->loggerSrv->addInfo('Received response: '.$responseString);
			}

			if (HttpResponse::HTTP_UNAUTHORIZED === $response->getStatusCode()) {
				throw new XtrfSessionException(HttpResponse::HTTP_UNAUTHORIZED, HttpResponse::HTTP_UNAUTHORIZED);
			}

			return new $responseClass($response->getStatusCode(), $responseBody);
		} catch (\Throwable $thr) {
			if (HttpResponse::HTTP_UNAUTHORIZED === $thr->getCode()) {
				$this->loggerSrv->addWarning('Warning: session unauthorized. Trying to login again.', $thr);
			} else {
				$this->loggerSrv->addError('Error: sending request to Client Portal Api', $thr);
			}
			throw $thr;
		}
	}

	private function relogin(string $sessionId = null): ?string
	{
		if (!$sessionId) {
			$this->loggerSrv->addError('Error: Relogin function was called with empty sessionID.');

			return null;
		}
		$userData = $this->retrieveXtrfUserData();
		if (!$userData) {
			$this->loggerSrv->addError("Error: Relogin function could not get userData for sessionId=$sessionId.");

			return null;
		}
		$userId = $userData['id'] ?? null;
		$username = $userData['username'] ?? null;

		if (!$userId) {
			$this->loggerSrv->addError('Error: Relogin function could not get userId after unserialize.');

			return null;
		}
		if (!$username) {
			$this->loggerSrv->addError('Error: Relogin function could not get the username after unserialize.');

			return null;
		}
		$tokenResponse = $this->xtrfConn->getSingInToken($username);
		if (!$tokenResponse->isSuccessfull()) {
			$this->loggerSrv->addError("Error: Relogin function failed to in call to getSingInToken for username=$username.");

			return null;
		}

		$tokenRaw = $tokenResponse->getRaw();

		$loginResponse = $this->loginWithToken($tokenRaw['token']);
		if (!$loginResponse->isSuccessfull()) {
			$this->loggerSrv->addError("Error: Relogin function failed to in call to loginWithToken for token={$tokenRaw['token']}.");

			return null;
		}

		$rawData = $loginResponse->getRaw();
		if (empty($rawData)) {
			$this->loggerSrv->addError('Error: Relogin function received empty rawData from  loginWithToken function.');

			return null;
		}
		$sessionId = $rawData['jsessionid'];
		$this->saveXtrfUserData(
			userId: $userId,
			username: $username,
			xtrfSessionId: $sessionId
		);
		$this->loggerSrv->addInfo('Relogin was successfully executed.');

		return $sessionId;
	}

	public function login(string $username, string $password): ?LoginResponse
	{
		$request = new LoginRequest($username, $password);
		try {
			return $this->sendRequest($request, LoginResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error: sending request login $username to Client Portal Api", $thr);
			if ($thr instanceof XtrfSessionException) {
				throw new XtrfSessionException(ApiError::$descriptions[ApiError::CODE_XTRF_SESSION_EXPIRED], HttpResponse::HTTP_UNAUTHORIZED);
			}
		}

		return null;
	}

	public function logout(string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new LogoutRequest($sessionId);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request logout to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($relogin);
				}
			}
		}

		return null;
	}

	public function session(string $sessionId = null): ?SessionResponse
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new SessionRequest($sessionId);
		try {
			return $this->sendRequest($request, SessionResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request session to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($relogin);
				}
			}
		}

		return null;
	}

	public function getContactPerson(string $customerId, string $personId, string $sessionId = null): ?GetContactPersonResponse
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new GetContactPersonRequest($customerId, $personId, $sessionId);
		try {
			return $this->sendRequest($request, GetContactPersonResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getContactPerson to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($customerId, $personId, $relogin);
				}
			}
		}

		return null;
	}

	public function updateContactPerson(string $customerId, ContactPersonXtrfDto $contactPersonXtrfDto, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new UpdateContactPersonRequest($customerId, $contactPersonXtrfDto, $sessionId);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request updateContactPerson to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($customerId, $contactPersonXtrfDto, $relogin);
				}
			}
		}

		return null;
	}

	public function changePassword(string $oldPassword, string $newPassword, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new ChangePasswordRequest($sessionId, $oldPassword, $newPassword);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request changePassword to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($relogin);
				}
			}
		}

		return null;
	}

	public function recoveryPasswordSendEmail(string $email): ?Response
	{
		$request = new RecoveryPasswordSendEmailRequest($email);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request recoveryPasswordSendEmail to Client Portal Api', $thr);
		}

		return null;
	}

	public function validateToken(string $token): ?TokenValidateResponse
	{
		$request = new TokenValidateRequest($token);
		try {
			return $this->sendRequest($request, TokenValidateResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request validateToken to Client Portal Api', $thr);
		}

		return null;
	}

	public function resetPassword(string $accessToken, string $newPassword): ?Response
	{
		$request = new ResetPasswordRequest($accessToken, $newPassword);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request resetPassword to Client Portal Api', $thr);
		}

		return null;
	}

	public function getSpecializationList(string $sessionId = null): ?SpecializationsListResponse
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new SpecializationsListRequest($sessionId);
		try {
			return $this->sendRequest($request, SpecializationsListResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getSpecializationList to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($relogin);
				}
			}
		}

		return null;
	}

	public function getPriceprofileList(string $id, string $sessionId = null): ?PriceprofileListResponse
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new PriceprofileListRequest($id, $sessionId);
		try {
			return $this->sendRequest($request, PriceprofileListResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getPriceprofileList to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($id, $relogin);
				}
			}
		}

		return null;
	}

	public function getLanguagesList(string $sessionId = null): ?LanguagesListResponse
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new LanguagesListRequest($sessionId);
		try {
			return $this->sendRequest($request, LanguagesListResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getLanguagesList to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($relogin);
				}
			}
		}

		return null;
	}

	public function getServicesList(string $customerId, string $sessionId = null): ?ServicesListResponse
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new ServicesListRequest($customerId, $sessionId);
		try {
			return $this->sendRequest($request, ServicesListResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getServicesList to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($customerId, $relogin);
				}
			}
		}

		return null;
	}

	public function uploadProjectFile(array $params, string $sessionId = null): ?UploadProjectFileResponse
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new UploadProjectFileRequest($params, $sessionId);
		try {
			return $this->sendRequest($request, UploadProjectFileResponse::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request uploadProjectFile to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($params, $relogin);
				}
			}
		}

		return null;
	}

	public function retrieveProjectFiles(string $sessionId = null): ?RetrieveProjectFileResponse
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new GetProjectFilesRequest($sessionId);
		try {
			return $this->sendRequest($request, RetrieveProjectFileResponse::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request retrieveProjectFiles to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($relogin);
				}
			}
		}

		return null;
	}

	public function deleteProjectFile(string $fileId, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new DeleteProjectFileRequest($fileId, $sessionId);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request deleteProjectFile to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($fileId, $relogin);
				}
			}
		}

		return null;
	}

	public function donwloadProjectFile(string $fileId, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new DownloadProjectFileRequest($fileId, $sessionId);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error: sending request donwloadProjectFile to Client Portal Api $fileId and sessionId: $sessionId", $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($fileId, $relogin);
				}
			}
		}

		return null;
	}

	public function projectConfirmationFile(string $projectId, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new ProjectConfirmationFileRequest($projectId, $sessionId);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error: sending request projectConfirmationFile for project $projectId to Client Portal Api", $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($projectId, $relogin);
				}
			}
		}

		return null;
	}

	public function quoteConfirmationFile(string $quoteId, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new QuoteConfirmationFileRequest($quoteId, $sessionId);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request quoteConfirmationFile to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($quoteId, $relogin);
				}
			}
		}

		return null;
	}

	public function projectDownloadOutputFiles(string $projectId, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new ProjectDownloadOutFileRequest($projectId, $sessionId);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request projectDownloadOutputFiles to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($projectId, $relogin);
				}
			}
		}

		return null;
	}

	public function projectDownloadFileById(string $fileId, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new ProjectDownloadFileByIdRequest($fileId, $sessionId);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request projectDownloadFileById to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($fileId, $relogin);
				}
			}
		}

		return null;
	}

	public function getProjectsReview(string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new GetProjectsReviewRequest($sessionId);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getProjectsReview to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($relogin);
				}
			}
		}

		return null;
	}

	public function getTasksReview(string $taskId, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new GetTasksReviewRequest($taskId, $sessionId);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getTasksReview to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($taskId, $relogin);
				}
			}
		}

		return null;
	}

	public function getFilesTaskReview(string $taskId, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new GetFilesTasksReviewRequest($taskId, $sessionId);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getFilesTaskReview to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($taskId, $relogin);
				}
			}
		}

		return null;
	}

	public function uploadFilesTaskReview(string $taskId, array $params, string $sessionId = null): ?UploadProjectFileResponse
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new UploadFilesTaskReviewRequest($taskId, $params, $sessionId);
		try {
			return $this->sendRequest($request, UploadProjectFileResponse::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request uploadFilesTaskReview to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($taskId, $params, $relogin);
				}
			}
		}

		return null;
	}

	public function deleteFilesTaskReview(string $taskId, string $fileName, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new DeleteFilesTaskReviewRequest($taskId, $fileName, $sessionId);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request deleteFilesTaskReview to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($taskId, $fileName, $relogin);
				}
			}
		}

		return null;
	}

	public function projectDownloadTaskReviewFile(string $taskId, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new ProjectDownloadTaskReviewFileRequest($taskId, $sessionId);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request projectDownloadTaskReviewFile to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($taskId, $relogin);
				}
			}
		}

		return null;
	}

	public function projectCommentTaskReview(string $taskId, string $comment, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new ProjectCommentTaskReviewRequest($taskId, $comment, $sessionId);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request projectCommentTaskReview to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($taskId, $comment, $relogin);
				}
			}
		}

		return null;
	}

	public function createQuote(array $params, string $sessionId = null): ?CreateQuoteResponse
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new QuoteCreateRequest($params, $sessionId);
		try {
			return $this->sendRequest($request, CreateQuoteResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request createQuote to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($params, $relogin);
				}
			}
		}

		return null;
	}

	public function createProject(array $params, string $sessionId = null): ?CreateProjectResponse
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new ProjectCreateRequest($params, $sessionId);
		try {
			return $this->sendRequest($request, CreateProjectResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request createProject to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($params, $relogin);
				}
			}
		}

		return null;
	}

	public function projectTasksDownloadOutputFiles(string $taskId, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new TaskDownloadOutFileRequest($taskId, $sessionId);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error: sending request donwloadProjectFile for task $taskId to Client Portal Api", $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($taskId, $relogin);
				}
			}
		}

		return null;
	}

	public function tasksDownloadInputFiles(string $taskId, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new TaskDownloadInFileRequest($taskId, $sessionId);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request tasksDownloadInputFiles to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($taskId, $relogin);
				}
			}
		}

		return null;
	}

	public function projectSubmitComplaint(string $projectId, FeedbackDto $feedbackDto, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new SubmitComplaintRequest($projectId, $feedbackDto, $sessionId);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request projectSubmitComplaint to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($projectId, $feedbackDto, $relogin);
				}
			}
		}

		return null;
	}

	public function quoteAccept(string $quoteId, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new QuoteAcceptRequest($quoteId, $sessionId);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request quoteAccept to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($quoteId, $relogin);
				}
			}
		}

		return null;
	}

	public function quoteAcceptDecline(string $quoteId, array $params, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new QuoteAcceptDeclineRequest($quoteId, $params, $sessionId);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request quoteAcceptDecline to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($quoteId, $params, $relogin);
				}
			}
		}

		return null;
	}

	public function invoiceGeneratePdf(string $invoiceId, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new QuoteGeneratePdfRequest($invoiceId, $sessionId);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error: sending request invoiceGeneratePdf to Client Portal Api: $invoiceId session Id: $sessionId", $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($invoiceId, $relogin);
				}
			}
		}

		return null;
	}

	public function getProjectFeedback(string $projectId, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new GetProjectFeedbackRequest($projectId, $sessionId);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getProjectFeedback to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($projectId, $relogin);
				}
			}
		}

		return null;
	}

	public function submitProjectFeedback(string $projectId, array $params, string $sessionId = null): ?Response
	{
		$sessionId = $sessionId ?? $this->retrieveSessionId();
		$request = new ProjectSubmitFeedbackRequest($projectId, $params, $sessionId);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request submitProjectFeedback to Client Portal Api', $thr);
			if ($thr instanceof XtrfSessionException) {
				$relogin = $this->relogin($sessionId);
				if ($relogin) {
					$funct = __FUNCTION__;

					return $this->$funct($projectId, $params, $relogin);
				}
			}
		}

		return null;
	}

	public function loginWithToken(string $token): ?Response
	{
		$request = new LoginWithTokenRequest($token);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request submitProjectFeedback to Client Portal Api', $thr);
		}

		return null;
	}
}
