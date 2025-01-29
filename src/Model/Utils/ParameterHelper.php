<?php

namespace App\Model\Utils;

use App\Model\Entity\Parameter;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Repository\ParameterRepository;

class ParameterHelper
{
	/**
	 * @var string
	 */
	protected $scope = '';
	/**
	 * @var ParameterRepository
	 */
	private $parameterRepository;
	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	/**
	 * ParameterHelper constructor.
	 */
	public function __construct(
		EntityManagerInterface $em,
		ParameterRepository $parameterRepository
	) {
		$this->em = $em;
		$this->parameterRepository = $parameterRepository;
	}

	public function get(string $name, string $scope = null): ?string
	{
		$scope = ($scope ?: $this->scope);
		$this->parameterRepository->clear();
		$optionEntity = $this->parameterRepository->findByNameAndScope($name, $scope);
		if (null === $optionEntity) {
			return null;
		}

		return $optionEntity->getValue();
	}

	/**
	 * @param bool $flush
	 *
	 * @return bool
	 */
	public function set(string $name, ?string $value, string $scope = null, $flush = true)
	{
		$scope = ($scope ?: $this->scope);
		$parameter = $this->parameterRepository->findByNameAndScope($name, $scope);

		if (null === $parameter) {
			$parameter = new Parameter();
			$parameter->setName($name);
			$parameter->setScope($scope);
		}
		$parameter->setValue($value);

		$this->em->persist($parameter);
		if ($flush) {
			$this->em->flush();
		}

		return true;
	}

	public function setScope(string $scope): void
	{
		$this->scope = $scope;
	}
}
