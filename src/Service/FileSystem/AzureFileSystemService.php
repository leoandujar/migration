<?php

namespace App\Service\FileSystem;

use App\Service\LoggerService;
use MicrosoftAzure\Storage\Blob\BlobSharedAccessSignatureHelper;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AzureFileSystemService
{
	private LoggerService $loggerSrv;

	private string $container;

	private string $account;

	private string $key;

	public function __construct(
		LoggerService $loggerService,
		ParameterBagInterface $bag
	) {
		$this->loggerSrv = $loggerService;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_BUCKET);
		$this->container = $bag->get('az.storage.workflow.container');
		$this->account = $bag->get('az.storage.account.name');
		$this->key = $bag->get('az.storage.account.key');
	}

	public function generateAzureSasToken(string $path, int $expirationMinutes = 60): string
	{
		try {
			$sas_helper = new BlobSharedAccessSignatureHelper($this->account, $this->key);

			return $sas_helper->generateBlobServiceSharedAccessSignatureToken(
				Resources::RESOURCE_TYPE_BLOB,
				"$this->container/$path",
				'w',
				(new \DateTime())->modify("+$expirationMinutes minute"),
				(new \DateTime())->modify('-5 minute'),
				'',
				'https',
			);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error getting temporary folder token $path.", $thr);

			return '';
		}
	}
}
