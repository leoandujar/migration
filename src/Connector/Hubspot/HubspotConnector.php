<?php

namespace App\Connector\Hubspot;

use App\Connector\Hubspot\Request\Companies\UpdateCompanyRequest;
use HubSpot\Factory;
use HubSpot\Crm\ObjectType;
use App\Service\LoggerService;
use GuzzleHttp\RequestOptions;
use App\Model\Entity\HsPipeline;
use HubSpot\Discovery\Discovery;
use SevenShores\Hubspot\Http\Client;
use GuzzleHttp\Client as GuzzleClient;
use SevenShores\Hubspot\Http\Response;
use App\Connector\Hubspot\Response\Response as ConnectorResponse;
use App\Connector\Hubspot\Request\Request;
use HubSpot\Client\Crm\Objects\ApiException;
use HubSpot\Client\Crm\Objects\Model\CollectionResponseSimplePublicObjectWithAssociationsForwardPaging;
use HubSpot\Client\Crm\Objects\Model\SimplePublicObjectWithAssociations;
use App\Connector\Hubspot\Response\MarketingEmail\GetMarketingEmailResponse;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Connector\Hubspot\Request\MarketingEmail\GetMarketingEmailListRequest;
use HubSpot\Client\Crm\Pipelines\Model\CollectionResponsePipelineNoPaging;

class HubspotConnector
{
	public const COMPANIES_PROPERTIES = ['hubspot_owner_id', 'hs_lastmodifieddate', 'createdate', 'notes_last_updated', 'first_deal_created_date', 'first_conversion_date', 'industry', 'lifecyclestage', 'hs_predictivecontactscore_v2', 'name', 'hs_num_open_deals', 'num_contacted_notes', 'hs_num_open_deals', 'num_contacted_notes', 'city', 'state', 'country', 'hs_total_deal_value', 'type', 'hs_analytics_num_visits', 'hs_analytics_source_data_1', 'hs_analytics_source', 'hs_analytics_first_visit_timestamp', 'hs_analytics_last_visit_timestamp', 'acquisition_type', 'num_conversion_events', 'xtrf_client_account_id__c', 'xtrf_id', 'sale_type', 'division__c', 'closedate', 'recent_deal_close_date', 'current_account_com_status', 'days_to_close', 'hs_lead_status', 'hs_last_sales_activity_timestamp', 'notes_last_contacted', 'hs_parent_company_id', 'referral_date', 'referral_type', 'referred_by', 'responsible_for_referral', 'last_project_date', 'first_project_date', 'count_of_lifetime_projects', 'lifetime_spendings'];
	public const CONTACTS_PROPERTIES = ['hubspot_owner_id', 'xtrf_id', 'associatedcompanyid', 'lastmodifieddate', 'createdate', 'notes_last_updated', 'first_deal_created_date', 'first_conversion_date', 'hs_lifecyclestage_customer_date', 'hs_lifecyclestage_lead_date', 'hs_lifecyclestage_marketingqualifiedlead_date', 'hs_lifecyclestage_salesqualifiedlead_date', 'hs_lifecyclestage_subscriber_date', 'hs_lifecyclestage_opportunity_date', 'hs_buying_role', 'city', 'state', 'country', 'owner_id', 'firstname', 'lastname', 'industry', 'jobtitle', 'lead_source_event', 'lifecyclestage', 'num_notes', 'hs_persona', 'hs_sales_email_last_opened', 'hs_sales_email_last_replied', 'hs_sales_email_last_clicked', 'subscribe_to_newsletter', 'hs_social_facebook_clicks', 'hs_social_twitter_clicks', 'hs_social_linkedin_clicks', 'hs_email_first_click_date', 'hs_email_first_open_date', 'hs_email_first_send_date', 'hs_email_last_click_date', 'hs_email_last_open_date', 'hs_email_last_send_date', 'hs_email_click', 'hs_email_delivered', 'hs_email_open', 'hs_email_sends_since_last_engagement', 'hs_analytics_first_referrer', 'hs_analytics_first_url', 'hs_analytics_num_visits', 'hs_analytics_source', 'first_conversion_event_name', 'leadsource', 'referrer_first_name__c', 'referrer_last_name__c', 'division__c', 'hs_lifecyclestage_other_date', 'child_company', 'company', 'notes_last_contacted', 'hs_last_sales_activity_timestamp', 'hs_lead_status', 'hubspotscore', 'hs_sequences_enrolled_count', 'num_contacted_notes', 'reference', 'willing_to_be_a_reference', 'num_conversion_events'];
	public const DEALS_PROPERTIES = ['hubspot_owner_id', 'amount', 'closedate', 'createdate', 'dealname', 'owner_id', 'dealstage', 'dealtype', 'estimated_rfp_amount', 'industry', 'notes_last_updated', 'num_notes', 'closed_lost_reason', 'pipeline', 'days_to_close', 'hs_closed_amount', 'hs_deal_stage_probability', 'hs_forecast_amount', 'hs_is_closed', 'hs_is_closed_won', 'hs_latest_meeting_activity', 'hs_num_target_accounts', 'hs_projected_amount', 'hs_sales_email_last_replied', 'notes_last_contacted', 'num_associated_contacts', 'num_notes', 'services_requested', 'copies_of_all_bids_received', 'go_no_go_score', 'hs_acv', 'hs_analytics_source', 'hs_analytics_source_data_1', 'hs_analytics_source_data_2', 'hs_campaign', 'hs_deal_amount_calculation_preference', 'hs_deal_stage_probability_shadow', 'hs_forecast_probability', 'hs_likelihood_to_close', 'hs_manual_forecast_category', 'hs_mrr', 'hs_next_step', 'hs_num_associated_deal_splits', 'hs_predicted_amount', 'hs_priority', 'hs_tcv', 'reason_for_no_bid', 'successful_bidder', 'engagements_last_meeting_booked', 'engagements_last_meeting_booked_medium', 'id_quotes', 'closed_won_reason', 'annual_contract_amount', 'are_we_excited_for_this_bid_', 'contract_term_ending_date', 'go__no_go', 'go_no_go_score', 'initial_caller_client_services_member', 'num_contacted_notes', 'opportunity_ratio'];

