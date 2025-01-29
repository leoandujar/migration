<?php

namespace App\Connector\ApacheTika;

use App\Service\LoggerService;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use App\Service\FileSystem\FileSystemService;
use App\Connector\ApacheTika\Request\Request;
use App\Connector\ApacheTika\Response\Response;
use App\Connector\ApacheTika\Request\GetFileMetaRequest;
use App\Connector\ApacheTika\Request\GetFileContentRequest;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TikaConnector
{
	private string $url;
	private ?GuzzleClient $client = null;
	private LoggerService $loggerSrv;
	private FileSystemService $fileSystemSrv;

	public function __construct(
		ParameterBagInterface $bag,
		FileSystemService $fileSystemSrv,
		LoggerService $loggerSrv
	) {
		$this->url = $bag->get('app.tika.url');
		$this->loggerSrv = $loggerSrv;
		$this->fileSystemSrv = $fileSystemSrv;
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
	private function getFileMeta(string $filePath, string $mime): ?Response
	{
		$request = new GetFileMetaRequest($filePath, $mime);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getFileMeta to TIKA API', $thr);
		}

		return null;
	}

	/**
	 * @return ?Response
	 */
	public function getFileContent(string $filePath, ?string $mime): ?Response
	{
		$request = new GetFileContentRequest($filePath, $mime);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getFileContent to TIKA API', $thr);
		}

		return null;
	}

	public function getPagesCount(string $filePath): int
	{
		$mimeType = mime_content_type($filePath);
		$metadataResponse = $this->getFileMeta($filePath, $mimeType);
		if (null === $metadataResponse || !$metadataResponse->isSuccessfull()) {
			return 0;
		}
		$metadata = $metadataResponse->getRaw() ?? [];

		return $metadata['xmpTPg:NPages'] ?? 0;
	}

	/**
	 * @return array|null
	 */
	public function getFileStats(string $filename, string $fileContent)
	{
		try {
			$result = [
				'charactersWithSpaces' => 0,
				'charactersWithoutSpaces' => 0,
				'words' => 0,
				'lines' => 0,
				'pages' => 0,
				'lang' => '',
				'analyzed' => false,
			];
			$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, 'files_stats');
			$filePath = $this->fileSystemSrv->filesPath."/files_stats/$filename";
			if ($this->fileSystemSrv->createOrOverrideFile($filePath, [$fileContent])) {
				$mimeType = mime_content_type($filePath);
				$metadataResponse = $this->getFileMeta($filePath, $mimeType);
				if (null === $metadataResponse || !$metadataResponse->isSuccessfull()) {
					return $result;
				}
				$metadata = $metadataResponse->getRaw() ?? [];

				$fileContent = $metadata['X-TIKA:content'] ?? '';
				if ('application/vnd.openxmlformats-officedocument.wordprocessingml.document' !== $mimeType || !isset($metadata['Word-Count'])) {
					$contentResponse = $this->getFileContent($filePath, $mimeType);
					if (null === $contentResponse || !$contentResponse->isSuccessfull()) {
						return $result;
					}
					$fileContent = $contentResponse->getRaw()[0] ?? $fileContent;
				}

				$spacesCount = substr_count($fileContent, ' ');
				$totalWords = $metadata['Word-Count'] ?? str_word_count($fileContent, 0, '123456789.');
				$charactersWithoutSpaces = $metadata['Character Count'] ?? mb_strlen(preg_replace('/[^A-Za-z]/', '', $fileContent));
				$charactersWithSpaces = $metadata['Character-Count-With-Spaces'] ?? $charactersWithoutSpaces + $spacesCount;

				$result = [
					'charactersWithSpaces' => $charactersWithSpaces,
					'charactersWithoutSpaces' => $charactersWithoutSpaces,
					'words' => $totalWords,
					'lines' => $metadata['Line-Count'] ?? $this->getLinesCount($fileContent),
					'lang' => $metadata['language'] ?? '',
					'pages' => $metadata['xmpTPg:NPages'] ?? 0,
					'analyzed' => ($charactersWithSpaces + $charactersWithoutSpaces + $totalWords) > 0,
				];
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request to TIKA API', $thr);
		}

		return $result;
	}

	public function getFileStatsV2(string $filePath)
	{
		try {
			$result = [
				'charactersWithSpaces' => 0,
				'charactersWithoutSpaces' => 0,
				'words' => 0,
				'lines' => 0,
				'pages' => 0,
				'lang' => '',
				'analyzed' => false,
			];
			$mimeType = mime_content_type($filePath);
			$metadataResponse = $this->getFileMeta($filePath, $mimeType);
			if (null === $metadataResponse || !$metadataResponse->isSuccessfull()) {
				return $result;
			}
			$metadata = $metadataResponse->getRaw() ?? [];

			$fileContent = $metadata['X-TIKA:content'] ?? '';
			if ('application/vnd.openxmlformats-officedocument.wordprocessingml.document' !== $mimeType || !isset($metadata['Word-Count'])) {
				$contentResponse = $this->getFileContent($filePath, $mimeType);
				if (null === $contentResponse || !$contentResponse->isSuccessfull()) {
					return $result;
				}
				$fileContent = $contentResponse->getRaw()[0] ?? $fileContent;
			}

			$spacesCount = substr_count($fileContent, ' ');
			$totalWords = $metadata['Word-Count'] ?? str_word_count($fileContent, 0, '123456789.');
			$charactersWithoutSpaces = $metadata['Character Count'] ?? mb_strlen(preg_replace('/[^A-Za-z]/', '', $fileContent));
			$charactersWithSpaces = $metadata['Character-Count-With-Spaces'] ?? $charactersWithoutSpaces + $spacesCount;

			$result = [
				'charactersWithSpaces' => $charactersWithSpaces,
				'charactersWithoutSpaces' => $charactersWithoutSpaces,
				'words' => $totalWords,
				'lines' => $metadata['Line-Count'] ?? $this->getLinesCount($fileContent),
				'lang' => $metadata['language'] ?? '',
				'pages' => $metadata['xmpTPg:NPages'] ?? 0,
				'analyzed' => ($charactersWithSpaces + $charactersWithoutSpaces + $totalWords) > 0,
			];
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request to TIKA API', $thr);
		}

		return $result;
	}

	/**
	 * @return int
	 */
	private function getLinesCount(string $fileContent)
	{
		$filename = uniqid().'.txt';
		$fileContent = str_replace("\n\n", '', $fileContent);
		$filePath = $this->fileSystemSrv->filesPath."/files_stats/$filename";
		if ($this->fileSystemSrv->createOrOverrideFile($filePath, [$fileContent])) {
			$file = new \SplFileObject($filePath, 'r');
			$file->seek(PHP_INT_MAX);

			return $file->key() + 1;
		}

		return 0;
	}
}
