<?php

declare(strict_types=1);

namespace App\Apis\CustomerPortal\Factory;

use App\Apis\Shared\DTO\CPTemplateDto;
use App\Model\Entity\CPTemplate;

class ResponseFactory
{
	public static function templateDtoInstance(CPTemplate $template, bool $owner): CPTemplateDto
	{
		return new CPTemplateDto(
			$template->getId(),
			$template->getName(),
			$template->getType(),
			$template->getDataNew(),
			$owner
		);
	}
}
