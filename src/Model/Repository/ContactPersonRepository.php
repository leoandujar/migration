<?php

namespace App\Model\Repository;

use App\Model\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use App\Model\Entity\ContactPerson;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Parameter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ContactPersonRepository extends EntityRepository implements UserProviderInterface
{
	public function __construct(EntityManagerInterface $em)
	{
		$class = $em->getClassMetadata(ContactPerson::class);
		parent::__construct($em, $class);
	}

	public function loadUserByUsername($identifier): ?UserInterface
	{
		return $this->loadUserByIdentifier($identifier);
	}

	public function loadUserByIdentifier(string $identifier): UserInterface
	{
		return $this->findOneBy(['id' => $identifier]);
	}

	public function refreshUser(UserInterface $user): UserInterface
	{
		throw new UnsupportedUserException();
	}

	public function supportsClass($class): bool
	{
		return User::class === $class;
	}

	public function getByUsername(string $username)
	{
		$q = $this->createQueryBuilder('cp');
		$q->select('cp')
			->innerJoin('cp.systemAccount', 'sa')
			->where($q->expr()->eq('sa.uid', ':username'))
			->setParameters(new ArrayCollection([
				new Parameter('username', $username),
			]));
		try {
			return $q->getQuery()->getOneOrNullResult();
		} catch (NonUniqueResultException $e) {
			return null;
		}
	}

	public function getListBySystemAccount(string $office): array
	{
		$q = $this->createQueryBuilder('cp');
		$q->select('cp.id')
			->innerJoin('cp.systemAccount', 'sa')
			->where($q->expr()->eq('sa.customerContactManagePolicy', ':office'))
			->setParameters(new ArrayCollection([
				new Parameter('office', $office),
			]));

		return $q->getQuery()->getArrayResult();
	}

	public function getListBySystemAccountDepartment(string $personDepartmentId, string $customerId): array
	{
		$q = $this->createQueryBuilder('cp');
		$q->select('cp.id')
			->innerJoin('cp.personDepartment', 'pd')
			->innerJoin('cp.customersPerson', 'csp')
			->where($q->expr()->andX(
				$q->expr()->eq('pd.id', ':departmentId'),
				$q->expr()->eq('csp.customer', ':customerId'),
			))
			->setParameters(new ArrayCollection([
				new Parameter('departmentId', $personDepartmentId),
				new Parameter('customerId', $customerId),
			]));

		return $q->getQuery()->getArrayResult();
	}

	public function getContactPersons(?string $firstName, ?string $lastname, ?int $customerId, int $type, int $limit): ?array
	{
		$q = $this->createQueryBuilder('cp');
		$q->select(
			'cp.id,
			cp.email,
			cp.name,
			cp.lastName'
		);
		if (2 === $type) {
			$q->innerJoin('cp.provider', 'p');
		}
		if (1 === $type) {
			$q->select(
				'cp.id,
				cp.email,
				cp.name,
				cp.lastName,
				sa.webLoginAllowed',
			);
			$q->innerJoin('cp.customersPerson', 'c')
				->innerJoin('cp.systemAccount', 'sa');
		}

		$q->where('cp.active = :active')
		->setParameter('active', true);

		if (!empty($firstName)) {
			$q->andWhere($q->expr()->like('LOWER(cp.name)', 'LOWER(:firstName)'))
				->setParameter('firstName', "%$firstName%");
		}

		if (!empty($lastname)) {
			$q->andWhere($q->expr()->like('LOWER(cp.lastName)', 'LOWER(:lastname)'))
				->setParameter('lastname', "%$lastname%");
		}

		if (!empty($customerId)) {
			$q->andWhere($q->expr()->eq('c.customer', ':customerId'))
				->setParameter('customerId', $customerId);
		}

		$q->setMaxResults($limit);

		return $q->getQuery()->getArrayResult();
	}
}
