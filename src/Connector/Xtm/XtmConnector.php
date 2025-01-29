<?php

namespace App\Connector\Xtm;

use App\Connector\Xtm\Request\FileDownloadRequest;
use App\Connector\Xtm\Request\FileStatusRequest;
use App\Connector\Xtm\Request\ProjectFileRequest;
use App\Connector\Xtm\Response\FileStatusResponse;
use App\Connector\Xtm\Response\ProjectFileResponse;
use App\Service\LoggerService;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client as GuzzleClient;
use App\Connector\Xtm\Request\Request;
use App\Connector\Xtm\Response\Response;
use App\Connector\Xtm\Request\LoginRequest;
use App\Connector\Xtm\Response\LoginResponse;
use App\Connector\Xtm\Response\ProjectsResponse;
use App\Connector\Xtm\Request\ProjectByIdRequest;
use App\Connector\Xtm\Request\CreateProjectRequest;
use App\Connector\Xtm\Response\ProjectByIdResponse;
use App\Connector\Xtm\Response\ProjectsCountResponse;
use App\Connector\Xtm\Request\StatsByProjectIdRequest;
use App\Connector\Xtm\Request\MetricsByProjectIdRequest;
use App\Connector\Xtm\Request\ProjectsByCriteriaRequest;
use App\Connector\Xtm\Response\StatsByProjectIdResponse;
use App\Connector\Xtm\Request\DownloadFilesByLangRequest;
use App\Connector\Xtm\Response\MetricsByProjectIdResponse;
use App\Connector\Xtm\Response\DownloadFilesByLangResponse;
use App\Connector\Xtm\Request\TranslationMemoryStatusRequest;
use App\Connector\Xtm\Request\DownloadTranslationMemoryRequest;
use App\Connector\Xtm\Request\GenerateTranslationMemoryRequest;
use App\Connector\Xtm\Response\TranslationMemoryStatusResponse;
use App\Connector\Xtm\Response\DownloadTranslationFilesResponse;
use App\Connector\Xtm\Response\GenerateTranslationFilesResponse;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class XtmConnector
{
	public const MAX_PER_PAGE = 200;
	private const STATUS_FINISHED = 'FINISHED';

	private LoggerService $loggerSrv;
	private ParameterBagInterface $bag;
	private string $url;

	public function __construct(
		ParameterBagInterface $bag,
		LoggerService $loggerSrv,
	) {
		$this->bag = $bag;
		$this->loggerSrv = $loggerSrv;
		$this->url = $bag->get('app.xtm.url');
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_CONNECTORS);
	}

	/**
	 * @throws \Throwable
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	protected function sendRequest(Request $request, string $responseClass, bool $skipLogs = false): mixed
	{
		try {
			$options = ['headers' => $request->getHeaders()];
			if (Request::TYPE_JSON === $request->getType()) {
				$options['body'] = json_encode($request->getParams());
			} elseif (Request::TYPE_FORM === $request->getType()) {
				$options['form_params'] = $request->getParams();
			} else {
				$options['multipart'] = $request->getParams();
			}

			$client = new GuzzleClient([
				RequestOptions::CONNECT_TIMEOUT => $request->getTimeout(),
				RequestOptions::READ_TIMEOUT => $request->getTimeout(),
				RequestOptions::TIMEOUT => $request->getTimeout(),
				RequestOptions::HTTP_ERRORS => false,
			]);
			$response = $client->request($request->getRequestMethod(), "$this->url{$request->getRequestUri()}", $options);
			$responseString = $response->getBody()->getContents();
			$responseBody = json_decode($responseString, true);
			if (empty($responseBody)) {
				$responseBody = [$responseString];
			}
			if (!is_array($responseBody)) {
				$responseBody = [$responseBody];
			}
			if (!$skipLogs) {
				$this->loggerSrv->addInfo('Received response: '.$responseString);
			}

			return new $responseClass($response->getStatusCode(), $responseBody, $response->getHeaders());
		} catch (\Throwable $thr) {
			throw $thr;
		}
	}

	/**
	 * @return ?string
	 */
	private function getToken(): ?string
	{
		$authToken = $this->bag->get('app.xtm.login_token');

		if (null === $authToken) {
			$response = $this->login();
			if (null === $response || !$response->isSuccessfull() || empty($response->getToken())) {
				$msg = 'Unable to login and obtain token into XTM Api.';
				$this->loggerSrv->addError($msg);

				return null;
			}
			$authToken = $response->getToken();
		}

		return $authToken;
	}

	/**
	 * @return LoginResponse
	 */
	public function login(): ?Response
	{
		$clientId = $this->bag->get('app.xtm.company');
		$userId = $this->bag->get('app.xtm.user.id');
		$password = $this->bag->get('app.xtm.password');
		$request = new LoginRequest($clientId, $userId, $password);
		try {
			/* @var LoginResponse $response */
			return $this->sendRequest($request, LoginResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request to login on XTM Connector', $thr);
		}

		return null;
	}

	/**
	 * @return ProjectsCountResponse
	 */
	public function projectsCount(?string $finishedDate): ?Response
	{
		$params = [
			'page' => 1,
			'pageSize' => 1,
			'status' => self::STATUS_FINISHED,
		];
		if (null !== $finishedDate) {
			$params['finishedDateFrom'] = $finishedDate;
		}
		try {
			$authToken = $this->getToken();
			if (null === $authToken) {
				return null;
			}
			$request = new ProjectsByCriteriaRequest($params, $authToken);
			return $this->sendRequest($request, ProjectsCountResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request to count projects on XTM Connector', $thr);
		}

		return null;
	}

	/**
	 * @return ProjectsResponse
	 */
	public function getProjects(int $page, $finishedDate = null): ?Response
	{
		$params = [
			'page' => $page,
			'pageSize' => self::MAX_PER_PAGE,
			'status' => self::STATUS_FINISHED,
		];
		if (null !== $finishedDate) {
			$params['finishedDateFrom'] = $finishedDate;
		}
		try {
			$authToken = $this->getToken();
			if (null === $authToken) {
				return null;
			}
			$request = new ProjectsByCriteriaRequest($params, $authToken);
			return $this->sendRequest($request, ProjectsResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request to retrieve projects on XTM Connector', $thr);
		}

		return null;
	}

	/**
	 * @return ProjectByIdResponse
	 */
	public function getProjectById(int $projectId): ?Response
	{
		try {
			$authToken = $this->getToken();
			if (null === $authToken) {
				return null;
			}
			$request = new ProjectByIdRequest($projectId, $authToken);

			return $this->sendRequest($request, ProjectByIdResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getProjectById to XTM Connector', $thr);
		}

		return null;
	}

	/**
	 * @return Response
	 */
	public function createProject($requestParams): ?Response
	{
		try {
			$authToken = $this->getToken();
			if (null === $authToken) {
				return null;
			}
			$params = [];
			foreach ($requestParams as $key => $requestParam) {
				$params[] = [
					'name' => $key,
					'contents' => $requestParam,
				];
			}
			$request = new CreateProjectRequest($authToken, $params);

			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request to create project on XTM Connector', $thr);
		}

		return null;
	}

	public function downloadFilesByProjectId(int $projectId, string $lang): ?DownloadFilesByLangResponse
	{
		try {
			$authToken = $this->getToken();
			if (null === $authToken) {
				return null;
			}
			$request = new DownloadFilesByLangRequest($projectId, $lang, $authToken);

			return $this->sendRequest($request, DownloadFilesByLangResponse::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request to download files by project name on XTM Connector', $thr);
		}

		return null;
	}

	/**
	 * @return MetricsByProjectIdResponse
	 */
	public function getMetricsByProjectId(int $projectId): ?Response
	{
		try {
			$authToken = $this->getToken();
			if (null === $authToken) {
				return null;
			}
			$request = new MetricsByProjectIdRequest($projectId, $authToken);

			return $this->sendRequest($request, MetricsByProjectIdResponse::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request to get metrics by project id to XTM Connector', $thr);
		}

		return null;
	}

	/**
	 * @return ProjectFileResponse
	 */
	public function generateProjectFile(int $projectId, string $languageCode = null, string $fileType = ProjectFileRequest::FILE_TYPE_LQA_REPORT): ?Response
	{
		try {
			$authToken = $this->getToken();
			if (null === $authToken) {
				return null;
			}

			$params = [];
			$queryParams = [];

			if (null !== $languageCode) {
				$queryParams['targetLanguages'] = $languageCode;
			}

			if (null !== $fileType) {
				$queryParams['fileType'] = $fileType;
			}
			if (ProjectFileRequest::FILE_TYPE_EXCEL_EXTENDED_TABLE === $fileType) {
				$params = [
					'extendedTableOptions' => [
						'excelOptions' => [
							'extendedReportType' => 'ALL_PROJECT_FILES_SINGLE_REPORT',
							'includeComments' => 'DO_NOT_INCLUDE',
							'includeEditDistanceScore' => 'INCLUDE',
							'includeFinalText' => 'DO_NOT_INCLUDE',
							'includeLQAErrors' => 'DO_NOT_INCLUDE',
							'includeMatches' => 'DO_NOT_INCLUDE',
							'includeOnlySegmentsWithQaWarnings' => 'DO_NOT_INCLUDE',
							'includePostEditedText' => 'DO_NOT_INCLUDE',
							'includePreTranslatedText' => 'DO_NOT_INCLUDE',
							'includeQaWarnings' => 'DO_NOT_INCLUDE',
							'includeRevisions' => 'DO_NOT_INCLUDE',
							'includeSegmentId' => 'INCLUDE',
							'includeSegmentKey' => 'DO_NOT_INCLUDE',
							'includeSource' => 'DO_NOT_INCLUDE',
							'includeTarget' => 'DO_NOT_INCLUDE',
							'includeXTMStatus' => 'DO_NOT_INCLUDE',
							'includeXliffDocStatus' => 'DO_NOT_INCLUDE',
							'languagesType' => 'SELECTED_LANGUAGES',
							'populateTargetWithSource' => 'DO_NOT_POPULATE',
						],
					],
				];
			}

			$request = new ProjectFileRequest($projectId, $queryParams, $params, $authToken);

			return $this->sendRequest($request, ProjectFileResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request to generateProjectLqa to XTM Connector', $thr);
		}

		return null;
	}

	/**
	 * @return FileStatusResponse
	 */
	public function checkProjectFile(int $projectId, array $fileIds, string $fileType = ProjectFileRequest::FILE_TYPE_LQA_REPORT, string $fileScope = 'PROJECT'): ?Response
	{
		try {
			$authToken = $this->getToken();
			if (null === $authToken) {
				return null;
			}

			$params = [
				'fileIds' => $fileIds,
				'fileType' => $fileType,
				'fileScope' => $fileScope,
			];
			$request = new FileStatusRequest($projectId, $params, $authToken);

			return $this->sendRequest($request, FileStatusResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request to checkProjectLqaFile to XTM Connector', $thr);
		}

		return null;
	}

	/**
	 * @return Response
	 */
	public function downloadProjectFile(int $projectId, string $fileId, string $fileType = ProjectFileRequest::FILE_TYPE_LQA_REPORT, string $fileScope = 'PROJECT'): ?Response
	{
		try {
			$authToken = $this->getToken();
			if (null === $authToken) {
				return null;
			}

			$params = [
				'fileScope' => $fileScope,
				'fileType' => $fileType,
			];
			$request = new FileDownloadRequest($projectId, $fileId, $params, $authToken);

			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request to checkProjectLqaFile to XTM Connector', $thr);
		}

		return null;
	}

	/**
	 * @return StatsByProjectIdResponse
	 */
	public function getStatsByProjectId(int $projectId): ?Response
	{
		try {
			$authToken = $this->getToken();
			if (null === $authToken) {
				return null;
			}
			$request = new StatsByProjectIdRequest($projectId, $authToken);

			return $this->sendRequest($request, StatsByProjectIdResponse::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request to get stats by project id to XTM Connector', $thr);
		}

		return null;
	}

	public function generateTranslationMemoryFile(array $params): ?Response
	{
		try {
			$authToken = $this->getToken();
			if (null === $authToken) {
				return null;
			}
			$request = new GenerateTranslationMemoryRequest($authToken, $params);

			return $this->sendRequest($request, GenerateTranslationFilesResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request generate translation memory file to XTM Connector', $thr);
		}

		return null;
	}

	public function downloadTranslationMemoryFiles($fileID): ?Response
	{
		try {
			$authToken = $this->getToken();
			if (null === $authToken) {
				return null;
			}
			$request = new DownloadTranslationMemoryRequest($authToken, $fileID);

			return $this->sendRequest($request, DownloadTranslationFilesResponse::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: downloading translation memory file to XTM Connector', $thr);
		}

		return null;
	}

	public function translationMemoryFileStatus($fileID): ?Response
	{
		try {
			$authToken = $this->getToken();
			if (null === $authToken) {
				return null;
			}
			$request = new TranslationMemoryStatusRequest($authToken, $fileID);

			return $this->sendRequest($request, TranslationMemoryStatusResponse::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: checking translation memory file status on XTM Connector', $thr);
		}

		return null;
	}
}
