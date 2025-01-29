<?php

namespace App\Apis\Shared\Handlers;

use App\Model\Entity\Role;
use App\Service\LoggerService;
use App\Apis\Shared\Traits\UserResolver;
use Doctrine\ORM\EntityManagerInterface;

class SecurityHandler
{
	use UserResolver;

	private EntityManagerInterface $em;
	private LoggerService $loggerSrv;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
	}

	public function getAbilities(array $roles): array
	{
		$abilities = [];
		foreach ($roles as $roleString) {
			$role = $this->em->getRepository(Role::class)->findOneBy(['code' => $roleString]);
			if ($role) {
				$a = $role->getAbilities();
				$abilities = array_merge($abilities, $a);
			}
		}

		return array_values(array_unique($abilities, SORT_REGULAR));
	}
}
