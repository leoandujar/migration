<?php

declare(strict_types=1);

namespace App\Service\Xtrf;

use App\Service\LoggerService;
use App\Apis\Shared\Util\UtilsService;
use App\Connector\Xtrf\XtrfConnector;
use App\Model\Repository\CustomerRepository;
use App\Model\Repository\LanguageTagRepository;
use App\Model\Repository\ContactPersonRepository;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

class XtrfQuoteService
{
	use XtrfTraitService;

	private LoggerService $loggerSrv;
	private LanguageTagRepository $langRepo;
	private ContactPersonRepository $contactPersonRepo;
	private CustomerRepository $customerRepo;
	private UtilsService $utilsSrv;
	private XtrfConnector $xtrfConnector;
	private CustomerPortalConnector $portalConnector;

	public function __construct(
		LoggerService $loggerSrv,
		XtrfConnector $xtrfConnector,
		CustomerPortalConnector $portalConnector,
		LanguageTagRepository $langRepo,
		ContactPersonRepository $contactPersonRepo,
		CustomerRepository $customerRepo,
		UtilsService $utilsSrv
	) {
		$this->loggerSrv = $loggerSrv;
		$this->langRepo = $langRepo;
		$this->contactPersonRepo = $contactPersonRepo;
		$this->customerRepo = $customerRepo;
		$this->utilsSrv = $utilsSrv;
		$this->xtrfConnector = $xtrfConnector;
		$this->portalConnector = $portalConnector;
		$this->loggerSrv->setSubcontext(self::class);
	}

	public function prepareCreateData(array $params): array
	{
		$data = [];
		$this->utilsSrv->arrayKeysToCamel($params);
		$requiredParams = array_flip(['targetLanguages', 'sourceLanguage', 'service', 'specialization']);
		$diff = array_diff_key($requiredParams, $params);
		if (count($diff)) {
			$this->loggerSrv->addError('Error while Creating Quote', [
				'error' => 'Missing some required params=>'.print_r($diff, true),
			]);
			throw new ParameterNotFoundException('Missing required params');
		}

		if (!empty($params['service'])) {
			$data['serviceId'] = $params['service'];
		}

		if (!empty($params['specialization'])) {
			$data['specializationId'] = $params['specialization'];
		}

		if (!empty($params['sourceLanguage'])) {
			$data['sourceLanguageId'] = $params['sourceLanguage'];
		}

		if (!empty($params['targetLanguages'])) {
			foreach ($params['targetLanguages'] as $targetLang) {
				$data['targetLanguageIds'][] = $targetLang;
			}
		}

		if (!empty($params['priceProfile'])) {
			$data['priceProfileId'] = $params['priceProfile'];
		}

		if (!empty($params['person'])) {
			$data['personId'] = $params['person'];
		}

		if (!empty($params['additionalContacts'])) {
			foreach ($params['additionalContacts'] as $contactId) {
				$data['additionalPersonIds'][] = $contactId;
			}
		}

		if (!empty($params['sendBackTo'])) {
			$data['sendBackToId'] = $params['sendBackTo'];
		}

		if (!empty($params['deliveryDate'])) {
			$data['deliveryDate'] = [
				'time' => (new \DateTime($params['deliveryDate']))->getTimestamp() * 1000,
			];
		}

		if (!empty($params['office'])) {
			$data['officeId'] = $params['office'];
		}

		if (!empty($params['autoAccept'])) {
			$data['autoAccept'] = $params['autoAccept'];
		}

		if (!empty($params['notes'])) {
			$data['notes'] = $params['notes'];
		}

		if (!empty($params['name'])) {
			$data['name'] = $params['name'];
		}

		if (!empty($params['referenceNumber'])) {
			$data['customerProjectNumber'] = $params['referenceNumber'];
		}

		if (!empty($params['inputFiles'])) {
			foreach ($params['inputFiles'] as $input) {
				if (is_array($input) && isset($input['id'])) {
					$data['files'][] = $input;
					continue;
				}
				$data['files'][] = ['id' => $input];
			}
		}

		if (!empty($params['referenceFiles'])) {
			foreach ($params['referenceFiles'] as $ref) {
				if (is_array($ref) && isset($ref['id'])) {
					$data['referenceFiles'][] = $ref;
					continue;
				}
				$data['referenceFiles'][] = ['id' => $ref];
			}
		}

		if (!empty($params['customFields'])) {
			$data['customFields'] = $params['customFields'];
		}

		return $data;
	}
}
