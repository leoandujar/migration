<?php

namespace App\Service\FileSystem;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\LoggerService;
use Psr\Log\InvalidArgumentException;

class FileSystemService
{
	private LoggerService $loggerSrv;
	public string $filesPath;

	public function __construct(
		LoggerService $loggerService,
		ParameterBagInterface $parameterBag
	) {
		$this->loggerSrv = $loggerService;
		$this->filesPath = $parameterBag->get('app.files.local.path');
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	public function deleteFile(?string $filePath = null): bool
	{
		$response = false;
		if ($filePath && file_exists($filePath)) {
			try {
				$response = unlink($filePath);
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError("Error deleting file $filePath.", $thr);

				return false;
			}
		}

		return $response;
	}

	public function createDirectory(string $path, string $dirName): bool
	{
		if (!file_exists("$path/$dirName")) {
			return mkdir("$path/$dirName", 0777, true);
		}

		return true;
	}

	public function createOrOverrideFile(string $filePath, $fileData): bool
	{
		try {
			$fp = fopen($filePath, 'w');
			if (is_array($fileData)) {
				$content = array_shift($fileData);
			} else {
				$content = $fileData;
			}
			fwrite($fp, $content);
			fclose($fp);
			chmod($filePath, 0777);

			return true;
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error creating file $filePath.", $thr);

			return false;
		}
	}

	public function getBase64ImagePngFromResource($resource): ?string
	{
		try {
			$data = stream_get_contents($resource);
			if ($data) {
				return 'data:image/png;base64,'.base64_encode($data);
			}

			return null;
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting image base64 from png.', $thr);

			return null;
		}
	}

	public function createTempDir(string $dirName): bool
	{
		if (!file_exists($this->filesPath.DIRECTORY_SEPARATOR.$dirName)) {
			return mkdir($this->filesPath.DIRECTORY_SEPARATOR.$dirName);
		}

		return false;
	}

	public function renameFile(string $filePath, string $oldName, string $newName): bool
	{
		if (!file_exists("$filePath/$oldName")) {
			return false;
		}

		return rename("$filePath/$oldName", "$filePath/$newName");
	}

	public function deleteDirectory(string $target): void
	{
		if (is_dir($target)) {
			$files = glob($target.'*', GLOB_MARK);

			foreach ($files as $file) {
				$this->deleteDirectory($file);
			}
			rmdir($target);
		} elseif (is_file($target)) {
			unlink($target);
		}
	}

	public function unzipFile(string $filePath, $extractPath): bool
	{
		try {
			$zip = new \ZipArchive();
			$res = $zip->open($filePath);
			if (true === $res) {
				$zip->extractTo($extractPath);
				$zip->close();

				return true;
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error trying to unzip file $filePath.", $thr);

			return false;
		}

		$this->loggerSrv->addWarning("Unable to unzip file $filePath.");

		return false;
	}

	public function getBinaryFromFile(string $filename): bool|string|null
	{
		try {
			$handle = fopen($filename, 'rb');
			$contents = fread($handle, filesize($filename));
			fclose($handle);

			return $contents;
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error trying to get binary file $filename.", $thr);
		}

		return null;
	}

	public static function deleteDir(string $dirPath): void
	{
		if (!is_dir($dirPath)) {
			throw new InvalidArgumentException("$dirPath must be a directory");
		}
		if ('/' != substr($dirPath, strlen($dirPath) - 1, 1)) {
			$dirPath .= '/';
		}
		$files = glob($dirPath.'*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				self::deleteDir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dirPath);
	}

    public function findFileByName(string $directory, string $partialName): ?string
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException("$directory must be a valid directory");
        }

        $files = scandir($directory);
        foreach ($files as $file) {
            if (str_contains($file, $partialName)) {
                return $file;
            }
        }

        return null;
    }

	public function getContentDir(string $folderPath): array
	{
		return array_diff(scandir($folderPath), ['.', '..']);
	}
}
