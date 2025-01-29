<?php

namespace App\Service\FileSystem;

use App\Service\LoggerService;
use League\Flysystem\FilesystemOperator;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use League\Flysystem\DirectoryListing;

class CloudFileSystemService implements BucketInterface
{
	private FilesystemOperator $defaultStorage;
	private FilesystemOperator $currentStorage;
	private FilesystemOperator $cpFilesStorage;
	private FilesystemOperator $apFilesStorage;
	private FilesystemOperator $awsProjectsStorage;
	private FilesystemOperator $azArchiveStorage;
	private FilesystemOperator $azWorkflowStorage;
	private FilesystemOperator $awsInvoicesStorage;
	private FilesystemOperator $azFtpStorage;
	private LoggerService $loggerSrv;

	public const LOCAL = 0;
	public const BUCKET_AP = 1;
	public const BUCKET_CP = 2;
	public const BUCKET_PROJECTS = 3;
	public const BUCKET_ARCHIVE = 4;
	public const BUCKET_WORKFLOW = 5;
	public const BUCKET_INVOICES = 6;
	public const BUCKET_FTP = 7;

	public function __construct(
		LoggerService $loggerService,
		FilesystemOperator $defaultStorage,
		FilesystemOperator $apFilesStorage,
		FilesystemOperator $cpFilesStorage,
		FilesystemOperator $awsProjectsStorage,
		FilesystemOperator $awsInvoicesStorage,
		FilesystemOperator $azArchiveStorage,
		FilesystemOperator $azWorkflowStorage,
		FilesystemOperator $azFtpStorage,
	) {
		$this->loggerSrv = $loggerService;
		$this->loggerSrv->setSubcontext(self::class);
		$this->defaultStorage = $defaultStorage;
		$this->currentStorage = $cpFilesStorage;
		$this->cpFilesStorage = $cpFilesStorage;
		$this->apFilesStorage = $apFilesStorage;
		$this->awsProjectsStorage = $awsProjectsStorage;
		$this->awsInvoicesStorage = $awsInvoicesStorage;
		$this->azArchiveStorage = $azArchiveStorage;
		$this->azWorkflowStorage = $azWorkflowStorage;
		$this->azFtpStorage = $azFtpStorage;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_BUCKET);
	}

	public function exists(string $path): bool
	{
		try {
			return $this->currentStorage->fileExists($path);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error checking for file existence for file $path", $thr);

			return false;
		}
	}

	public function upload(string $fileName, mixed $filePath): bool
	{
		try {
			$this->currentStorage->write($fileName, file_get_contents($filePath));

			return true;
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error uploading file from $filePath/$fileName.", $thr);

			return false;
		}
	}

	public function write(string $fileName, $content): bool
	{
		try {
			$this->currentStorage->write($fileName, $content);

			return true;
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error writing file $fileName.", $thr);

			return false;
		}
	}

	public function uploadStream(string $fileName, $filePath): bool
	{
		try {
			$stream = fopen($filePath, 'r+');
			$this->currentStorage->writeStream($fileName, $stream);
			if (is_resource($stream)) {
				fclose($stream);
			}

			return true;
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error uploading stream file from $filePath/$fileName.", $thr);

			return false;
		}
	}

	public function uploadImage(UploadedFile $file, string $imageName, ?int $size = null, $folder = null): ?string
	{
		if ($file->isValid()) {
			try {
				$manager = ImageManager::gd();
				$img = $manager->read($file);
				if ($size) {
					$img->resize($size, $size);
				}
				$path = $folder ? "$folder/$imageName" : $imageName;
				$this->defaultStorage->write($path, $img->encode());
				$upload = $this->uploadStream($path, $path);
				if ($upload) {
					return $path;
				}
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError("Error uploading image $path.", $thr);

				return null;
			}
		}

		return null;
	}

	public function download(string $path): bool|string|null
	{
		$response = null;
		if (empty($path)) {
			return null;
		}

		try {
			$response = $this->currentStorage->read($path);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error downloading file from $path.", $thr);
		}

		return $response;
	}

	public function mimeType(string $path): bool|string|null
	{
		$response = null;
		if (empty($path)) {
			return null;
		}

		try {
			$response = $this->currentStorage->mimeType($path);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error reading mimeType from $path.", $thr);
		}

		return $response;
	}

	public function listContents(string $folderName): ?DirectoryListing
	{
		$response = null;
		if (empty($folderName)) {
			return null;
		}
		try {
			$response = $this->currentStorage->listContents($folderName);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error listing content for folder $folderName.", $thr);
		}

		return $response;
	}

	public function deleteFile($path): bool
	{
		try {
			$this->currentStorage->delete($path);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error deleting file $path.", $thr);

			return false;
		}

		return true;
	}

	public function createDir($path): bool
	{
		try {
			$this->currentStorage->createDirectory($path);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error creating folder $path.", $thr);

			return false;
		}

		return true;
	}

	public function getImageBase64(?string $picName): ?string
	{
		if (empty($picName)) {
			return null;
		}
		$splittedName = explode('__', $picName);

		if (!isset($splittedName[1])) {
			return null;
		}

		$mimeType = $splittedName[1];
		try {
			$stream = $this->currentStorage->readStream($picName);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error getting base64 from image pic $picName.", $thr);

			return null;
		}
		if (!$stream) {
			return null;
		}

		$contents = stream_get_contents($stream);
		if (is_resource($stream)) {
			fclose($stream);
		}

		return 'data:image/'.$mimeType.';base64,'.base64_encode($contents);
	}

	public function getTemporaryUrl(string $path, int $expirationDays = 7): string
	{
		try {
			$fileExists = $this->currentStorage->fileExists($path);
			if (!$fileExists) {
				return '';
			}
			$now = new \DateTime('now');
			$expiration = $now->modify("+$expirationDays day");

			return $this->currentStorage->temporaryUrl($path, $expiration);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error getting temporary url for file $path.", $thr);

			return '';
		}
	}

	public function changeStorage(int $bucket): void
	{
		$newBucket = match ($bucket) {
			self::LOCAL => $this->defaultStorage,
			self::BUCKET_AP => $this->apFilesStorage,
			self::BUCKET_CP => $this->cpFilesStorage,
			self::BUCKET_ARCHIVE => $this->azArchiveStorage,
			self::BUCKET_WORKFLOW => $this->azWorkflowStorage,
			self::BUCKET_PROJECTS => $this->awsProjectsStorage,
			self::BUCKET_INVOICES => $this->awsInvoicesStorage,
			self::BUCKET_FTP => $this->azFtpStorage,
			default => false,
		};
		$this->currentStorage = $newBucket;
	}

	public function checkStorage(int $bucket): bool
	{
		return match ($bucket) {
			self::LOCAL,
			self::BUCKET_AP,
			self::BUCKET_WORKFLOW,
			self::BUCKET_PROJECTS,
			self::BUCKET_INVOICES,
			self::BUCKET_ARCHIVE,
			self::BUCKET_FTP,
			self::BUCKET_CP => true,
			default => false,
		};
	}
}
