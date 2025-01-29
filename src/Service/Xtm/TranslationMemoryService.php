<?php

namespace App\Service\Xtm;

use App\Service\LoggerService;
use App\Service\FileSystem\FileSystemService;
use Symfony\Component\HttpKernel\KernelInterface;

class TranslationMemoryService
{
	private KernelInterface $kernel;
	private LoggerService $loggerSrv;

	public function __construct(
		KernelInterface $kernel,
		LoggerService $loggerSrv
	) {
		$this->kernel    = $kernel;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
	}

	/**
	 * @param null $outputPath
	 */
	public function processZipFile($fileDir, $externalID, $outputPath = null): ?string
	{
		$extractPath = sprintf('%s/var/generate-trans/', $this->kernel->getProjectDir());
		try {
			$zip = new \ZipArchive();
			$res = $zip->open($fileDir, \ZipArchive::CHECKCONS);
			if (false === $res) {
				switch ($res) {
					case \ZipArchive::ER_NOZIP:
						$this->loggerSrv->addError(sprintf('the file %s is not a zip file', $fileDir));

						return null;
					case \ZipArchive::ER_INCONS:
						$this->loggerSrv->addError(sprintf('the file %s consistency check failed', $fileDir));

						return null;
					case \ZipArchive::ER_CRC:
						$this->loggerSrv->addError(sprintf('the file %s checksum failed', $fileDir));

						return null;
					case \ZipArchive::ER_NOENT:
						$this->loggerSrv->addError(sprintf("the zip file dir %s doesn't exist", $fileDir));

						return null;
				}
			}
			$zip->extractTo($extractPath);
			$zip->close();
			if (null === $outputPath) {
				$outputPath = sprintf('%s/var/generate-trans-output-%s/', $this->kernel->getProjectDir(), md5(random_bytes(5)));
			}
			if (!is_dir($outputPath)) {
				mkdir($outputPath, 0766);
			}
			if ($handler = opendir($extractPath)) {
				while (false !== ($entry = readdir($handler))) {
					if ('.' !== $entry && '..' !== $entry) {
						$name     = basename($entry);
						$filePath = sprintf('%s%s', $extractPath, $name);
						if (preg_match('/[a-z]{2,3}_[A-Z]{2,3}-[a-z]{2,3}(_[A-Z]{2,3}){0,1}/', $name) && 'no_tm_found.txt' !== $name) {
							$folder = explode('.', explode('-', $name)[1])[0];
							if (!is_dir(sprintf('%s/%s', $outputPath, $folder))) {
								mkdir(sprintf('%s/%s', $outputPath, $folder));
							}
							copy($filePath, sprintf('%s/%s/%s_%s', $outputPath, $folder, $externalID, $name));
							unlink($filePath);
						}
					}
				}
				closedir($handler);
			}
			unlink($fileDir);

			return $outputPath;
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError(sprintf('There was an error processing the file %s', $fileDir), $thr);
		}

		return null;
	}

	public function creteZip($dir): ?string
	{
		$outputDir = sprintf('%s/var', $this->kernel->getProjectDir());
		if (!is_dir($outputDir)) {
			mkdir($outputDir);
		}
		$fileName = sprintf('%s/%s.zip', $outputDir, basename($dir));
		$zipper   = new \ZipArchive();
		$zipper->open($fileName, \ZipArchive::CREATE);
		if ($handler = opendir($dir)) {
			while (false !== ($entry = readdir($handler))) {
				if ('.' !== $entry && '..' !== $entry) {
					$file = implode('/', [$dir, $entry]);
					if (!file_exists($file)) {
						$this->loggerSrv->addError(sprintf('file %s was not found', $entry));
						continue;
					}
					$zipper->addFile($file, basename($entry));
				}
			}
			closedir($handler);
		}
		if (!$zipper->close()) {
			$this->loggerSrv->addError('unable to create the zip file.');
		}
		FileSystemService::deleteDir($dir);

		return $fileName;
	}
}
