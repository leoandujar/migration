<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Apis\CustomerPortal\Factory\ResponseFactory;
use App\Apis\Shared\Http\Response\DefaultPaginationResponse;
use App\Apis\Shared\DTO\PaginationDto;
use App\Apis\Shared\Handlers\BaseHandler;
use App\Model\Entity\Role;
use App\Model\Entity\CPTemplate;
use App\Apis\Shared\Util\UtilsService;
use App\Connector\Xtrf\XtrfConnector;
use App\Apis\Shared\Http\Error\ApiError;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FileSystem\FileSystemService;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Model\Repository\CPTemplateRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TemplateHandler extends BaseHandler
{
	private UtilsService $utilsSrv;
	private SessionInterface $session;
	private EntityManagerInterface $em;
	private XtrfConnector $xtrfConnector;
	private FileSystemService $fileSystemSrv;
	private TokenStorageInterface $tokenStorage;
	private CustomerPortalConnector $clientConnector;
	private CPTemplateRepository $templateRepository;
	private RequestStack $requestStack;

	public function __construct(
		RequestStack $requestStack,
		TokenStorageInterface $tokenStorage,
		UtilsService $utilsSrv,
		CPTemplateRepository $templateRepository,
		FileSystemService $fileSystemSrv,
		EntityManagerInterface $em,
		XtrfConnector $xtrfConnector,
		CustomerPortalConnector $clientConnector,
	) {
		parent::__construct($requestStack, $em);
		$this->tokenStorage = $tokenStorage;
		$this->xtrfConnector = $xtrfConnector;
		$this->session = $requestStack->getSession();
		$this->clientConnector = $clientConnector;
		$this->utilsSrv = $utilsSrv;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->em = $em;
		$this->templateRepository = $templateRepository;
		$this->requestStack = $requestStack;
	}

	public function processGetList(array $params): ApiResponse
	{
		$user = $this->getCurrentUser();
		$type = $params['type'] ?? null;
		$search = $params['search'] ?? null;
		$ids = array_keys($this->getCustomerMembers());
		if (null !== $type) {
			$type = intval($type);
			if (CPTemplate::TYPE_CONTACT_PERSON === $type) {
				$ids = [$user->getId()];
			}
			$totalRowCount = $this->templateRepository->getCountOfTemplates($ids, $type);
			$paginationDto = new PaginationDto($params['page'], $params['per_page'], $totalRowCount, $params['sort_by'], $params['sort_order']);
			$templates = $this->templateRepository->getByContactPerson($ids, $type, $paginationDto->from, $params['per_page']);
		} else {
			$ids[] = $user->getId();
			$totalRowCount = $this->templateRepository->getCountByCustomerAndContactPerson($ids, CPTemplate::TYPE_CUSTOMER, $user->getId(), CPTemplate::TYPE_CONTACT_PERSON, $search);
			$paginationDto = new PaginationDto($params['page'], $params['per_page'], $totalRowCount, $params['sort_by'], $params['sort_order']);
			$templates = $this->templateRepository->getByCustomerAndContactPerson($ids, CPTemplate::TYPE_CUSTOMER, $user->getId(), CPTemplate::TYPE_CONTACT_PERSON, $paginationDto->from, $params['per_page'], search: $search);
		}

		$result = [];
		/** @var CPTemplate $template */
		foreach ($templates as $template) {
			$owner = $template->getContactPerson()->getId() === $user->getId();
			$result[] = ResponseFactory::templateDtoInstance($template, $owner);
		}

		$response = new DefaultPaginationResponse(
			[
				'entities' => $result,
			]
		);
		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function processGetTemplate(string $id): ApiResponse
	{
		$user = $this->getCurrentUser();
		$template = $this->templateRepository->find($id);

		if (!$template) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'template');
		}

		$ids = array_keys($this->getCustomerMembers());
		if (CPTemplate::TYPE_CUSTOMER === $template->getType()) {
			$ids[] = $user->getId();
		} else {
			$ids = [$user->getId()];
		}

		$templateContactId = $template->getContactPerson()?->getId();
		if (!in_array($templateContactId, $ids)) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
		}

		$owner = $templateContactId === $user->getId();

		$result = ResponseFactory::templateDtoInstance($template, $owner);

		return new ApiResponse(data: $result);
	}

	public function processCreate(array $params): ApiResponse
	{
		$user = $this->getCurrentUser();
		$type = $params['type'];
		if (CPTemplate::TYPE_CUSTOMER === $type && (!in_array(Role::ROLE_CP_ADMIN, $user->getRoles()))) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
		}

		$template = new CPTemplate();
		$template
			->setName($params['name'])
			->setType($type)
			->setDataNew($params['data'])
			->setContactPerson($user);
		$this->em->persist($template);
		$this->em->flush();

		$result = ResponseFactory::templateDtoInstance($template, true);

		return new ApiResponse(data: $result);
	}

	public function processUpdate(array $params): ApiResponse
	{
		$user = $this->getCurrentUser();
		$template = $this->templateRepository->find($params['id']);
		if (!$template) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'template');
		}

		if (CPTemplate::TYPE_CUSTOMER === $template->getType() && (!in_array(Role::ROLE_CP_ADMIN, $user->getRoles()))) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
		} else {
			if ($template->getContactPerson()->getId() !== $user->getId()) {
				return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
			}
		}
		if (!empty($params['name'])) {
			$template->setName($params['name']);
		}
		if (!empty($params['type'])) {
			$template->setType($params['type']);
		}
		if (!empty($params['data'])) {
			$template->setDataNew($params['data']);
		}
		$this->em->persist($template);
		$this->em->flush();

		$result = ResponseFactory::templateDtoInstance($template, true);

		return new ApiResponse(data: $result);
	}

	public function processDelete(int $id): ApiResponse
	{
		$user = $this->getCurrentUser();
		$template = $this->templateRepository->find($id);
		if (!$template) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'template');
		}

		if (CPTemplate::TYPE_CUSTOMER === $template->getType() && (!in_array(Role::ROLE_CP_ADMIN, $user->getRoles()))) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
		} else {
			if ($template->getContactPerson()->getId() !== $user->getId()) {
				return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
			}
		}

		$this->em->remove($template);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
