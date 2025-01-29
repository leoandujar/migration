<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Model\Entity\Role;
use Doctrine\DBAL\Schema\Schema;
use App\Model\Entity\InternalUser;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200608122109 extends AbstractMigration
{
	

	public function up(Schema $schema): void
	{
		$em = $this->entityManager;

		$internalUser = new InternalUser();
		$internalUser
			->setUsername('admin')
			->setStatus(InternalUser::STATUS_ACTIVE)
			->setEmail('admin@admin.com')
			->setFirstName('Admin Firstname')
			->setLastName('Admin Lastname')
			->setRoles([Role::ROLE_AP_ADMIN])
			->setPassword('$2y$13$GY/wdRHTwqv6R5G1htYT.OLTQzuNuYF5NpQOFT/Zzxa/1Iv8GQpiy');
		$em->persist($internalUser);
		$em->flush();
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
	}
}
