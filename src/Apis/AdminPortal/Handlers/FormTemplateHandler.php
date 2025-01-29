<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Util\Factory;
use App\Model\Entity\APFormTemplate;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Traits\UserResolver;
use Doctrine\ORM\EntityManagerInterface;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;

class FormTemplateHandler
{
	use UserResolver;

	private EntityManagerInterface $em;

	public function __construct(
		EntityManagerInterface $em
	) {
		$this->em           = $em;
	}

	public function processGetList(): ApiResponse
	{
		$result = [];
		foreach ($this->em->getRepository(APFormTemplate::class)->findAll() as $template) {
			$result[] = Factory::apFormTemplateDtoInstance($template);
		}

		return new ApiResponse(data: $result);
	}

	public function processRetrieve(string $id): ApiResponse
	{
		/** @var APFormTemplate $template */
		$template = $this->em->getRepository(APFormTemplate::class)->find($id);

		if (!$template) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'template');
		}

		return new ApiResponse(data: Factory::apFormTemplateDtoInstance($template));
	}

	public function processCreate(array $params): ApiResponse
	{
		$template = new APFormTemplate();
		$template
			->setName(strip_tags($params['name']))
			->setType(intval($params['type']))
			->setContent($params['content']);
		$this->em->persist($template);
		$this->em->flush();

		return new ApiResponse(data: Factory::apFormTemplateDtoInstance($template));
	}

	public function processUpdate(array $params): ApiResponse
	{
		/** @var APFormTemplate $template */
		$template = $this->em->getRepository(APFormTemplate::class)->find($params['id']);

		if (!$template) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'template');
		}

		if (!empty($params['name'])) {
			$template->setName(strip_tags($params['name']));
		}

		if (!empty($params['type'])) {
			$template->setType(intval($params['type']));
		}

		if (!empty($params['content'])) {
			$template->setContent($params['content']);
		}

		$this->em->persist($template);
		$this->em->flush();

		return new ApiResponse(data: Factory::apFormTemplateDtoInstance($template));
	}

	public function processDelete(array $params): ApiResponse
	{
		$template = $this->em->getRepository(APFormTemplate::class)->find($params['id']);

		if (!$template) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'template');
		}

		$this->em->remove($template);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
