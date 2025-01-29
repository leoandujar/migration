<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'project_archived_directories')]
#[ORM\Entity]
class ProjectArchivedDirectory implements EntityInterface
{
	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'project_id', nullable: true)]
	private ?Project $project;

	#[ORM\Id]
	#[ORM\Column(name: 'archived_directory', type: 'text', nullable: false)]
	private string $archivedDirectory;
}