	private Discovery|Factory $connector;
	private LoggerService $loggerSrv;
	private ?GuzzleClient $client = null;
	private string $url;
	private string $hsAccessToken;

	public function __construct(
		ParameterBagInterface $parameterBag,
		LoggerService $loggerSrv,
	) {
		$this->url = $parameterBag->get('hubspot.api_url');
		$this->hsAccessToken = $parameterBag->get('hubspot.api_access_token');
		$this->connector = Factory::createWithAccessToken($this->hsAccessToken);
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
	}

	/**
	 * @throws \Throwable
	 */
	protected function sendRequest(Request $request, string $responseClass, bool $skipLogs = false): mixed
	{
		try {
			if (Request::TYPE_JSON === $request->getType()) {
				$options['body'] = json_encode($request->getParams());
			} elseif (Request::TYPE_FORM === $request->getType()) {
				$options['form_params'] = $request->getParams();
			} else {
				$options['multipart'] = $request->getParams();
			}
			$options['headers'] = $request->getHeaders();
			$options['headers']['Authorization'] = "Bearer $this->hsAccessToken";
			$options['version'] = 1.1;
			if (null === $this->client) {
				$this->client = new GuzzleClient([
					RequestOptions::CONNECT_TIMEOUT => $request->getTimeout(),
					RequestOptions::READ_TIMEOUT => $request->getTimeout(),
					RequestOptions::TIMEOUT => $request->getTimeout(),
					RequestOptions::HTTP_ERRORS => false,
				]);
			}
			$response = $this->client->request(
				$request->getRequestMethod(),
				"$this->url{$request->getRequestUri()}",
				$options
			);
			$responseString = $response->getBody()->getContents();
			$responseBody = json_decode($responseString, true);
			if (empty($responseBody)) {
				$responseBody = [];
			}
			if (!$skipLogs) {
				$this->loggerSrv->addInfo('Received response: '.$responseString);
			}

			return new $responseClass($response->getStatusCode(), $responseBody);
		} catch (\Throwable $thr) {
			throw $thr;
		}
	}

	public function findAll(string $entityName, int $pageLimit, ?string $after = null, $associations = ''): ?CollectionResponseSimplePublicObjectWithAssociationsForwardPaging
	{
		try {
			$properties = implode(',', $this->getProperties($entityName));

			return $this->connector->crm()->objects()->basicApi()->getPage(
				object_type: $entityName,
				limit: $pageLimit,
				after: $after,
				properties: $properties,
				associations: $associations
			);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in Hubspot FIND_ALL function.', $thr);
			throw $thr;
		}
	}

