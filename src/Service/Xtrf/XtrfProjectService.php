<?php

declare(strict_types=1);

namespace App\Service\Xtrf;

use App\Connector\Xtrf\Response\Projects\UploadProjectFileResponse;
use App\Service\LoggerService;
use App\Model\Entity\WorkflowJobFile;
use App\Apis\Shared\Util\UtilsService;
use App\Connector\Xtrf\XtrfConnector;
use App\Connector\CustomerPortal\CustomerPortalConnector;

class XtrfProjectService
{
	use XtrfTraitService;

	private LoggerService $loggerSrv;
	private XtrfConnector $xtrfConnector;
	private CustomerPortalConnector $portalConnector;
	private UtilsService $utilsSrv;

	public function __construct(
		LoggerService $loggerSrv,
		XtrfConnector $xtrfConnector,
		CustomerPortalConnector $portalConnector,
		UtilsService $utilsSrv
	) {
		$this->loggerSrv = $loggerSrv;
		$this->utilsSrv = $utilsSrv;
		$this->xtrfConnector = $xtrfConnector;
		$this->portalConnector = $portalConnector;
		$this->loggerSrv->setSubcontext(self::class);
	}

	/**
	 * @throws \Exception
	 */
	public function updateTaskFiles(array $tokenList, array $taskList): void
	{
		foreach ($taskList as $task) {
			foreach ($tokenList as $token) {
				$category = $token['category'] ?? WorkflowJobFile::CATEGORY_REF;
				$token = $token['token'] ?? $token;
				$data = [
					'token' => $token,
					'category' => $category,
				];
				$response = $this->xtrfConnector->uploadTaskFile(strval($task['id']), $data);
				if (!$response->isSuccessfull()) {
					throw new \Exception("Unable to upload file for task {$task['id']}");
				}
			}
		}
	}

	public function uploadProjectFiles(string $filename, $content): ?UploadProjectFileResponse
	{
		return $this->xtrfConnector->uploadProjectFile([[
			'name' => 'file',
			'contents' => $content,
			'filename' => $filename,
		]]);
	}
}
