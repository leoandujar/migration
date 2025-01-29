<?php

namespace App\Apis\Shared\Listener;

use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Facade\AppFacade;
use App\Linker\Services\RedisClients;
use App\Model\Entity\ContactPerson;
use App\Model\Entity\CPToken;
use App\Model\Entity\InternalUser;
use App\Model\Entity\Permission;
use App\Model\Entity\Role;
use App\Model\Repository\PermissionsRepository;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\FileSystem\FileSystemService;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use oasis\names\specification\ubl\schema\xsd\CommonAggregateComponents_2\Contact;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthenticationListener
{
	private JwtService $jwtSrv;
	private EntityManagerInterface $em;
	private PermissionsRepository $permissionsRepo;

	public function __construct(
		JwtService $jwtSrv,
		EntityManagerInterface $em,
		ValidatorInterface $validator,
		FileSystemService $fileSystemSrv,
		CloudFileSystemService $fileBucketSrv,
		RedisClients $redisClients,
		PermissionsRepository $permissionsRepo,
	) {
		$this->em = $em;
		$this->jwtSrv = $jwtSrv;
		$this->permissionsRepo = $permissionsRepo;
		AppFacade::getInstance()->validator = $validator;
		AppFacade::getInstance()->fileSystemSrv = $fileSystemSrv;
		AppFacade::getInstance()->fileBucketSrv = $fileBucketSrv;
		AppFacade::getInstance()->redisClients = $redisClients;
		AppFacade::getInstance()->jwtSrv = $jwtSrv;
	}

	public function onKernelRequest(RequestEvent $event): void
	{
		try {
			$controllerData = $event->getRequest()->attributes->get('_controller');
			$routeDetails = explode('::', $controllerData, 2);
			if (2 !== count($routeDetails)) {
				$event->setResponse(new Response(Response::HTTP_NOT_FOUND));

				return;
			}

			if ('cp_2fa' === $event->getRequest()->attributes->get('_route') && ($tfaCheck = $this->check2fa($event)) instanceof ErrorResponse) {
				$event->setResponse($tfaCheck);
			}
			$controllerClass = $routeDetails[0];
			$methodName = $routeDetails[1];
			$reflectionMethod = new \ReflectionMethod($controllerClass, $methodName);
			foreach ($reflectionMethod->getAttributes(PublicEndpoint::class) as $attribute) {
				if ($attribute->newInstance() instanceof PublicEndpoint) {
					return;
				}
			}

			$this->checkToken($event);
		} catch (\Throwable $thr) {
			$event->setResponse(
				new ErrorResponse(
					Response::HTTP_UNAUTHORIZED,
					ApiError::CODE_INVALID_VALUE,
					ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
					'token'
				)
			);
		}
	}

	private function checkToken(RequestEvent $event): void
	{
		$request = $event->getRequest();

		$sentToken = $this->jwtSrv->extract();

		if (!$sentToken) {
			$event->setResponse(
				new ErrorResponse(
					Response::HTTP_UNAUTHORIZED,
					ApiError::CODE_INVALID_VALUE,
					ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
					'token'
				)
			);

			return;
		}

		$payload = (array) $this->jwtSrv->decode($sentToken);
		$receivedSubnet = implode('.', array_slice(explode('.', $payload['ip']), 0, 3));
		$currentSubnet = implode('.', array_slice(explode('.', $request->getClientIp()), 0, 3));
		if ($receivedSubnet !== $currentSubnet) {
			$event->setResponse(
				new ErrorResponse(
					Response::HTTP_UNAUTHORIZED,
					ApiError::CODE_INVALID_VALUE,
					ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
					'token'
				)
			);

			return;
		}

		if ($this->jwtSrv->isExpired($payload)) {
			$event->setResponse(
				new ErrorResponse(
					Response::HTTP_UNAUTHORIZED,
					ApiError::CODE_TOKEN_EXPIRED,
					ApiError::$descriptions[ApiError::CODE_TOKEN_EXPIRED]
				)
			);

			return;
		}

		$event->getRequest()->attributes->set('logged_jwt', $sentToken);
		$this->isAllowed($event, $request->get('_route'), $payload);
	}

	private function isAllowed(RequestEvent $event, string $action, array $payload): void
	{
		$action = strtoupper($action);

		$user = match ($payload['target']) {
			Permission::TARGET_ADMIN_PORTAL => $this->em->getRepository(InternalUser::class)->find($payload['identity']),
			Permission::TARGET_CLIENT_PORTAL => $this->em->getRepository(ContactPerson::class)->find($payload['identity']),
		};

		if (!$user) {
			$event->setResponse(
				new ErrorResponse(
					Response::HTTP_BAD_REQUEST,
					ApiError::CODE_NOT_FOUND,
					ApiError::$descriptions[ApiError::CODE_NOT_FOUND],
					'user'
				)
			);

			return;
		}

		$event->getRequest()->attributes->set('user', $user);
		$event->getRequest()->attributes->set('user_id', $user->getId());
		switch ($payload['target']) {
			case Permission::TARGET_ADMIN_PORTAL:
				if (in_array(Role::ROLE_AP_ADMIN, array_map('trim', $user->getRoles()), true)) {
					return;
				}

				$response = $this->hasPermissions($action, Permission::TARGET_ADMIN_PORTAL, $user);
				if ($response instanceof ApiResponse) {
					return;
				}
				break;
			case Permission::TARGET_CLIENT_PORTAL:
				if (in_array(Role::ROLE_CP_ADMIN, $user->getRoles())) {
					return;
				}

				$customer = $user->getCustomersPerson()?->getCustomer();
				$customerParent = $user->getCustomersPerson()?->getCustomer()?->getParentCustomer();

				if (in_array(Role::ROLE_CP_ADMIN, $customer->getRoles())) {
					return;
				}

				$response = $this->hasPermissions($action, Permission::TARGET_CLIENT_PORTAL, $user);
				if ($response instanceof ApiResponse) {
					return;
				}

				$response = $this->hasPermissions($action, Permission::TARGET_CLIENT_PORTAL, $customer);
				if ($response instanceof ApiResponse) {
					return;
				}

				$response = $this->hasPermissions($action, Permission::TARGET_CLIENT_PORTAL, $customerParent);
				if ($response instanceof ApiResponse) {
					return;
				}
				break;
		}

		$event->setResponse(
			new ErrorResponse(
				Response::HTTP_FORBIDDEN,
				ApiError::CODE_FORBIDDEN_ACTION,
				ApiError::$descriptions[ApiError::CODE_FORBIDDEN_ACTION]
			)
		);
	}

	private function hasPermissions(string $action, $target, UserInterface $entity): ApiResponse|false
	{
		if (!$entity) {
			return false;
		}
		$userAllowedActions = $this->permissionsRepo->getActionsByUserOrRoles($entity->getId(), $target);
		if (isset($userAllowedActions[strtoupper($action)]) && true === $userAllowedActions[strtoupper($action)]) {
			return new ApiResponse(code: Response::HTTP_NO_CONTENT);
		}

		$rolesAllowedActions = $this->permissionsRepo->getActionsByUserOrRoles($entity->getRoles(), $target);
		if (isset($rolesAllowedActions[strtoupper($action)]) && true === $rolesAllowedActions[strtoupper($action)]) {
			return new ApiResponse(code: Response::HTTP_NO_CONTENT);
		}

		return false;
	}

	private function check2fa(RequestEvent $event): ?ErrorResponse
	{
		$user = $event->getRequest()->getPayload()->get('user');
		if (!$user) {
			return new ErrorResponse(
				Response::HTTP_UNAUTHORIZED,
				ApiError::CODE_TFA_FAILED,
				ApiError::$descriptions[ApiError::CODE_TFA_FAILED]
			);
		}

		$smsCode = $event->getRequest()->getPayload()->get('sms_code');
		if (!$smsCode) {
			return new ErrorResponse(
				Response::HTTP_UNAUTHORIZED,
				ApiError::CODE_TFA_FAILED,
				ApiError::$descriptions[ApiError::CODE_TFA_FAILED]
			);
		}

		$token = $this->em->getRepository(CPToken::class)->find($user);
		if (!$token) {
			return new ErrorResponse(
				Response::HTTP_UNAUTHORIZED,
				ApiError::CODE_TFA_FAILED,
				ApiError::$descriptions[ApiError::CODE_TFA_FAILED]
			);
		}

		if ($token->getToken() !== strtoupper($smsCode)) {
			return new ErrorResponse(
				Response::HTTP_UNAUTHORIZED,
				ApiError::CODE_TFA_FAILED,
				ApiError::$descriptions[ApiError::CODE_TFA_FAILED]
			);
		}
		$this->em->remove($token);
		$this->em->flush();
		$event->getRequest()->attributes->set('user', $token->getUser());

		return null;
	}
}
