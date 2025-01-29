<?php

namespace App\Linker\Services;

use App\Command\Services\Helper;
use App\Service\LoggerService;
use App\Model\Entity\AnalyticsProject;
use Doctrine\ORM\EntityManagerInterface;
use App\Command\Services\LinkJobsService;
use App\Linker\Managers\XtmProjectManager;
use App\Model\Entity\ExternalSystemProject;
use App\Model\Repository\AnalyticsProjectRepository;
use App\Command\Services\LinkAnalyticsProjectsService;

class LinkProjectService
{
	private LoggerService $loggerSrv;
	private XtmProjectManager $xtmProjectManager;
	private LinkAnalyticsProjectsService $analyticsProjectsService;
	private LinkJobsService $linkJobsService;
	private EntityManagerInterface $em;
	private AnalyticsProjectRepository $analyticsProjectRepository;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		LinkJobsService $linkJobsService,
		XtmProjectManager $xtmProjectManager,
		LinkAnalyticsProjectsService $analyticsProjectsService,
		AnalyticsProjectRepository $analyticsProjectRepository
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->linkJobsService = $linkJobsService;
		$this->xtmProjectManager = $xtmProjectManager;
		$this->analyticsProjectsService = $analyticsProjectsService;
		$this->analyticsProjectRepository = $analyticsProjectRepository;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_LINKERS);
	}

	/**
	 * @return null
	 */
	public function link(int $xtmProjectId)
	{
		$results = $this->xtmProjectManager->updateAnalyticsProject($xtmProjectId);
		$entities = [];
		foreach ($results as $result) {
			$status = $result['status'];
			if (Helper::CREATED == $status || Helper::NOT_CHANGED == $status) {
				/** @var AnalyticsProject $entity */
				$entity = $result['entity'];
				$entities[] = $this->analyticsProjectRepository->find($entity->getId());
			}
		}

		if (!count($entities)) {
			$this->loggerSrv->addWarning("The analytic project $xtmProjectId was not created.");
			return null;
		}

		foreach ($entities as $entity) {
			if (AnalyticsProject::CREATED === $entity->getProcessingStatus()) {
				$this->loggerSrv->addInfo("The xtm project $xtmProjectId for lang {$entity->getTargetLanguageCode()} was created.");
				$this->analyticsProjectsService->link($entity);
				if (AnalyticsProject::LINKED != $entity->getProcessingStatus()) {
					$this->loggerSrv->addWarning("The xtm project $xtmProjectId for lang {$entity->getTargetLanguageCode()} was not linked.");
					continue;
				}
				$this->em->persist($entity);
				$this->loggerSrv->addInfo("The xtm project $xtmProjectId for lang {$entity->getTargetLanguageCode()} was linked.");
			}

			if (AnalyticsProject::LINKED === $entity->getProcessingStatus()) {
				$this->linkJobsService->link($entity);
				if (AnalyticsProject::JOBS_PROCESSED != $entity->getProcessingStatus()) {
					$this->loggerSrv->addWarning("The xtm project $xtmProjectId for lang {$entity->getTargetLanguageCode()} could not be procesed.");
					continue;
				}
				$this->em->persist($entity);
				$this->loggerSrv->addInfo("The xtm project $xtmProjectId for lang {$entity->getTargetLanguageCode()} was procesed.");
				continue;
			} elseif ($entity->getProcessingStatus() >= AnalyticsProject::JOBS_PROCESSED) {
				$this->loggerSrv->addInfo("The xtm project $xtmProjectId for lang {$entity->getTargetLanguageCode()} was ready for going to next step.");
			}
		}

		$this->em->flush();
	}
}
