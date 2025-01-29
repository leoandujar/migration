<?php

namespace App\Command\Services;

use App\Service\LoggerService;
use App\Model\Entity\LqaIssueType;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Repository\LqaIssueTypeRepository;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class InitializeLqaIssueTypeService
{
	private ParameterBagInterface $bag;
	private string $filePath = '/src/Command/Data/lqa-issue-types.json';
	private LqaIssueTypeRepository $lqaIssueTypeRepository;
	private EntityManagerInterface $em;
	private LoggerService $loggerSrv;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerService,
		LqaIssueTypeRepository $lqaIssueTypeRepository,
		ParameterBagInterface $bag
	) {
		$this->bag = $bag;
		$this->em = $em;
		$this->lqaIssueTypeRepository = $lqaIssueTypeRepository;
		$this->loggerSrv = $loggerService;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function execute(OutputInterface $output): void
	{
		$sourceJson = file_get_contents("{$this->bag->get('kernel.project_dir')}$this->filePath");
		$sourceData = json_decode($sourceJson, true);
		if (is_array($sourceData)) {
			$this->updateEntries($output, $sourceData);
		}
	}

	/**
	 * @param int $level
	 */
	public function updateEntries(OutputInterface $output, array $elements, LqaIssueType $parent = null, $level = 0): mixed
	{
		foreach ($elements as $element) {
			$entity = $this->lqaIssueTypeRepository->findOneByNameAndParent($element['name'], $parent);
			$executedOperation = Helper::UPDATED;
			if (null === $entity) {
				$entity = new LqaIssueType();
				$entity->setName($element['name']);
				$executedOperation = Helper::CREATED;
			}

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

			$remoteSource = [
				'weight' => 1,
				'isLeaf' => !isset($element['childs']),
				'parent' => $parent,
				'active' => true,
				'path' => $path,
				'pathDepth' => $pathDepth,
			];

			$entityHash = $entity->hashFromObject();
			$remoteHash = $entity->hashFromRemote($remoteSource);

			if ($entityHash === $remoteHash) {
				$executedOperation = Helper::NOT_CHANGED;
			} else {
				$entity->populateFromRemote($remoteSource);
			}
			try {
				$this->em->persist($entity);
			} catch (\Throwable $thr) {
				$message = "Error while flushing LqaIssueType=>{$entity->getName()}";
				$this->loggerSrv->addError($message, $thr);
				$output->writeln("<entval>$message</entval>");
				continue;
			}

			$outputText = match ($executedOperation) {
				Helper::CREATED => "LqaIssueType Level $level => {$element['name']}: created",
				Helper::UPDATED => "LqaIssueType Level $level => {$element['name']}: updated",
				Helper::NOT_CHANGED => "LqaIssueType Level $level => {$element['name']}: up to date",
				default => '',
			};
			$output->writeln("<entval>$outputText</entval>");

			if (isset($element['childs'])) {
				$this->updateEntries($output, $element['childs'], $entity, ++$level);
			}
		}
		$this->em->flush();

		return null;
	}
}
