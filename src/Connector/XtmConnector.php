<?php

namespace App\Connector;

use App\Connector\Xtm\Request\ProjectFileRequest;
use App\Connector\Xtm\Request\ProjectLqaRequest;
use App\Service\FileSystem\FileSystemService;
use Psr\Log\LoggerInterface;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class XtmConnector
{
	public const MAX_PER_PAGE = 200;

	private $client;

	private $wsdl;

	/**
	 * @var array
	 */
	private $loginApi;

	private $logger;

	/**
	 * @var int
	 */
	private $timeout = 30;

	private $lastRequest;
	private FileSystemService $fileSystemSrv;

	public function __construct(
		ParameterBagInterface $parameterBag,
		LoggerInterface $logger,
		FileSystemService $fileSystemSrv
	) {
		$this->logger = $logger;
		$this->wsdl = $parameterBag->get('app.xtm.wsdl');

		$this->loginApi = [
			'client' => $parameterBag->get('app.xtm.company'),
			'username' => $parameterBag->get('app.xtm.username'),
			'password' => $parameterBag->get('app.xtm.password'),
		];
		$this->fileSystemSrv = $fileSystemSrv;
	}

	public function getProjectMetrics($projectId): mixed
	{
		$params = [
			'project' => [
				'id' => $projectId,
			],
		];

		return $this->sendRequest('obtainProjectMetrics', $params);
	}

	public function generateProjectLqa($projectId, string $languageCode = null): mixed
	{
		$params = [
			'project' => [
				'id' => $projectId,
			],
			'options' => [
				'fileType' => ProjectFileRequest::FILE_TYPE_LQA_REPORT,
			],
		];

		if (null !== $languageCode) {
			$params['options']['targetLanguage'] = $languageCode;
		}

		return $this->sendRequest('generateProjectFile', $params);
	}

	public function generateJobLqa($jobId): mixed
	{
		$params = [
			'jobs' => [
				'id' => $jobId,
			],
			'options' => [
				'fileType' => ProjectFileRequest::FILE_TYPE_LQA_REPORT,
			],
		];

		return $this->sendRequest('generateJobFile', $params);
	}

	public function checkProjectLqaFile($fileId): mixed
	{
		$params = [
			'files' => [
				'id' => $fileId,
			],
		];

		return $this->sendRequest('checkProjectFileCompletion', $params, true);
	}

	public function checkJobLqaFile($fileId): mixed
	{
		$params = [
			'files' => [
				'id' => $fileId,
			],
		];

		return $this->sendRequest('checkJobFileCompletion', $params, true);
	}

	public function getProjectLqaFile($fileId): mixed
	{
		$params = [
			'files' => [
				'id' => $fileId,
			],
		];

		return $this->sendRequest('downloadProjectFileURL', $params, true);
	}

	public function getJobLqaFile($fileId): mixed
	{
		$params = [
			'files' => [
				'id' => $fileId,
			],
		];

		return $this->sendRequest('downloadJobFileURL', $params, true);
	}

	public function downloadLqaFile(string $fileUrl): string
	{
		try {
			$path = $this->fileSystemSrv->filesPath.'/'.uniqid('xtm_file_');
			$filePath = fopen($path, 'w+');
			$client = $this->getClient();
			$client->get($fileUrl, ['sink' => $filePath]);

			$zip = new \ZipArchive();
			$zip->open($path);
			$filename = $zip->getNameIndex(0);
			$xlsxPath = tempnam($this->fileSystemSrv->filesPath, 'file.').'.LQA';
			$zip->extractTo($xlsxPath);
			$zip->close();

			unlink($path);

			return $xlsxPath.DIRECTORY_SEPARATOR.$filename;
		} catch (\Throwable $thr) {
			throw $thr;
		}
	}

	public function getProject($projectId): mixed
	{
		$params = [
			'filter' => [
				'projects' => [
					'id' => $projectId,
				],
			],
		];

		return $this->sendRequest('findProject', $params);
	}

	public function getProjects(int $page = 1): mixed
	{
		$params = [
			'options' => [
				'filter' => [
					'status' => 'FINISHED',
				],
				'pagination' => [
					'page' => $page,
					'pageSize' => self::MAX_PER_PAGE,
				],
			],
		];

		return $this->sendRequest('findProject', $params);
	}

	public function getProjectsNumber(): ?int
	{
		$params = [
			'options' => [
				'filter' => [
					'status' => 'FINISHED',
				],
				'pagination' => [
					'page' => 1,
					'pageSize' => 1,
				],
			],
		];

		$result = $this->sendRequest('findProject', $params);
		if (null === $result) {
			return null;
		}

		return intval($result->return->paginationDetails->totalItemsCount);
	}

	public function getProjectStatistics($projectId): mixed
	{
		$params = [
			'project' => [
				'id' => $projectId,
			],
		];

		return $this->sendRequest('obtainProjectAllStatistics', $params);
	}

	public function getLqa($projectId): mixed
	{
		$params = [
			'files' => [
				'id' => $projectId,
			],
		];

		return $this->sendRequest('checkProjectFileCompletion', $params);
	}

	public function getLastRequest(): mixed
	{
		return $this->lastRequest;
	}

	protected function getSoapClient(): mixed
	{
		if (empty($this->client)) {
			$context = stream_context_create([
				'http' => [
					'user_agent' => 'AvantPage Worker',
					'timeout' => $this->timeout,
				],
			]);

			$this->client = new \SoapClient($this->wsdl, [
				'stream_context' => $context,
				'soap_version' => SOAP_1_1,
				'exceptions' => true,
				'trace' => 1,
				'cache_wsdl' => WSDL_CACHE_BOTH,
			]);
		}

		return $this->client;
	}

	private function sendRequest(string $command, array $params = [], $skipLogs = false): mixed
	{
		$this->getSoapClient();

		$request = $this->createRequest($params);

		$this->logger->debug('Created '.$command.' request to '.$this->wsdl.'.', $request);

		try {
			$response = $this->client->$command($request);
			if (!$skipLogs) {
				$this->logger->debug('Received response', (array) $response);
			}
		} catch (\SoapFault $e) {
			$this->logger->warning($e->getMessage());
			$response = null;
		} finally {
			$soapThings = [
				'reqHeader' => $this->client->__getLastRequestHeaders(),
				'reqBody' => $this->client->__getLastRequest(),
				'resHeader' => $this->client->__getLastResponseHeaders(),
				'resBody' => $this->client->__getLastResponse(),
			];

			$this->lastRequest = $soapThings;

			$this->logger->debug('Soap transmision', $soapThings);
		}

		return $response;
	}

	private function createRequest(array $params = null): array
	{
		$request = [
			'loginAPI' => $this->loginApi,
		];

		return array_merge($request, $params);
	}

	private function getClient(): GuzzleClient
	{
		$config = [
			RequestOptions::CONNECT_TIMEOUT => $this->timeout,
			RequestOptions::READ_TIMEOUT => $this->timeout,
			RequestOptions::TIMEOUT => $this->timeout,
		];

		return new GuzzleClient($config);
	}
}
