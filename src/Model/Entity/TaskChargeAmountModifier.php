<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'task_charge_amount_modifiers')]
#[ORM\Index(name: 'task_charge_amount_modifiers_amount_modifier_id_idx', columns: ['amount_modifier_id'])]
#[ORM\Entity]
class TaskChargeAmountModifier implements EntityInterface
{
	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: TaskCharge::class, inversedBy: 'taskChargeAmountModifiers')]
	#[ORM\JoinColumn(name: 'task_charge_id', referencedColumnName: 'task_charge_id', nullable: false)]
	private TaskCharge $taskCharge;

	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: AmountModifier::class, inversedBy: 'taskChargeAmountModifiers')]
	#[ORM\JoinColumn(name: 'amount_modifier_id', referencedColumnName: 'amount_modifier_id', nullable: false)]
	private AmountModifier $amountModifier;

	#[ORM\Column(name: '`index`', type: 'integer', nullable: false, options: ['default' => 0])]
	private int $index;

	// ################################ NORMAL RELATION FIELDS START HERE################
}
