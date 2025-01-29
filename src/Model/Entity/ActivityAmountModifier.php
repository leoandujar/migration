<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'activity_amount_modifiers')]
#[ORM\Entity]
class ActivityAmountModifier implements EntityInterface
{
	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: Activity::class, inversedBy: 'amountModifiersList')]
	#[ORM\JoinColumn(name: 'activity_id', referencedColumnName: 'activity_id', nullable: false)]
	private Activity $activity;

	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: AmountModifier::class, inversedBy: 'activitiesList')]
	#[ORM\JoinColumn(name: 'amount_modifier_id', referencedColumnName: 'amount_modifier_id', nullable: false)]
	private AmountModifier $amountModifier;

	#[ORM\Column(name: '`index`', type: 'integer', nullable: false, options: ['default' => 0])]
	private int $index;

	/**
	 * @return mixed
	 */
	public function getIndex(): ?int
	{
		return $this->index;
	}

	/**
	 * @return mixed
	 */
	public function setIndex(int $index): self
	{
		$this->index = $index;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getActivity(): ?Activity
	{
		return $this->activity;
	}

	/**
	 * @return mixed
	 */
	public function setActivity(?Activity $activity): self
	{
		$this->activity = $activity;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAmountModifier(): ?AmountModifier
	{
		return $this->amountModifier;
	}

	/**
	 * @return mixed
	 */
	public function setAmountModifier(?AmountModifier $amountModifier): self
	{
		$this->amountModifier = $amountModifier;

		return $this;
	}
}
