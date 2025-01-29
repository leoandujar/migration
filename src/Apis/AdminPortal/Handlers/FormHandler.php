<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Util\Factory;
use App\Model\Entity\APForm;
use App\Model\Entity\APFormTemplate;
use App\Model\Entity\InternalUser;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Traits\UserResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\InternalUserRepository;

class FormHandler
{
	use UserResolver;

	private EntityManagerInterface $em;
	private InternalUserRepository $userRepo;
	private SecurityHandler $securityHandler;

	/**
	 * FormHandler constructor.
	 */
	public function __construct(
		EntityManagerInterface $em,
		SecurityHandler $securityHandler,
		InternalUserRepository $userRepo
	) {
		$this->em = $em;
		$this->userRepo = $userRepo;
		$this->securityHandler = $securityHandler;
	}

	public function processGetList(): ApiResponse
	{
		$result = [];
		foreach ($this->em->getRepository(APForm::class)->findAll() as $form) {
			$result[] = Factory::apFormDtoInstance($form);
		}

		return new ApiResponse(data: $result);
	}

	public function processRetrieve(string $id): ApiResponse
	{
		/** @var APForm $template */
		$form = $this->em->getRepository(APForm::class)->find($id);
		if (!$form) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'form');
		}

		return new ApiResponse(data: Factory::apFormDtoInstance($form));
	}

	public function processCreate(Request $request, array $params): ApiResponse
	{
		$approvers = $this->userRepo->findBy(['id' => $params['approvers_id']]);

		if (!$approvers) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'approvers');
		}

		$template = $this->em->getRepository(APFormTemplate::class)->find($params['template_id']);

		if (!$template) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'template');
		}

		/** @var InternalUser $user */
		$currentUser = $this->securityHandler->getCurrentUser($request);

		if (!$currentUser) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$form = new APForm();
		$form
			->setApprovers($params['approvers_id'])
			->setCreatedBy($currentUser)
			->setTemplate($template)
			->setCategory($params['category'])
			->setName(strip_tags($params['name']))
			->setPmkTemplateId(strip_tags($params['pmk_template_id']));
		$this->em->persist($form);
		$this->em->flush();

		return new ApiResponse(data: Factory::apFormDtoInstance($form));
	}

	public function processUpdate(array $params): ApiResponse
	{
		/** @var APForm $form */
		$form = $this->em->getRepository(APForm::class)->find($params['id']);

		if (!$form) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'form');
		}

		if (!empty($params['approvers_id'])) {
			$approvers = $this->userRepo->findBy(['id' => $params['approvers_id']]);

			if (!$approvers) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'approvers');
			}
			$form->setApprovers($params['approvers_id']);
		}

		if (!empty($params['template_id'])) {
			$template = $this->em->getRepository(APFormTemplate::class)->find($params['template_id']);

			if (!$template) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'template');
			}
			$form->setTemplate($template);
		}

		if (!empty($params['category'])) {
			$form->setCategory(intval($params['category']));
		}

		if (!empty($params['name'])) {
			$form->setName(strip_tags($params['name']));
		}

		if (!empty($params['pmk_template_id'])) {
			$form->setPmkTemplateId(strip_tags($params['pmk_template_id']));
		}

		$this->em->persist($form);
		$this->em->flush();

		return new ApiResponse(data: Factory::apFormDtoInstance($form));
	}

	public function processDelete(array $params): ApiResponse
	{
		$form = $this->em->getRepository(APForm::class)->find($params['id']);

		if (!$form) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'form');
		}

		$this->em->remove($form);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
