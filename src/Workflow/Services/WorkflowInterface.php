<?php

namespace App\Workflow\Services;

use App\Model\Entity\WFParams;

interface WorkflowInterface
{
	public function run(string $name, WFParams $params = null): void;
}
