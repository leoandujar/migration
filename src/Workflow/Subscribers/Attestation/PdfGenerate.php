<?php

namespace App\Workflow\Subscribers\Attestation;

use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\FileSystem\FileSystemService;
use App\Service\Notification\NotificationService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManager;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class PdfGenerate implements EventSubscriberInterface
{
	private Environment $env;
	private Registry $registry;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private FileSystemService $fileSystemSrv;
	private CloudFileSystemService $fileBucketService;
	private NotificationService $notificationSrv;
	private MonitorLogService $monitorLogSrv;
	private WorkflowMonitorRepository $wfMonitorRepo;

	public function __construct(
		Environment $env,
		Registry $registry,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		NotificationService $notificationSrv,
		FileSystemService $fileSystemSrv,
		CloudFileSystemService $fileBucketService,
		MonitorLogService $monitorLogSrv,
		WorkflowMonitorRepository $wfMonitorRepo,
	) {
		$this->em = $em;
		$this->env = $env;
		$this->registry = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->fileBucketService = $fileBucketService;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_ATTESTATION);
		$this->notificationSrv = $notificationSrv;
		$this->monitorLogSrv = $monitorLogSrv;
		$this->wfMonitorRepo = $wfMonitorRepo;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.attestation.completed.get_content' => 'pdfGenerate',
		];
	}

	/**
	 * @throws \Throwable
	 */
	public function pdfGenerate(Event $event)
	{
		try {
			$this->loggerSrv->addInfo('Starting Generate PDF step for Attestation filtered projects.');
			/** @var WFHistory $history */
			$history = $event->getSubject();
			$context = $history->getContext();
			/** @var AVWorkflowMonitor $monitorObj */
			$monitorObj = $this->wfMonitorRepo->find($context['monitor_id']);
			if ($monitorObj) {
				$this->monitorLogSrv->setMonitor($monitorObj);
			}
			$documentsResult = $context['documentsResult'];
			$template = $context['template'];
			$params = $context['params'];
			$startDate = $context['startDate'];
			unset($context['documentsResult']);

			$zipper = new \ZipArchive();
			$zipName = 'attestations_'.(new \DateTime())->format('Y_m_d_H_i_s').'.zip';
			$zipPath = $this->fileSystemSrv->filesPath.DIRECTORY_SEPARATOR.$zipName;
			$zipper->open($zipPath, \ZipArchive::CREATE);
			$countFailed = 0;
			$folderName = uniqid('attestation_output_');
			$this->fileSystemSrv->createTempDir($folderName);
			$folderName = $this->fileSystemSrv->filesPath.DIRECTORY_SEPARATOR.$folderName;
			foreach ($documentsResult as $filename => $info) {
				$document = $info['file'];
				$language = $info['language'];
				$project = $info['project'];
				$mpdf = new Mpdf();
				$mpdf->setAutoBottomMargin = 'stretch';
				$mpdf->setAutoTopMargin = 'stretch';
				$this->fileSystemSrv->createDirectory($folderName, $project);

				$tpl = $this->env->createTemplate(file_get_contents('templates/Emails/attestation_tpl.html.twig'));
				$content = $tpl->render([
					'contact' => [
						'name' => $template['name'],
						'address' => $template['address'],
						'email' => $template['email'],
					],
					'docData' => [
						'member' => $document['member'] ?? '',
						'type' => $document['type'] ?? '',
						'language' => $language ?? '',
						'date' => $startDate ?? '',
					],
				]);
				$mpdf->WriteHTML($content);
				$this->loggerSrv->addInfo("PDF generated for $filename");
				try {
					$mpdf->Output("$folderName/$project/attestation_$filename.pdf", Destination::FILE);
					$zipper->addFile("$folderName/$project/attestation_$filename.pdf", "$project/attestation_$filename.pdf");
				} catch (\Throwable) {
					$this->loggerSrv->addWarning("Error generating PDF for $filename");
					++$countFailed;
					continue;
				}
			}

			$zipper->close();
			$this->loggerSrv->addInfo('Zip file completed');

			$this->notificationSrv->addNotification(
				NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL,
				$params['to'],
				[
					'subject' => 'Attestation files',
					'template' => 'attestation',
					'attachments' => [$zipPath],
					'data' => [
						'totalProjects' => $context['totalProjects'] ?? 0,
						'totalDocs' => count($documentsResult) ?? 0,
						'projectIds' => $context['projectIds'],
						'filesError' => $context['filesError'],
						'filesOcr' => $context['filesOcr'],
					],
				]
			);
			$this->fileBucketService->changeStorage(CloudFileSystemService::BUCKET_WORKFLOW);
			$this->fileBucketService->upload($zipName, $zipPath);
			$temporaryUrl = $this->fileBucketService->getTemporaryUrl($zipName);
			$this->monitorLogSrv->appendSuccess([
				'type' => 'download_url',
				'url' => $temporaryUrl,
			]);
			$this->loggerSrv->addInfo('Cleaning up working folders for Attestation workflow.');
			try {
				$this->fileSystemSrv->deleteDirectory($folderName);
			} catch (\Throwable $thr) {
				$this->loggerSrv->addWarning('Error cleaning up working folders for Attestation workflow.', $thr);
			}

			$this->loggerSrv->addInfo('Finished Attestation workflow.');

			$wf = $this->registry->get($history, 'attestation');
			if ($wf->can($history, 'generate')) {
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'generate');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error generating the PDFs for Attestation workflow.', $thr);
			throw $thr;
		}
	}
}
