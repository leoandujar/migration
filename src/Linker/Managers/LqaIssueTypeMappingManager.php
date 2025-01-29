<?php

namespace App\Linker\Managers;

use App\Command\Services\Helper;
use App\Service\LoggerService;
use App\Connector\Xtm\XtmConnector;
use App\Model\Utils\ParameterHelper;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Entity\LqaIssueTypeMapping;
use Doctrine\Persistence\ManagerRegistry;
use App\Command\Services\AlertBuilderService;
use App\Model\Repository\FetchQueueRepository;
use App\Model\Repository\LqaIssueTypeRepository;
use App\Model\Repository\LqaIssueTypeMappingRepository;

class LqaIssueTypeMappingManager extends AbstractXtmManager
{
	/**
	 * @var LqaIssueTypeMappingRepository
	 */
	private $issueTypeMappingRepository;
	/**
	 * @var LqaIssueTypeRepository
	 */
	private $issueTypeRepository;

	public function __construct(
		EntityManagerInterface $em,
		ManagerRegistry $managerRegistry,
		LoggerService $loggerService,
		ParameterHelper $parameterHelper,
		AlertBuilderService $alertBuilder,
		FetchQueueRepository $repository,
		XtmConnector $connector,
		LqaIssueTypeMappingRepository $issueTypeMappingRepository,
		LqaIssueTypeRepository $issueTypeRepository
	) {
		parent::__construct($em, $managerRegistry, $loggerService, $parameterHelper, $alertBuilder, $repository, $connector);
		$this->issueTypeMappingRepository = $issueTypeMappingRepository;
		$this->issueTypeRepository = $issueTypeRepository;
	}

	/**
	 * @var array
	 */
	private $entities = [];

	public function updateEntry(array $element): array
	{
		$entityManager = $this->getDoctrine()->getManager();
		$processedOutput = null;

		if (!empty($element['ParentID'])) {
			if (isset($this->entities[$element['ParentID']])
				&& $this->entities[$element['ParentID']] instanceof LqaIssueTypeMapping) {
				$parent = $this->entities[$element['ParentID']];
			} else {
				throw new \UnexpectedValueException('Parent of processed entry was not processed yet');
			}
		} elseif (isset($element['Parent']) && $element['Parent'] instanceof LqaIssueTypeMapping) {
			$parent = $element['Parent'];
		} else {
			$parent = null;
		}

		$processedIssueTypeMapping = $this->issueTypeMappingRepository->findOneByNameAndParent($element['IssueType'], $parent);
		if (null === $processedIssueTypeMapping) {
			$processedIssueTypeMapping = new LqaIssueTypeMapping();
			$processedIssueTypeMapping->setName($element['IssueType']);
			$created = true;
		} else {
			$created = false;
		}

		$normalizedName = $this->normalizeName($element['IssueType']);

		$issueType = $this->issueTypeRepository->findOneByName($normalizedName);

		$active = boolval($element['active']);

		if (null === $parent) {
			$path = null;
			$pathDepth = 0;
		} else {
			if (null === $parent->getPath()) {
				$path = $parent->getId();
			} else {
				$path = $parent->getPath().'|'.$parent->getId();
			}
			$pathDepth = $parent->getPathDepth() + 1;
		}

		$processedIssueTypeMapping
			->setWeight(1)
			->setParent($parent)
			->setActive($active)
			->setLqaIssueType($issueType)
			->setPath($path)
			->setPathDepth($pathDepth);
		$entityManager->persist($processedIssueTypeMapping);
		$entityManager->flush();
		$entityManager->refresh($processedIssueTypeMapping);

		if (!empty($element['ID'])) {
			$this->entities[$element['ID']] = $processedIssueTypeMapping;
		}

		return [
			'status' => ($created ? Helper::CREATED : Helper::UPDATED),
			'entity' => $processedIssueTypeMapping,
		];
	}

	public function normalizeName($name): string
	{
		return trim(strtr(preg_replace('/\.$/', '', $name), [
			'/ ' => '/',
			' /' => '/',
			' / ' => '/',
		]));
	}
}