	public function findAllPipelines(): ?CollectionResponsePipelineNoPaging
	{
		try {
			return $this->connector->crm()->pipelines()->pipelinesApi()->getAll(HsPipeline::TYPE_DEAL);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in Hubspot findAllPipelines function.', $thr);
			throw $thr;
		}
	}

	/**
	 * @return Response
	 *
	 * @throws \Throwable
	 */
	public function findAllEngagement(int $pageLimit, ?string $after = null)
	{
		try {
			$hubspot = new \SevenShores\Hubspot\Factory([
				$config = [
					'key' => $this->hsAccessToken,
					'oauth2' => true,
				],
			], new Client($config));

			return $hubspot->engagements()->all([
				'count' => $pageLimit,
				'offset' => $after,
			]);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in Hubspot findAllEngagement function.', $thr);
			throw $thr;
		}
	}

	/**
	 * @return Response
	 *
	 * @throws \Throwable
	 */
	public function findAllModifiedEngagement(int $pageLimit, ?string $after = null)
	{
		try {
			$hubspot = new \SevenShores\Hubspot\Factory([
				$config = [
					'key' => $this->hsAccessToken,
					'oauth2' => true,
				],
			], new Client($config));

			return $hubspot->engagements()->recent([
				'count' => $pageLimit,
				'offset' => $after,
			]);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in Hubspot findAllModifiedEngagement function.', $thr);
			throw $thr;
		}
	}

	/**
	 * @throws \Throwable
	 * @throws ApiException
	 */
	public function findById(string $entityName, string $id): ?SimplePublicObjectWithAssociations
	{
		try {
			$properties = implode(',', $this->getProperties($entityName));

			return $this->connector->crm()->objects()->basicApi()->getById($entityName, $id, $properties);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error in Hubspot FIND_BY_ID function $entityName for ID=>$id.", $thr);
			throw $thr;
		}
	}

	/**
	 * @throws \Throwable
	 */
	public function update(string $entityName, string $entityId, array $keysToUpdate, array $dataToUpdate): mixed
	{
		try {
			$targetEntity = ucfirst($entityName);
			$targetClass = "\HubSpot\Client\Crm\\$targetEntity\Model\SimplePublicObjectInput";
			$updateObj = (new $targetClass($keysToUpdate))->setProperties($dataToUpdate);

			return $this->connector->crm()->$entityName()->basicApi()->update($entityId, $updateObj);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error in Hubspot UPDATE function $entityName for ID=>$entityId.", $thr);
			throw $thr;
		}
	}

	public function updateCompany(string $entityId, array $data): mixed
	{
		$request = new UpdateCompanyRequest($entityId, $data);
		try {
			return $this->sendRequest($request, ConnectorResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request updateCompany to Hubspot Api', $thr);
		}

		return null;
	}

	public function updateWithCurl($entityId, array $data): bool
	{
		$url = "$this->url/crm/v3/objects/companies/$entityId";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		$d = json_encode($data);
		$this->loggerSrv->addInfo("Payload from updateWithCurl $d");
		curl_setopt($curl, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			"Authorization: Bearer $this->hsAccessToken",
		]);

		$response = curl_exec($curl);
		if (curl_errno($curl)) {
			$this->loggerSrv->addError('Error: sending request updateWithCurl to Hubspot Api=> '.curl_error($curl));

			return false;
		}

		curl_close($curl);
		$this->loggerSrv->addInfo("Response from updateWithCurl $response");

		return true;
	}

	public function getMarketingEmails($limit, $offset): ?GetMarketingEmailResponse
	{
		$request = new GetMarketingEmailListRequest($limit, $offset);
		try {
			return $this->sendRequest($request, GetMarketingEmailResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getMarketingEmails to Hubspot Api', $thr);
		}

		return null;
	}

	private function getProperties(string $entityName): array
	{
		return match ($entityName) {
			ObjectType::COMPANIES => self::COMPANIES_PROPERTIES,
			ObjectType::CONTACTS => self::CONTACTS_PROPERTIES,
			ObjectType::DEALS => self::DEALS_PROPERTIES,
			default => [],
		};
	}
}
