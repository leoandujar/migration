<?php

namespace App\Workflow\Subscribers\XtmGithub;

use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use App\Connector\Xtm\XtmConnector;
use App\Connector\Github\GithubService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use App\Service\FileSystem\FileSystemService;
use Symfony\Component\Workflow\Event\Event;
use App\Connector\Github\Request\FileRequest;
use App\Service\Notification\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PrepareSubscriber implements EventSubscriberInterface
{
	private XtmConnector $xtmConnector;
	private LoggerService $loggerSrv;
	private FileSystemService $fileSystemSrv;
	private NotificationService $notificationService;
	private Registry $registry;
	private GithubService $githubService;
	private EntityManagerInterface $em;

	/**
	 * PrepareSubscriber constructor.
	 */
	public function __construct(
		XtmConnector $xtmConnector,
		LoggerService $loggerSrv,
		FileSystemService $fileSystemService,
		NotificationService $notificationService,
		Registry $registry,
		GithubService $githubService,
		EntityManagerInterface $em
	) {
		$this->xtmConnector = $xtmConnector;
		$this->loggerSrv = $loggerSrv;
		$this->fileSystemSrv = $fileSystemService;
		$this->notificationService = $notificationService;
		$this->registry = $registry;
		$this->githubService = $githubService;
		$this->em = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_GITHUB);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.github.completed.initialized' => 'prepare',
		];
	}

	public function prepare(Event $event)
	{
		try {
			/**
			 * @var WFHistory $history
			 */
			$history = $event->getSubject();
			$wf = $this->registry->get($history, 'github');
			$context = $history->getContext();
			$languages = $context['languages'];
			$projectID = $context['project_id'];
			list($files, $failed) = $this->getFiles($languages, $projectID, $context);
			$context['failed'] = $failed;
			$latestCommit = $this->githubService->getLatestCommit($context['owner'], $context['repository'], $context['token']);
			$context['latestCommit'] = $latestCommit;
			if ($wf->can($history, 'prepare')) {
				if (0 === count($files)) {
					$this->loggerSrv->addWarning('no file to be processed');

					return;
				}
				$context['files'] = $files;
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'prepare');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}

	private function getFiles($languages, $projectID, &$context): array
	{
		$files = [];
		$zipPath = $this->fileSystemSrv->filesPath;
		$this->fileSystemSrv->createDirectory($zipPath, $projectID);
		$failed = [];
		foreach ($languages as $key => $value) {
			$response = $this->xtmConnector->downloadFilesByProjectId($projectID, $key);
			if (null === $response || !$response->isSuccessfull()) {
				$msg = "Unable to get file from XTM for language $key and project $projectID";
				$this->loggerSrv->addError($msg);
				$data = [
					'message' => $msg,
					'status' => 'failure',
					'date' => (new \DateTime())->format('Y-m-d'),
					'title' => 'CA Covid XTM Project upload failed',
				];
				$this->notificationService->addNotification(NotificationService::NOTIFICATION_TYPE_TEAM, $context['notification_target'], $data, 'XTM Project');
				$failed[] = $msg;
				continue;
			}
			$responseFileData = $response->getRaw();
			$zipCreated = $this->fileSystemSrv->createOrOverrideFile("$zipPath/$projectID/$key.zip", $responseFileData);
			if (!$zipCreated) {
				$this->loggerSrv->addError("Unable to create ZIP file for language $key in project $projectID. Data was present but zip was not created.");
				continue;
			}
			$unZipSuccess = $this->fileSystemSrv->unzipFile("$zipPath/$projectID/$key.zip", "$zipPath/$projectID/");
			if (!$unZipSuccess) {
				$this->loggerSrv->addError("Unable to UNZIP file for language $key in project $projectID. ZIP was created but unzip failed.");
				continue;
			}
			foreach ($response->getFilesData() as $fileData) {
				$filename = pathinfo("$zipPath/$projectID/{$fileData['targetLanguage']}/{$fileData['fileName']}", PATHINFO_FILENAME);
				$fileExtension = pathinfo("$zipPath/$projectID/{$fileData['targetLanguage']}/{$fileData['fileName']}", PATHINFO_EXTENSION);
				if (!isset($context['pr_title'])) {
					$context['pr_title'] = $filename;
					$context['tree_title'] = sprintf('avantpage_translation_%s_%s', $filename, $projectID);
				}
				$file = new FileRequest();
				$file->path = sprintf('%s/%s-%s.%s', $context['path'], $filename, $value, $fileExtension);
				$file->type = 'blob';
				$content = file_get_contents("$zipPath/$projectID/{$fileData['targetLanguage']}/{$fileData['fileName']}");
				/* Workaround for XTM bug: ticket #337391
					TODO: REMOVE when XTM is updated
				*/
				$patterns = [];
				$patterns[0] = '/&#xa;/';
				$patterns[1] = '/<!--/';
				$patterns[2] = '/-->/';
				$patterns[3] = '/[\x{2066}\x{2069}]/u';
				$replacements = [];
				$replacements[0] = "\n";
				$replacements[1] = "\n$0";
				$replacements[2] = "$0\n";
				$replacements[3] = '';
				$file->content = preg_replace($patterns, $replacements, $content);
				$file->mode = '100644';
				if (strlen($file->content) > 0) {
					$files[] = $file;
				} else {
					$failed[] = $filename;
				}
			}
		}

		return [$files, $failed];
	}
}
