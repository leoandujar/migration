<?php

namespace App\Linker\Services;

use App\Connector\Hubspot\HubspotConnector;
use App\Model\Entity\Project;
use Doctrine\ORM\EntityManager;
use HubSpot\Crm\ObjectType;
use App\Model\Entity\HsDeal;
use App\Service\LoggerService;
use App\Model\Entity\Customer;
use App\Model\Entity\HsCustomer;
use App\Model\Entity\HsPipeline;
use App\Model\Entity\HsEngagement;
use App\Model\Entity\InternalUser;
use App\Model\Entity\ContactPerson;
use App\Model\Entity\HsContactPerson;
use App\Model\Entity\HsPipelineStage;
use App\Model\Entity\HsMarketingEmail;
use App\Model\Entity\HsEngagementAssoc;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Repository\CustomerRepository;
use App\Model\Repository\WorkflowRepository;
use App\Model\Repository\InternalUserRepository;
use App\Model\Repository\ContactPersonRepository;

class HubspotQueueService
{
	public const OPERATION_CREATE = 'creation';
	public const OPERATION_UPDATE = 'propertyChange';
	public const OPERATION_DELETE = 'deletion';
	public const OPERATION_CREATE_OR_UPDATE = 'create-or-update';
	public const OPERATION_UPDATE_REMOTE = 'update-remote';

	public const ENTITY_NAME_CUSTOMER = ObjectType::COMPANIES;
	public const ENTITY_NAME_CONTACTS = ObjectType::CONTACTS;
	public const ENTITY_NAME_DEALS = ObjectType::DEALS;
	public const ENTITY_NAME_OWNERS = 'owners';
	public const ENTITY_NAME_MARKETING = 'marketing';
	public const ENTITY_NAME_PIPELINES = 'pipelines';
	public const ENTITY_NAME_ENGAGEMENTS = 'engagements';

