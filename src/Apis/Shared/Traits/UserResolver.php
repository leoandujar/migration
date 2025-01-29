<?php

declare(strict_types=1);

namespace App\Apis\Shared\Traits;

use App\Apis\CustomerPortal\Security\RedisUserTrait;
use App\Model\Entity\Customer;
use App\Model\Entity\CustomerPerson;
use App\Apis\Shared\Http\Error\ApiError;
use App\Model\Entity\SystemAccount;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use Symfony\Component\Security\Core\User\UserInterface;

trait UserResolver
{
	use RedisUserTrait;

	public function getCurrentUser(): ?UserInterface
	{
		/* @var UserInterface $user */
		return $this->requestStack->getCurrentRequest()->get('user');
	}

	public function getCurrentCustomer(): ?Customer
	{
		$user = $this->getCurrentUser();
		$activeCustomer = $this->retrieveSwitchCustomerData();
		if ($activeCustomer) {
			$customerObj = $this->em->getRepository(Customer::class)->find($activeCustomer);
			if ($customerObj) {
				return $customerObj;
			}
		}

		return $user?->getCustomersPerson()?->getCustomer();
	}

	public function getOfficeCurrentUser(UserInterface $user = null): ?string
	{
		$user = $this->getCurrentUser() ?? $user;
		$systemAccount = $user?->getSystemAccount();
		if ($systemAccount) {
			return $systemAccount->getCustomerContactManagePolicy();
		}

		return null;
	}

	public function getCustomerMembers(): array
	{
		$user = $this->getCurrentUser();
		$memberList = [];
		$customer = $this->getCurrentCustomer();
		if (!$customer) {
			return [];
		}

		$customerPersons = $customer->getCustomerPersons();
		/** @var CustomerPerson $customerPerson */
		foreach ($customerPersons as $customerPerson) {
			$contactPerson = $customerPerson->getContactPerson();
			if ($contactPerson->getId() !== $user->getId()) {
				$memberList[$contactPerson->getId()] = $contactPerson;
			}
		}

		return $memberList;
	}

	public function checkOfficePermission(mixed $entity): bool|ErrorResponse
	{
		$user = $this->getCurrentUser();
		$customer = $user->getCustomersPerson()->getCustomer();
		$officePlace = $this->getOfficeCurrentUser();
		if (empty($officePlace)) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_MANAGE_POLICY_EMPTY, ApiError::$descriptions[ApiError::CODE_MANAGE_POLICY_EMPTY]);
		}

		switch ($officePlace) {
			case SystemAccount::OFFICE_ONLY_RELATED:
				if ($entity?->getCustomerContactPerson()?->getContactPerson()?->getId() !== $user->getId()) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer_contact_person');
				}
				break;
			case SystemAccount::OFFICE_DEPARTMENT:
				$contactPersonList = $this->contactPersonRepository->getListBySystemAccount($officePlace);
				$officeContactPersonList = [];
				foreach ($contactPersonList as $cp) {
					$officeContactPersonList[] = array_shift($cp);
				}
				if (!in_array($entity?->getCustomerContactPerson()?->getContactPerson()?->getId(), $officeContactPersonList)) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer_contact_person');
				}
				break;
			case SystemAccount::OFFICE_OFFICE:
				if ($entity?->getCustomer()?->getId() !== $customer->getId()) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
				}
				break;
			case SystemAccount::OFFICE_ALL_OFFICE:
				$customerPerson = $user->getCustomersPerson();
				if (!$customerPerson) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer_person');
				}
				$customers = $customerPerson->getCustomers();
				foreach ($customers as $cust) {
					$officeCustomerList[] = $cust->getId();
				}
				if (!in_array($entity?->getCustomer()?->getId(), $officeCustomerList)) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
				}
				break;
			default:
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'manage_policy');
		}

		return true;
	}
}
