<?php

declare(strict_types=1);

namespace App\Service\Xtrf;

use App\Connector\Xtrf\XtrfConnector;
use App\Model\Repository\ContactPersonRepository;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait XtrfTraitService
{
	private XtrfConnector $xtrfConnector;
	private CustomerPortalConnector $portalConnector;
	private ContactPersonRepository $contactPersonRepo;

	/**
	 * XtrfTraitService constructor.
	 */
	public function __construct(
		XtrfConnector $xtrfConnector,
		CustomerPortalConnector $portalConnector,
		ContactPersonRepository $contactPersonRepo
	) {
		$this->xtrfConnector     = $xtrfConnector;
		$this->contactPersonRepo = $contactPersonRepo;
		$this->portalConnector   = $portalConnector;
	}

	public function xtrfLoginWithToken(int|string $contactPersonRef, int|string $defaultContactPersonRef = null): string
	{
		$rowToken = $this->xtrfGetRawToken($contactPersonRef, $defaultContactPersonRef);

		$loginResponse = $this->portalConnector->loginWithToken($rowToken);
		if (!$loginResponse->isSuccessfull()) {
			$msg = "Unable to get customer session id for $contactPersonRef";
			$this->loggerSrv->addError($msg);
			throw new \Exception($msg);
		}

		$rawData = $loginResponse->getRaw();
		if (empty($rawData)) {
			$msg = "Login with token response is empty for $contactPersonRef";
			$this->loggerSrv->addError($msg);
			throw new \Exception($msg);
		}

		return $rawData['jsessionid'];
	}

	public function xtrfGetRawToken(int|string $contactPersonRef, int|string $defaultContactPersonRef = null): string
	{
		$username = null;
		if (is_numeric($contactPersonRef)) {
			$contactPerson = $this->contactPersonRepo->find($contactPersonRef);
			if (!$contactPerson) {
				if (!$defaultContactPersonRef) {
					$msg = "Unable to find contact person for=>$contactPersonRef";
					$this->loggerSrv->addError($msg);
					throw new NotFoundHttpException($msg);
				}

				$contactPerson = $this->contactPersonRepo->find($defaultContactPersonRef);
				if (!$contactPerson) {
					$msg = "Unable to find default contact person for=>$defaultContactPersonRef";
					$this->loggerSrv->addError($msg);
					throw new NotFoundHttpException($msg);
				}
			}

			$sytemAccount = $contactPerson?->getSystemAccount();
			if (!$sytemAccount) {
				$msg = "Unable to find System Account for contact person=>$contactPersonRef";
				$this->loggerSrv->addError($msg);
				throw new NotFoundHttpException($msg);
			}

			$username = $sytemAccount->getUid();
		}
		$tokenResponse = $this->xtrfConnector->getSingInToken($username);
		if (!$tokenResponse->isSuccessfull()) {
			$msg = 'Unable to get customer token for getSingInToken function.';
			$this->loggerSrv->addError($msg);
			throw new \Exception($msg);
		}

		$tokenRaw = $tokenResponse->getRaw();
		if (empty($tokenRaw)) {
			$msg = "Login with token response is empty for $username";
			$this->loggerSrv->addError($msg);
			throw new \Exception($msg);
		}

		return $tokenRaw['token'];
	}
}
