<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'task_amount_modifiers')]
#[ORM\Entity]
class TaskAmountModifier implements EntityInterface
{
	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: TaskFinance::class, inversedBy: 'amountModifiersList')]
	#[ORM\JoinColumn(name: 'task_finance_id', referencedColumnName: 'task_finance_id', nullable: false)]
	private TaskFinance $taskFinance;

	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: AmountModifier::class, inversedBy: 'tasksList')]
	#[ORM\JoinColumn(name: 'amount_modifier_id', referencedColumnName: 'amount_modifier_id', nullable: false)]
	private AmountModifier $amountModifier;

	#[ORM\Column(name: '`index`', type: 'integer', nullable: false, options: ['default' => 0])]
	private int $index;

	public function getIndex(): ?int
	{
		return $this->index;
	}

	public function setIndex(int $index): self
	{
		$this->index = $index;

		return $this;
	}

	public function getTaskFinance(): ?TaskFinance
	{
		return $this->taskFinance;
	}

	public function setTaskFinance(?TaskFinance $taskFinance): self
	{
		$this->taskFinance = $taskFinance;

		return $this;
	}

	public function getAmountModifier(): ?AmountModifier
	{
		return $this->amountModifier;
	}

	public function setAmountModifier(?AmountModifier $amountModifier): self
	{
		$this->amountModifier = $amountModifier;

		return $this;
	}
}
