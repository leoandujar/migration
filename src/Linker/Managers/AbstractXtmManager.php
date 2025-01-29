<?php

namespace App\Linker\Managers;

use App\Constant\EntitySource;
use App\Service\LoggerService;
use App\Model\Entity\Parameter;
use App\Connector\Xtm\XtmConnector;
use App\Model\Utils\ParameterHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Command\Services\AlertBuilderService;
use App\Model\Repository\FetchQueueRepository;

abstract class AbstractXtmManager extends AbstractManager
{
	protected $connector;
	/**
	 * @var Parameter
	 */
	protected $lastUpdate;

	public function __construct(
		EntityManagerInterface $em,
		ManagerRegistry $managerRegistry,
		LoggerService $loggerService,
		ParameterHelper $parameterHelper,
		AlertBuilderService $alertBuilder,
		FetchQueueRepository $repository,
		XtmConnector $connector
	) {
		$this->connector = $connector;
		$this->entitySource = EntitySource::XTM;

		parent::__construct($em, $managerRegistry, $loggerService, $parameterHelper, $alertBuilder, $repository);
	}
}
