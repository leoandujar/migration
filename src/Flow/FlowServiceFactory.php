<?php

namespace App\Flow;

use App\Connector\ApacheTika\TikaConnector;
use App\Connector\AzureCognitive\AzureVisionConnector;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use App\Connector\Qbo\QboConnector;
use App\Connector\Xtrf\XtrfConnector;
use App\Connector\XtrfMacro\MacroConnector;
use App\Flow\Services\XmlParserService;
use App\Flow\Utils\FlowUtils;
use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\BlCall;
use App\Model\Entity\ContactPerson;
use App\Model\Entity\CustomerInvoice;
use App\Model\Entity\Project;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use App\Service\Notification\NotificationService;
use App\Service\UtilService;
use App\Service\Xtrf\XtrfProjectService;
use App\Service\Xtrf\XtrfQuoteService;
use App\Workflow\HelperServices\EmailParsingService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

class FlowServiceFactory
{
	private array $dependencies = [];
    private EntityManagerInterface $em;
    private WorkflowMonitorRepository $wfMonitorRepo;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		MonitorLogService $monitorLogSrv,
		QboConnector $qboCon,
		CloudFileSystemService $fileBucketService,
		FlowUtils $flowUtils,
		MacroConnector $macroConn,
		XtrfProjectService $xtrfProjectSrv,
		XtrfQuoteService $xtrfQuoteSrv,
		XtrfConnector $xtrfConnector,
		CustomerPortalConnector $customerPortalConnector,
		FileSystemService $fileSystemSrv,
		EmailParsingService $emailParsingSrv,
		ParameterBagInterface $parameterBag,
		NotificationService $notificationService,
		AzureVisionConnector $azureVisionConn,
		TikaConnector $tikaConn,
		Environment $env,
        XmlParserService $xmlParserSrv,
        CustomerPortalConnector $portalConn,
        UtilService $utilsSrv
	) {
        $this->em = $em;
		$this->dependencies = [
			'em' => $em,
			'wfMonitorRepo' => $em->getRepository(AVWorkflowMonitor::class),
			'loggerSrv' => $loggerSrv,
			'monitorLogSrv' => $monitorLogSrv,
			'qboCon' => $qboCon,
			'fileBucketService' => $fileBucketService,
			'flowUtils' => $flowUtils,
			'blCallRepository' => $em->getRepository(BlCall::class),
			'customerInvoiceRepo' => $em->getRepository(CustomerInvoice::class),
			'projectRepo' => $em->getRepository(Project::class),
			'ciRepo' => $em->getRepository(CustomerInvoice::class),
			'macroConn' => $macroConn,
			'contactPersonRepo' => $em->getRepository(ContactPerson::class),
			'xtrfProjectSrv' => $xtrfProjectSrv,
			'xtrfQuoteSrv' => $xtrfQuoteSrv,
			'xtrfConnector' => $xtrfConnector,
			'customerPortalConnector' => $customerPortalConnector,
			'fileSystemSrv' => $fileSystemSrv,
			'emailParsingSrv' => $emailParsingSrv,
			'parameterBag' => $parameterBag,
			'notificationService' => $notificationService,
			'azureVisionConn' => $azureVisionConn,
			'tikaConn' => $tikaConn,
			'env' => $env,
            'xmlParserSrv' => $xmlParserSrv,
            'portalConn' => $portalConn,
            'utilsSrv' => $utilsSrv,
		];
	}

	public function getAction(string $actionClass): mixed
	{
		try {
			$reflection = new \ReflectionClass($actionClass);
			$constructor = $reflection->getConstructor();

			if (is_null($constructor)) {
				return new $actionClass();
			}

			$parameters = $constructor->getParameters();

			$finalDependencies = [];
			foreach ($parameters as $parameter) {
				$finalDependencies[] = $this->dependencies[$parameter->getName()];
			}

			return $reflection->newInstanceArgs($finalDependencies);
		} catch (\Throwable $thr) {
			return null;
		}
	}
}
