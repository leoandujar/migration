<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'xtrf_user_entity_image')]
#[ORM\Index(columns: ['xtrf_user_id'], name: '')]
#[ORM\Entity]
class XtrfUserEntityImage implements EntityInterface
{
	#[ORM\Id]
	#[ORM\OneToOne(inversedBy: 'entityImage', targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'xtrf_user_id', referencedColumnName: 'xtrf_user_id', nullable: false)]
	private User $user;

	#[ORM\Column(name: 'image_data', type: 'blob', nullable: true)]
	private mixed $imageData;

	public function getImageData(): mixed
	{
		return $this->imageData;
	}

	public function setImageData($imageData): self
	{
		$this->imageData = $imageData;

		return $this;
	}

	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setUser(User $user): self
	{
		$this->user = $user;

		return $this;
	}
}
