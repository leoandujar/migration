<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'task_cat_charge_amount_modifiers')]
#[ORM\Entity]
class TaskCatChargeAmountModifier implements EntityInterface
{
	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: TaskCatCharge::class, inversedBy: 'taskCatChargeAmountModifiers')]
	#[ORM\JoinColumn(name: 'task_cat_charge_id', referencedColumnName: 'task_cat_charge_id', nullable: false)]
	private TaskCatCharge $taskCatCharge;

	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: AmountModifier::class, inversedBy: 'taskCatChargeAmountModifiers')]
	#[ORM\JoinColumn(name: 'amount_modifier_id', referencedColumnName: 'amount_modifier_id', nullable: false)]
	private AmountModifier $amountModifier;

	#[ORM\Column(name: '`index`', type: 'integer', nullable: false, options: ['default' => 0])]
	private int $index;

	// ################################ NORMAL RELATION FIELDS START HERE################
}