	public const DEFAULT_PAGE_SIZE = 100;

	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private InternalUserRepository $userRepository;
	private CustomerRepository $customerRepository;
	private ContactPersonRepository $contactPersonRepository;
	private WorkflowRepository $workflowRepo;
	private HubspotConnector $hsConnector;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		HubspotConnector $hsConnector,
		InternalUserRepository $userRepository,
		CustomerRepository $customerRepository,
		ContactPersonRepository $contactPersonRepository,
		WorkflowRepository $workflowRepo,
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->userRepository = $userRepository;
		$this->customerRepository = $customerRepository;
		$this->contactPersonRepository = $contactPersonRepository;
		$this->workflowRepo = $workflowRepo;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_LINKERS);
		$this->hsConnector = $hsConnector;
	}

	public function processEntity(object $data): bool
	{
		try {
			$response = false;
			do {
				$entityName = $data->entityName;
				$operation = $data->operation;
				$objectData = $data->data;
				if (empty($objectData)) {
					$this->loggerSrv->addWarning('Hubspot processing found empty data saved in the queue.');
					break;
				}
				if (empty($operation)) {
					$this->loggerSrv->addWarning('Hubspot processing found empty operation saved in the queue.');
					break;
				}

				return match ($entityName) {
					self::ENTITY_NAME_CUSTOMER => $this->processCompanies($objectData, $operation),
					self::ENTITY_NAME_CONTACTS => $this->processContacts($objectData, $operation),
					self::ENTITY_NAME_DEALS => $this->processDeals($objectData, $operation),
					self::ENTITY_NAME_MARKETING => $this->processMarketingEmail($objectData, $operation),
					self::ENTITY_NAME_PIPELINES => $this->processPipelines($objectData, $operation),
					self::ENTITY_NAME_ENGAGEMENTS => $this->processEngagements($objectData, $operation),
					default => false,
				};
			} while (0);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Hubspot=> Error processing data in the Linker. Recommended enqueue again.', $thr);
		}

		return $response;
	}

	/**
	 * @throws \Exception
	 */
	public function processCompanies(array $objectData, string $operation): bool
	{
		if (empty($objectData['id'])) {
			$this->loggerSrv->addWarning('Object has empty ID, unable to continue.');

			return false;
		}

		if (self::OPERATION_UPDATE_REMOTE !== $operation) {
			$hsCustomer = $this->em->getRepository(HsCustomer::class)->findOneBy(['hsCustomerId' => $objectData['id']]);
			$exists = true;
		}

		switch ($operation) {
			case self::OPERATION_CREATE:
				if (null !== $hsCustomer) {
					$this->loggerSrv->addWarning('Operation is CREATE but entity already exists.');

					return true;
				}
				$hsCustomer = new HsCustomer();
				break;
			case self::OPERATION_UPDATE:
				if (null === $hsCustomer) {
					$dealObjRemote = $this->hsConnector->findById(HubspotQueueService::ENTITY_NAME_CUSTOMER, $objectData['id']);
					if (null === $dealObjRemote) {
						$this->loggerSrv->addWarning('Operation is UPDATE but entity does not exists on DB neither on hubspot server.');

						return false;
					}
					$objectData = json_decode($dealObjRemote->toHeaderValue(), true);
					$hsCustomer = new HsCustomer();
				}
				break;
			case self::OPERATION_DELETE:
				if (null === $hsCustomer) {
					$this->loggerSrv->addWarning("Operation is $operation but entity does not exists.");

					return false;
				} else {
					$this->em->remove($hsCustomer);
					$this->em->flush();

					return true;
				}
				// no break
			case self::OPERATION_CREATE_OR_UPDATE:
				if (null === $hsCustomer) {
					$exists = false;
					$hsCustomer = new HsCustomer();
				}
				break;
			case self::OPERATION_UPDATE_REMOTE:
				/** @var HsCustomer $hsCustomer */
				$hsCustomer = $this->em->getRepository(HsCustomer::class)->findOneBy(['customer' => $objectData['id']]);
				if (null === $hsCustomer) {
					$this->loggerSrv->addWarning("Operation is $operation but entity customer does not exists in  our DB.");

					return false;
				}
				if (null === $hsCustomer->getCustomer()) {
					$this->loggerSrv->addWarning("Hubspot remote update. HsCustomer {$hsCustomer->getId()} has not local customer linked.");

					return false;
				}

				$this->loggerSrv->addInfo("Hubspot remote update. HsCustomer {$hsCustomer->getId()}");
				$totalAgreedSum = array_reduce(
					$hsCustomer->getCustomer()->getProjects()->toArray(),
					function ($carry, Project $project) {
						return $carry + $project->getTotalAgreed();
					},
					0
				);
				$totalAgreedSum = round($totalAgreedSum, 2);
				$dataToUpdate = [
					'count_of_lifetime_projects' => $hsCustomer->getCustomer()->getNumberOfProjects(),
					'lifetime_spendings' => $totalAgreedSum,
				];
				if ($hsCustomer->getCustomer()->getLastProjectDate()) {
					$dataToUpdate['last_project_date'] = $hsCustomer->getCustomer()->getLastProjectDate()?->format('Y-m-d');
				}
				if ($hsCustomer->getCustomer()->getFirstProjectDate()) {
					$dataToUpdate['first_project_date'] = $hsCustomer->getCustomer()->getFirstProjectDate()?->format('Y-m-d');
				}
				$processResponse = $this->hsConnector->updateWithCurl($hsCustomer->getHsCustomerId(), ['properties' => $dataToUpdate]);
				if (null === $processResponse) {
					$this->loggerSrv->addWarning("Unable to UPDATE REMOTE hubspot entity with ID {$hsCustomer->getHsCustomerId()}.");

					return false;
				}

				return true;
			default:
				return false;
		}

		$properties = $objectData['properties'];
		$customer = $owner = null;

		if (empty($properties['xtrf_id'])) {
			$this->loggerSrv->addWarning("HsCustomer(Company) has empty xtrf_id for ID=>{$objectData['id']}. Continuing.");
		} else {
			$customer = $this->customerRepository->find($properties['xtrf_id']);

			if (!$customer || !$customer instanceof Customer) {
				$this->loggerSrv->addWarning("Customer not found in our DB for Hubspot xtrf_id =>{$properties['xtrf_id']}. Added Company but with null customer. Continuing");
			}
		}

		if (empty($properties['hubspot_owner_id'])) {
			$this->loggerSrv->addWarning("Company with ID=>{$objectData['id']} does not have value in hubspot_owner_id. Continuing.");
		} else {
			$owner = $this->getOwnerLinkedId($properties['hubspot_owner_id']);
			if ($owner) {
				$ownerId = $owner->getId();
				$owner = $this->userRepository->find($ownerId);
			}
		}

		$lastModificationDate = !empty($properties['hs_lastmodifieddate']) ? new \DateTime($properties['hs_lastmodifieddate']) : null;
		$createdDate = empty($properties['createdate']) ? null : new \DateTime($properties['createdate']);
		$lastActivityDate = empty($properties['notes_last_updated']) ? null : new \DateTime($properties['notes_last_updated']);
		$firstDealCreatedDate = empty($properties['first_deal_created_date']) ? null : new \DateTime($properties['first_deal_created_date']);
		$firstConversionDate = empty($properties['first_conversion_date']) ? null : new \DateTime($properties['first_conversion_date']);
		$firstVisit = empty($properties['hs_analytics_first_visit_timestamp']) ? null : new \DateTime($properties['hs_analytics_first_visit_timestamp']);
		$lastVisit = empty($properties['hs_analytics_last_visit_timestamp']) ? null : new \DateTime($properties['hs_analytics_last_visit_timestamp']);
		$closeDate = empty($properties['close_date']) ? null : new \DateTime($properties['close_date']);
		$recentDealCloseDate = empty($properties['recent_deal_close_date']) ? null : new \DateTime($properties['recent_deal_close_date']);
		$notesLastContacted = empty($properties['notes_last_contacted']) ? null : new \DateTime($properties['notes_last_contacted']);
		$referralDate = empty($properties['referral_date']) ? null : new \DateTime($properties['referral_date']);

		$openDeals = empty($properties['hs_num_open_deals']) ? 0 : $properties['hs_num_open_deals'];
		$contacted = empty($properties['num_contacted_notes']) ? 0 : $properties['num_contacted_notes'];
		$visits = empty($properties['visits']) ? 0 : $properties['visits'];
		$formSubmissions = empty($properties['num_conversion_events']) ? 0 : $properties['num_conversion_events'];

		$totalDealsValue = empty($properties['hs_total_deal_value']) ? 0.0 : $properties['hs_total_deal_value'];
		$likelihoodToClose = empty($properties['hs_predictivecontactscore_v2']) ? 0.0 : $properties['hs_predictivecontactscore_v2'];
		$hsParentCustomer = null;
		if (!empty($properties['hs_parent_company_id'])) {
			$hsParentCustomer = $this->em->getRepository(HsCustomer::class)->find($properties['hs_parent_company_id']);
		}

		$name = empty($properties['name']) ? '-' : $properties['name'];
		// End Workaround

		$hsCustomer
			->setHsCustomerId($objectData['id'])
			->setCustomer($customer)
			->setOwner($owner)
			->setName($name)
			->setLastModificationDate($lastModificationDate)
			->setCreatedDate($createdDate)
			->setLastActivityDate($lastActivityDate)
			->setFirstDealCreatedDate($firstDealCreatedDate)
			->setFirstConversionDate($firstConversionDate)
			->setIndustry($properties['industry'] ?? null)
			->setLifecicleStage($properties['lifecyclestage'] ?? null)
			->setLikelihoodToClose($likelihoodToClose)
			->setOpenDeals($openDeals)
			->setContacted($contacted)
			->setCity($properties['city'] ?? null)
			->setState($properties['state'] ?? null)
			->setCountry($properties['country'] ?? null)
			->setTotalDealValue($totalDealsValue)
			->setType($properties['type'] ?? null)
			->setVisits($visits)
			->setSourceData($properties['hs_analytics_source_data_1'] ?? null)
			->setSourceType($properties['hs_analytics_source'] ?? null)
			->setFirstVisit($firstVisit)
			->setLastVisit($lastVisit)
			->setAcquisitionType($properties['acquisition_type'] ?? null)
			->setSaleType($properties['sale_type'] ?? null)
			->setDivision($properties['division__c'] ?? null)
			->setCloseDate($closeDate)
			->setRecentDealCloseDate($recentDealCloseDate)
			->setCurrentAccountComStatus($properties['current_account_com_status'] ?? null)
			->setDaysToClose($properties['days_to_close'] ?? null)
			->setLeadStatus($properties['hs_lead_status'] ?? null)
			->setLastEngagementDate($properties['hs_last_sales_activity_timestamp'] ?? null)
			->setLastContactedDate($notesLastContacted)
			->setHsParentCustomer($hsParentCustomer)
			->setReferralDate($referralDate)
			->setReferralType($properties['referral_type'] ?? null)
			->setReferredBy($properties['referred_by'] ?? null)
			->setResponsibleForReferral($properties['responsible_for_referral'] ?? null)
			->setFormSubmissions($formSubmissions);

		if (!$this->em->isOpen()) {
			$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
		}
		if (isset($exists) && !$exists) {
			$unitOfWork = $this->em->getUnitOfWork();
			$entities = $unitOfWork->getIdentityMap()[get_class($hsCustomer)] ?? [];

			foreach ($entities as $entity) {
				$this->em->detach($entity);
			}
		}
		if (!$this->em->contains($hsCustomer)) {
			$this->em->detach($hsCustomer);
			$this->em->persist($hsCustomer);
		}
		$this->em->flush();

		return true;
	}

	public function processContacts(array $objectData, string $operation): bool
	{
		if (empty($objectData['id'])) {
			$this->loggerSrv->addWarning('Object has empty ID, unable to continue.');

			return false;
		}

		$hsContact = $this->em->getRepository(HsContactPerson::class)->findOneBy(['hsContactPerson' => $objectData['id']]);

		switch ($operation) {
			case self::OPERATION_CREATE:
				if (null !== $hsContact) {
					$this->loggerSrv->addWarning('Operation is CREATE but entity already exists.');

					return true;
				}
				$hsContact = new HsContactPerson();
				break;
			case self::OPERATION_UPDATE:
				if (null === $hsContact) {
					$dealObjRemote = $this->hsConnector->findById(HubspotQueueService::ENTITY_NAME_CONTACTS, $objectData['id']);
					if (null === $dealObjRemote) {
						$this->loggerSrv->addWarning('Operation is UPDATE but entity does not exists on DB neither on hubspot server.');

						return false;
					}
					$objectData = json_decode($dealObjRemote->toHeaderValue(), true);
					$hsContact = new HsContactPerson();
				}
				break;
			case self::OPERATION_DELETE:
				if (null === $hsContact) {
					$this->loggerSrv->addWarning("Operation is $operation but entity does not exists.");

					return false;
				} else {
					$this->em->remove($hsContact);
					$this->em->flush();

					return true;
				}
				// no break
			case self::OPERATION_CREATE_OR_UPDATE:
				if (null === $hsContact) {
					$hsContact = new HsContactPerson();
				}
				break;
			default:
				return false;
		}

		$properties = $objectData['properties'];
		$contactPerson = $owner = null;

		if (empty($properties['xtrf_id'])) {
			$this->loggerSrv->addWarning("Contact has empty xtrf_id for ID=>{$objectData['id']}. Continuing.");
		} else {
			$contactPerson = $this->contactPersonRepository->find($properties['xtrf_id']);

			if (!$contactPerson || !$contactPerson instanceof ContactPerson) {
				$this->loggerSrv->addWarning("Contact Person not found in our DB for Hubspot xtrf_id =>{$properties['xtrf_id']}. Added HsContact but with null contact person. Continuing");
			}
		}

		if (empty($properties['hubspot_owner_id'])) {
			$this->loggerSrv->addWarning("Contact with ID=>{$objectData['id']} does not have value in hubspot_owner_id. Continuing.");
		} else {
			$owner = $this->getOwnerLinkedId($properties['hubspot_owner_id']);
			if ($owner) {
				$this->em->refresh($owner);
			}
		}

		$lastModifcationDate = !empty($properties['lastmodifieddate']) ? new \DateTime($properties['lastmodifieddate']) : null;
		$createdDate = !empty($properties['createdate']) ? new \DateTime($properties['createdate']) : null;
		$lastActivityDate = !empty($properties['notes_last_updated']) ? new \DateTime($properties['notes_last_updated']) : null;
		$firstDealCreatedDate = !empty($properties['first_deal_created_date']) ? new \DateTime($properties['first_deal_created_date']) : null;
		$firstConversionDate = !empty($properties['first_conversion_date']) ? new \DateTime($properties['first_conversion_date']) : null;
		$becameCustomerDate = !empty($properties['hs_lifecyclestage_customer_date']) ? new \DateTime($properties['hs_lifecyclestage_customer_date']) : null;
		$becameLeadDate = !empty($properties['hs_lifecyclestage_lead_date']) ? new \DateTime($properties['hs_lifecyclestage_lead_date']) : null;
		$becameMarketingLeadDate = !empty($properties['hs_lifecyclestage_marketingqualifiedlead_date']) ? new \DateTime($properties['hs_lifecyclestage_marketingqualifiedlead_date']) : null;
		$becameSalesLeadDate = !empty($properties['hs_lifecyclestage_salesqualifiedlead_date']) ? new \DateTime($properties['hs_lifecyclestage_salesqualifiedlead_date']) : null;
		$becameSubscriberDate = !empty($properties['hs_lifecyclestage_subscriber_date']) ? new \DateTime($properties['hs_lifecyclestage_subscriber_date']) : null;
		$becameOpportunityDate = !empty($properties['hs_lifecyclestage_opportunity_date']) ? new \DateTime($properties['hs_lifecyclestage_opportunity_date']) : null;

		$lastSalesEmailLastOpenedDate = !empty($properties['hs_sales_email_last_opened']) ? new \DateTime($properties['hs_sales_email_last_opened']) : null;
		$lastSalesEmailLastRepliedDate = !empty($properties['hs_sales_email_last_replied']) ? new \DateTime($properties['hs_sales_email_last_replied']) : null;
		$lastSalesEmailLastClickedDate = !empty($properties['hs_sales_email_last_clicked']) ? new \DateTime($properties['hs_sales_email_last_clicked']) : null;
		$emailFirstClickDate = !empty($properties['hs_email_first_click_date']) ? new \DateTime($properties['hs_email_first_click_date']) : null;
		$emailFirstOpenDate = !empty($properties['hs_email_first_open_date']) ? new \DateTime($properties['hs_email_first_open_date']) : null;
		$emailFirstSendDate = !empty($properties['hs_email_first_send_date']) ? new \DateTime($properties['hs_email_first_send_date']) : null;
		$emailLastClickDate = !empty($properties['hs_email_last_click_date']) ? new \DateTime($properties['hs_email_last_click_date']) : null;
		$emailLastOpenDate = !empty($properties['hs_email_last_open_date']) ? new \DateTime($properties['hs_email_last_open_date']) : null;
		$emailLastSendDate = !empty($properties['hs_email_first_send_date']) ? new \DateTime($properties['hs_email_first_send_date']) : null;

		$salesActivities = !empty($properties['num_notes']) ? $properties['num_notes'] : null;
		$facebookClicks = !empty($properties['hs_social_facebook_clicks']) ? $properties['hs_social_facebook_clicks'] : null;
		$twitterClicks = !empty($properties['hs_social_twitter_clicks']) ? $properties['hs_social_twitter_clicks'] : null;
		$linkedinClicks = !empty($properties['hs_social_linkedin_clicks']) ? $properties['hs_social_linkedin_clicks'] : null;
		$emailClicks = !empty($properties['hs_email_click']) ? $properties['hs_email_click'] : null;
		$emailDelivered = !empty($properties['hs_email_delivered']) ? $properties['hs_email_delivered'] : null;
		$emailOpened = !empty($properties['hs_email_open']) ? $properties['hs_email_open'] : null;
		$emailSendsSinceLastEngagement = !empty($properties['hs_email_sends_since_last_engagement']) ? $properties['hs_email_sends_since_last_engagement'] : null;
		$visits = !empty($properties['hs_analytics_num_visits']) ? $properties['hs_analytics_num_visits'] : null;
		$hsCustomerId = !empty($properties['associatedcompanyid']) ? $properties['associatedcompanyid'] : null;
		$lifecyclestageOtherDate = !empty($properties['lifecyclestage_other_date']) ? new \DateTime($properties['lifecyclestage_other_date']) : null;
		$notesLastContacted = !empty($properties['notes_last_contacted']) ? new \DateTime($properties['notes_last_contacted']) : null;

		$hsContact
			->setHsContactPerson($objectData['id'])
			->setContactPerson($contactPerson)
			->setHsCustomerId($hsCustomerId)
			->setOwner($owner)
			->setLastModificationDate($lastModifcationDate)
			->setCreatedDate($createdDate)
			->setLastActivityDate($lastActivityDate)
			->setFirstDealCreatedDate($firstDealCreatedDate)
			->setFirstConversionDate($firstConversionDate)
			->setBecameCustomerDate($becameCustomerDate)
			->setBecameLeadDate($becameLeadDate)
			->setBecameMarketingLeadDate($becameMarketingLeadDate)
			->setBecameSalesLeadDate($becameSalesLeadDate)
			->setBecameSubscriberDate($becameSubscriberDate)
			->setBecameOpportunityDate($becameOpportunityDate)
			->setBuyingRole($properties['hs_buying_role'] ?? null)
			->setCity($properties['city'] ?? null)
			->setState($properties['state'] ?? null)
			->setCountry($properties['country'] ?? null)
			->setFirstName($properties['firstname'] ?? null)
			->setLastName($properties['lastname'] ?? null)
			->setIndustry($properties['industry'] ?? null)
			->setJobTitle($properties['jobtitle'] ?? null)
			->setLeadSourceEvent($properties['lead_source_event'] ?? null)
			->setLifecicleStage($properties['lifecyclestage'] ?? null)
			->setSalesActivities($salesActivities)
			->setPersona($properties['hs_persona'] ?? null)
			->setLastSalesEmailLastOpenedDate($lastSalesEmailLastOpenedDate)
			->setLastSalesEmailLastRepliedDate($lastSalesEmailLastRepliedDate)
			->setLastSalesEmailLastClickedDate($lastSalesEmailLastClickedDate)
			->setSubscriberToNewsletter($properties['subscribe_to_newsletter'] ?? null)
			->setFacebookClicks($facebookClicks)
			->setTwitterClicks($twitterClicks)
			->setLinkedinClicks($linkedinClicks)
			->setEmailFirstClickDate($emailFirstClickDate)
			->setEmailFirstOpenDate($emailFirstOpenDate)
			->setEmailFirstSendDate($emailFirstSendDate)
			->setEmailLastClickDate($emailLastClickDate)
			->setEmailLastOpenDate($emailLastOpenDate)
			->setEmailLastSendDate($emailLastSendDate)
			->setEmailClicks($emailClicks)
			->setEmailDelivered($emailDelivered)
			->setEmailOpened($emailOpened)
			->setEmailSendsSinceLastEngagement($emailSendsSinceLastEngagement)
			->setFirstReferrerSite($properties['hs_analytics_first_referrer'] ?? null)
			->setFirstUrl($properties['hs_analytics_first_url'] ?? null)
			->setVisits($visits)
			->setSource($properties['hs_analytics_source'] ?? null)
			->setFirstConversion($properties['first_conversion_event_name'] ?? null)
			->setLeadSource($properties['leadsource'] ?? null)
			->setReferrerFirstName($properties['referrer_first_name__c'] ?? null)
			->setDivision($properties['division__c'] ?? null)
			->setLifecyclestageOtherDate($lifecyclestageOtherDate)
			->setChildCompany($properties['child_company'] ?? null)
			->setCompany($properties['company'] ?? null)
			->setLastContactedDate($notesLastContacted)
			->setLastEngagementDate($properties['hs_last_sales_activity_timestamp'] ?? null)
			->setLeadStatus($properties['hs_lead_status'] ?? null)
			->setMqlScore($properties['hubspotscore'] ?? null)
			->setNumSequencesEnrolled($properties['hs_sequences_enrolled_count'] ?? null)
			->setNumTimesContacted($properties['num_contacted_notes'] ?? null)
			->setReference($properties['reference'] ?? null)
			->setWillingToBeAReference($properties['willing_to_be_a_reference'] ?? null)
			->setNumFormSubmissions($properties['num_conversion_events'] ?? null)
			->setReferrerLastName($properties['referrer_last_name__c'] ?? null);

		if (!$this->em->isOpen()) {
			$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
		}
		if (!$this->em->contains($hsContact)) {
			$this->em->detach($hsContact);
			$this->em->persist($hsContact);
		}
		$this->em->flush();

		return true;
	}

	public function processDeals(array $objectData, string $operation): bool
	{
		if (empty($objectData['id'])) {
			$this->loggerSrv->addWarning('Object has empty ID, unable to continue.');

			return false;
		}

		$hsDeal = $this->em->getRepository(HsDeal::class)->findOneBy(['hsDealId' => $objectData['id']]);
		switch ($operation) {
			case self::OPERATION_CREATE:
				if (null !== $hsDeal) {
					$this->loggerSrv->addWarning('Operation is CREATE but entity already exists.');

					return true;
				}
				$hsDeal = new HsDeal();
				break;
			case self::OPERATION_UPDATE:
				if (null === $hsDeal) {
					$dealObjRemote = $this->hsConnector->findById(HubspotQueueService::ENTITY_NAME_DEALS, $objectData['id']);
					if (null === $dealObjRemote) {
						$this->loggerSrv->addWarning('Operation is UPDATE but entity does not exists on DB neither on hubspot server.');

						return false;
					}
					$objectData = json_decode($dealObjRemote->toHeaderValue(), true);
					$hsDeal = new HsDeal();
				}
				break;
			case self::OPERATION_DELETE:
				if (null === $hsDeal) {
					$this->loggerSrv->addWarning("Operation is $operation but entity does not exists.");

					return false;
				} else {
					$this->em->remove($hsDeal);
					$this->em->flush();

					return true;
				}
				// no break
			case self::OPERATION_CREATE_OR_UPDATE:
				if (null === $hsDeal) {
					$hsDeal = new HsDeal();
				}
				break;
			default:
				return false;
		}

		$properties = $objectData['properties'];
		$owner = null;

		if (empty($properties['hubspot_owner_id'])) {
			$this->loggerSrv->addWarning("Deal with ID=>{$objectData['id']} does not have value in hubspot_owner_id. Continuing.");
		} else {
			$owner = $this->getOwnerLinkedId($properties['hubspot_owner_id']);
			if ($owner) {
				$this->em->refresh($owner);
			}
		}

		$amount = !empty($properties['amount']) ? $properties['amount'] : null;
		$estimatedRfpAmount = !empty($properties['estimated_rfp_amount']) ? $properties['estimated_rfp_amount'] : null;
		$closedDate = !empty($properties['closedate']) ? new \DateTime($properties['closedate']) : null;
		$createdDate = !empty($properties['createdate']) ? new \DateTime($properties['createdate']) : null;
		$lastActivityDate = !empty($properties['notes_last_updated']) ? new \DateTime($properties['notes_last_updated']) : null;
		$contractEndingDate = !empty($properties['contract_term_ending_date']) ? new \DateTime($properties['contract_term_ending_date']) : null;
		$salesActivities = !empty($properties['num_notes']) ? $properties['num_notes'] : null;
		$hsCustomer = null;
		$companyAssociations = $objectData['associations']['companies']['results'] ?? [];
		foreach ($companyAssociations as $companyAssociation) {
			if (($companyAssociation['type'] ?? null) === HsDeal::ASSOCIATION_TYPE_COMPANY && array_key_exists('id', $companyAssociation)) {
				$hsCustomer = $this->em->getRepository(HsCustomer::class)->findOneBy(['hsCustomerId' => $companyAssociation['id']]);
				break;
			}
		}

		$hsDeal
			->setHsDealId($objectData['id'])
			->setHsCustomer($hsCustomer)
			->setOwner($owner)
			->setAmount($amount)
			->setClosedDate($closedDate)
			->setCreatedDate($createdDate)
			->setName($properties['dealname'] ?? null)
			->setStage($properties['dealstage'] ?? null)
			->setType($properties['dealtype'] ?? null)
			->setDaysToClose($properties['days_to_close'] ?? null)
			->setHsClosedAmount($properties['hs_closed_amount'] ?? null)
			->setHsDealStageProbability($properties['hs_deal_stage_probability'] ?? null)
			->setHsForecastAmount($properties['hs_forecast_amount'] ?? null)
			->setHsIsClosed($properties['hs_is_closed'] ?? null)
			->setHsIsClosedWon($properties['hs_is_closed_won'] ?? null)
			->setHsLatestMeetingActivity($properties['hs_latest_meeting_activity'] ?? null)
			->setHsNumTargetAccounts($properties['hs_num_target_accounts'] ?? null)
			->setHsProjectedAmount($properties['hs_projected_amount'] ?? null)
			->setHsSalesEmailLastReplied($properties['hs_sales_email_last_replied'] ?? null)
			->setNotesLastContacted($properties['notes_last_contacted'] ?? null)
			->setNumAssociatedContacts($properties['num_associated_contacts'] ?? null)
			->setNumNotes($properties['num_notes'] ?? null)
			->setServicesRequested($properties['services_requested'] ?? null)
			->setCopiesOfAllBidsReceived($properties['copies_of_all_bids_received'] ?? null)
			->setGoNoGoScore($properties['go_no_go_score'] ?? null)
			->setHsAcv($properties['hs_acv'] ?? null)
			->setHsAnalyticsSource($properties['hs_analytics_source'] ?? null)
			->setHsAnalyticsSourceData1($properties['hs_analytics_source_data_1'] ?? null)
			->setHsAnalyticsSourceData2($properties['hs_analytics_source_data_2'] ?? null)
			->setHsCampaign($properties['hs_campaign'] ?? null)
			->setHsDealAmountCalculationPreference($properties['hs_deal_amount_calculation_preference'] ?? null)
			->setHsDealStageProbabilityShadow($properties['hs_deal_stage_probability_shadow'] ?? null)
			->setHsForecastProbability($properties['hs_forecast_probability'] ?? null)
			->setHsLikelihoodToClose($properties['hs_likelihood_to_close'] ?? null)
			->setHsManualForecastCategory($properties['hs_manual_forecast_category'] ?? null)
			->setHsMrr($properties['hs_mrr'] ?? null)
			->setHsNextStep($properties['hs_next_step'] ?? null)
			->setHsNumAssociatedDealSplits($properties['hs_num_associated_deal_splits'] ?? null)
			->setHsPredictedAmount($properties['hs_predicted_amount'] ?? null)
			->setHsPriority($properties['hs_priority'] ?? null)
			->setHsTcv($properties['hs_tcv'] ?? null)
			->setReasonForNoBid($properties['reason_for_no_bid'] ?? null)
			->setSuccessfulBidder($properties['successful_bidder'] ?? null)
			->setEngagementsLastMeetingBooked($properties['engagements_last_meeting_booked'] ?? null)
			->setEngagementsLastMeetingBookedMedium($properties['engagements_last_meeting_booked_medium'] ?? null)
			->setIdQuotes($properties['id_quotes'] ?? null)
			->setClosedWonReason($properties['closed_won_reason'] ?? null)
			->setAnnualContractAmount($properties['annual_contract_amount'] ?? null)
			->setExcitedforThisBid($properties['are_we_excited_for_this_bid_'] ?? null)
			->setContractTermEndingDate($contractEndingDate)
			->setGoHsGenerated($properties['go__no_go'] ?? null)
			->setGoScore($properties['go_no_go_score'] ?? null)
			->setInitialCallerClientServicesMember($properties['initial_caller_client_services_member'] ?? null)
			->setNumTimesContacted($properties['num_contacted_notes'] ?? null)
			->setOpportunityRatio($properties['opportunity_ratio'] ?? null)
			->setEstimatedRfpAmount($estimatedRfpAmount)
			->setIndustry($properties['industry'] ?? null)
			->setLastActivityDate($lastActivityDate)
			->setSalesActivities($salesActivities)
			->setPipelineId($properties['pipeline'] ?? null)
			->setReasonDealLost($properties['closed_lost_reason'] ?? null);

		if (!$this->em->isOpen()) {
			$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
		}
		if (!$this->em->contains($hsDeal)) {
			$this->em->detach($hsDeal);
			$this->em->persist($hsDeal);
		}
		$this->em->flush();

		return true;
	}

	public function processMarketingEmail(array $objectData, string $operation): bool
	{
		if (empty($objectData['id'])) {
			$this->loggerSrv->addWarning('Object has empty ID, unable to continue.');

			return false;
		}

		$marketingEmail = $this->em->getRepository(HsMarketingEmail::class)->findOneBy(['hsMarketingEmail' => $objectData['id']]);

		switch ($operation) {
			case self::OPERATION_CREATE:
				if (null !== $marketingEmail) {
					$this->loggerSrv->addWarning('Operation is CREATE but entity already exists.');

					return false;
				}
				$marketingEmail = new HsMarketingEmail();
				break;
			case self::OPERATION_UPDATE:
				if (null === $marketingEmail) {
					$this->loggerSrv->addWarning('Operation is UPDATE but entity does not exists.');

					return false;
				}
				break;
			case self::OPERATION_DELETE:
				if (null === $marketingEmail) {
					$this->loggerSrv->addWarning("Operation is $operation but entity does not exists.");

					return false;
				} else {
					$this->em->remove($marketingEmail);
					$this->em->flush();

					return true;
				}
				// no break
			case self::OPERATION_CREATE_OR_UPDATE:
				if (null === $marketingEmail) {
					$marketingEmail = new HsMarketingEmail();
				}
				break;
			default:
				return false;
		}

		$updatedDate = !empty($objectData['updated']) ? (new \DateTime())->setTimestamp($objectData['updated'] / 1000) : null;
		$publishDate = !empty($objectData['publishDate']) ? (new \DateTime())->setTimestamp($objectData['publishDate'] / 1000) : null;
		$createdDate = !empty($objectData['created']) ? (new \DateTime())->setTimestamp($objectData['created'] / 1000) : null;
		$sentCount = $openCount = $deliveredCount = $bounceCount = $clickCount = 0;
		$unsubscribedRatio = $clickRatio = $bounceRatio = $deliveredRatio = $deliveredRatio = $openRatio = $unsubscriberCount = $optInOut = 0.0;

		if (isset($objectData['stats'])) {
			if (isset($objectData['stats']['ratios'])) {
				$clickRatio = !empty($objectData['stats']['ratios']['unsubscribedratio']) ? $objectData['stats']['ratios']['unsubscribedratio'] : 0.0;
				$unsubscribedRatio = !empty($objectData['stats']['ratios']['unsubscribed']) ? $objectData['stats']['ratios']['unsubscribed'] : 0.0;
				$bounceRatio = !empty($objectData['stats']['ratios']['bounceratio']) ? $objectData['stats']['ratios']['bounceratio'] : 0.0;
				$deliveredRatio = !empty($objectData['stats']['ratios']['deliveredratio']) ? $objectData['stats']['ratios']['deliveredratio'] : 0.0;
				$openRatio = !empty($objectData['stats']['ratios']['openratio']) ? $objectData['stats']['ratios']['openratio'] : 0.0;
			}
			if (isset($objectData['stats']['counters'])) {
				$clickCount = !empty($objectData['stats']['counters']['click']) ? $objectData['stats']['counters']['click'] : 0;
				$unsubscriberCount = !empty($objectData['stats']['counters']['unsubscribed']) ? $objectData['stats']['counters']['unsubscribed'] : 0;
				$bounceCount = !empty($objectData['stats']['counters']['bounce']) ? $objectData['stats']['counters']['bounce'] : 0;
				$deliveredCount = !empty($objectData['stats']['counters']['delivered']) ? $objectData['stats']['counters']['delivered'] : 0;
				$openCount = !empty($objectData['stats']['counters']['open']) ? $objectData['stats']['counters']['open'] : 0;
				$sentCount = !empty($objectData['stats']['counters']['sent']) ? $objectData['stats']['counters']['sent'] : 0;
			}
		}

		$optInOut = !empty($objectData['opt_in_out']) ? $objectData['opt_in_out'] : 0;
		$successfulDelivery = !empty($objectData['successful_delivery']) ? $objectData['successful_delivery'] : 0;

		$marketingEmail
			->setHsMarketingEmail($objectData['id'])
			->setName($objectData['name'] ?? null)
			->setSuccessfulDelivery($successfulDelivery)
			->setOptInOut($optInOut)
			->setArchived($objectData['archived'] ?? null)
			->setCreatedDate($createdDate)
			->setPublishDate($publishDate)
			->setUpdatedDate($updatedDate)
			->setSentCount($sentCount)
			->setOpenCount($openCount)
			->setDeliveredCount($deliveredCount)
			->setBounceCount($bounceCount)
			->setUnsubscriberCount($unsubscriberCount)
			->setClickCount($clickCount)
			->setOpenRatio($openRatio)
			->setDeliveredRatio($deliveredRatio)
			->setBounceRatio($bounceRatio)
			->setUnsubscribedRatio($unsubscribedRatio)
			->setClickRatio($clickRatio);

		if (!$this->em->isOpen()) {
			$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
		}
		if (!$this->em->contains($marketingEmail)) {
			$this->em->detach($marketingEmail);
			$this->em->persist($marketingEmail);
		}
		$this->em->flush();

		return true;
	}

	public function processPipelines(array $objectData, string $operation): bool
	{
		if (empty($objectData['id'])) {
			$this->loggerSrv->addWarning('Object Pipeline has empty ID, unable to continue.');

			return false;
		}

		$hsPipeline = $this->em->getRepository(HsPipeline::class)->findOneBy(['hsId' => $objectData['id']]);

		switch ($operation) {
			case self::OPERATION_CREATE:
				if (null !== $hsPipeline) {
					$this->loggerSrv->addWarning('Operation is CREATE but entity Pipeline already exists.');

					return true;
				}
				break;
			case self::OPERATION_UPDATE:
				if (null === $hsPipeline) {
					$this->loggerSrv->addWarning('Operation is UPDATE but entity Pipeline does not exists.');

					return false;
				}
				break;
			case self::OPERATION_DELETE:
				if (null === $hsPipeline) {
					$this->loggerSrv->addWarning("Operation is $operation but entity Pipeline does not exists.");

					return false;
				} else {
					$this->em->remove($hsPipeline);
					$this->em->flush();

					return true;
				}
				// no break
			case self::OPERATION_CREATE_OR_UPDATE:
				if (null === $hsPipeline) {
					$hsPipeline = new HsPipeline();
				}
				break;
			default:
				return false;
		}

		$updatedAt = !empty($objectData['updatedAt']) ? new \DateTime($objectData['updatedAt']) : null;
		$createdAt = !empty($objectData['createdAt']) ? new \DateTime($objectData['createdAt']) : null;

		$hsPipeline
			->setHsId($objectData['id'])
			->setLabel($objectData['label'])
			->setDisplayOrder($objectData['displayOrder'])
			->setArchived($objectData['archived'])
			->setUpdatedAt($updatedAt)
			->setCreatedAt($createdAt);

		foreach ($objectData['stages'] as $stage) {
			$stageObj = $this->em->getRepository(HsPipelineStage::class)->findOneBy(['hsId' => $stage['id']]);
			if (!$stageObj) {
				$stageObj = new HsPipelineStage();
				$stageObj->setHsId($stage['id']);
			}

			$updatedAt = !empty($stage['updatedAt']) ? new \DateTime($stage['updatedAt']) : null;
			$createdAt = !empty($stage['createdAt']) ? new \DateTime($stage['createdAt']) : null;

			$stageObj
				->setLabel($stage['label'])
				->setDisplayOrder($stage['displayOrder'])
				->setArchived($stage['archived'])
				->setUpdatedAt($updatedAt)
				->setCreatedAt($createdAt)
				->setMetadata($stage['metadata']);

			$hsPipeline->addStage($stageObj);
		}

		if (!$this->em->isOpen()) {
			$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
		}
		if (!$this->em->contains($hsPipeline)) {
			$this->em->detach($hsPipeline);
			$this->em->persist($hsPipeline);
		}
		$this->em->flush();

		return true;
	}

	public function processEngagements(array $objectData, string $operation): bool
	{
		if (empty($objectData['engagement']['id'])) {
			$this->loggerSrv->addWarning('Object Engagement has empty ID, unable to continue.');

			return false;
		}

		$hsEngagement = $this->em->getRepository(HsEngagement::class)->findOneBy(['hsId' => $objectData['engagement']['id']]);

		switch ($operation) {
			case self::OPERATION_CREATE:
				if (null !== $hsEngagement) {
					$this->loggerSrv->addWarning('Operation is CREATE but entity Engagement already exists.');

					return true;
				}
				$hsEngagement = new HsEngagement();
				break;
			case self::OPERATION_UPDATE:
				if (null === $hsEngagement) {
					$this->loggerSrv->addWarning("Operation is $operation but entity Engagement does not exists.");

					return false;
				}
				break;
			case self::OPERATION_DELETE:
				if (null === $hsEngagement) {
					$this->loggerSrv->addWarning("Operation is $operation but entity Engagement does not exists.");

					return false;
				} else {
					$this->em->remove($hsEngagement);
					$this->em->flush();

					return true;
				}
				// no break
			case self::OPERATION_CREATE_OR_UPDATE:
				if (null === $hsEngagement) {
					$hsEngagement = new HsEngagement();
				}
				break;
			default:
				return false;
		}

		$eng = $objectData['engagement'];
		$owner = null;

		if (empty($eng['ownerId'])) {
			$this->loggerSrv->addWarning("Company with ID=>{$eng['id']} does not have value in ownerId. Continuing.");
		} else {
			$owner = $this->getOwnerLinkedId($eng['ownerId']);
			if ($owner) {
				$this->em->refresh($owner);
			}
		}

		$createdAt = !empty($eng['createdAt']) ? (new \DateTime())->setTimestamp($eng['createdAt'] / 1000) : null;
		$lastUpdated = !empty($eng['lastUpdated']) ? (new \DateTime())->setTimestamp($eng['lastUpdated'] / 1000) : null;
		$timestamp = intval($eng['timestamp']);
		$createdBy = null;
		if (!empty($eng['createdBy'])) {
			$createdBy = $this->userRepository->find($eng['createdBy']);
		}
		$modifiedBy = null;
		if (!empty($eng['modifiedBy'])) {
			$modifiedBy = $this->userRepository->find($eng['modifiedBy']);
		}

		$hsEngagement
			->setHsId($eng['id'])
			->setOwner($owner)
			->setCreatedBy($createdBy)
			->setModifiedBy($modifiedBy)
			->setPortalId($eng['portalId'])
			->setActive($eng['id'])
			->setType($eng['type'])
			->setTimestamp($timestamp)
			->setCreatedAt($createdAt)
			->setLastUpdated($lastUpdated)
			->setMetadata($objectData['metadata'])
			->setScheduledTasks($objectData['scheduledTasks'] ?? null);

		foreach ($objectData['associations'] as $key => $assoc) {
			foreach ($assoc as $id) {
				list($searchField, $setFunction, $setObj) = match ($key) {
					'contactIds' => [
						'hsContact',
						'setHsContact',
						$this->em->getRepository(HsContactPerson::class)->findOneBy(['hsContactPerson' => $id]),
					],
					'companyIds' => [
						'hsCompany',
						'setHsCompany',
						$this->em->getRepository(HsCustomer::class)->findOneBy(['hsCustomerId' => $id]),
					],
					'dealIds' => [
						'hsDeal',
						'setHsDeal',
						$this->em->getRepository(HsDeal::class)->findOneBy(['hsDealId' => $id]),
					],
					'ownerIds' => [
						'owner',
						'setOwner',
						$this->userRepository->findOneBy(['hsOwner' => $id]),
					],
					'workflowIds' => [
						'workflow',
						'setWorkflow',
						$this->workflowRepo->find($id),
					],
				};
				$stageAssocObj = $this->em->getRepository(HsEngagementAssoc::class)->findOneBy(['hsEngagement' => $hsEngagement->getId(), $searchField => $id]);

				if (!$stageAssocObj) {
					$stageAssocObj = new HsEngagementAssoc();
					$stageAssocObj->setHsEngagement($hsEngagement);
					$hsEngagement->addAssociation($stageAssocObj);
				}

				if ($stageAssocObj) {
					$stageAssocObj->$setFunction($setObj);
					$this->em->persist($stageAssocObj);
				}
			}
		}
		if (!$this->em->isOpen()) {
			$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
		}

		if (!$this->em->contains($hsEngagement)) {
			$this->em->detach($hsEngagement);
			$this->em->persist($hsEngagement);
		}
		$this->em->flush();

		return true;
	}

	private function getOwnerLinkedId(string $hsOwnerId): ?InternalUser
	{
		/** @var InternalUser $user */
		$user = $this->userRepository->findOneBy(['hsOwner' => $hsOwnerId]);
		if (!$user) {
			$this->loggerSrv->addWarning("User not found in InternalUser for ID $hsOwnerId. It will be added with NULL value.");

			return null;
		}

		return $user;
	}
}
