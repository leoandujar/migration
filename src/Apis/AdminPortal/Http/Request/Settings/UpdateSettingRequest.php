<?php

namespace App\Apis\AdminPortal\Http\Request\Settings;

use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Validator\ApiArrayConstraint;
use App\Apis\Shared\Http\Validator\ApiBooleanConstraint;
use App\Apis\Shared\Http\Validator\ApiCollectionConstraint;
use App\Apis\Shared\Http\Validator\ApiCountConstraint;
use App\Apis\Shared\Http\Validator\ApiIntegerConstraint;
use App\Apis\Shared\Http\Validator\ApiStringConstraint;
use App\Apis\Shared\Http\Validator\ApiUrlConstraint;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateSettingRequest extends ApiRequest
{
	#[ApiArrayConstraint]
	#[ApiCountConstraint(
		min: 1,
		max: 1
	)]
	#[ApiCollectionConstraint(
		fields: ['teams_webhook' => new ApiUrlConstraint()]
	)]
	public mixed $general_settings;

	#[ApiCollectionConstraint(
		[
			'features' => new ApiCollectionConstraint(
				fields: [
					'working_files_as_ref_files' => new ApiBooleanConstraint(),
					'update_working_files' => new ApiBooleanConstraint(),
					'confirmation_send_by_default' => new ApiBooleanConstraint(),
					'download_confirmation' => new ApiBooleanConstraint(),
					'duplicate_task' => new ApiBooleanConstraint(),
					'analyze_files' => new ApiBooleanConstraint(),
					'deadline_prediction' => new ApiBooleanConstraint(),
					'deadline_options' => new ApiArrayConstraint(),
					'quick_estimate' => new ApiBooleanConstraint(),
					'auto_start' => new ApiBooleanConstraint(),
					'files_queue' => new ApiStringConstraint(),
					'max_file_size' => new ApiIntegerConstraint(),
					'categories' => new ApiArrayConstraint(),
					'rush_deadline' => new ApiIntegerConstraint(),
					'file_extensions_warning' => new ApiArrayConstraint(),
					'dearchive' => new ApiBooleanConstraint(),
				],
				allowMissingFields: true
			),
			'custom_fields' => new Assert\All([
				new ApiCollectionConstraint(
					fields: [
						'name' => new ApiStringConstraint(),
						'visible' => new ApiBooleanConstraint(),
						'enabled' => new ApiBooleanConstraint(),
						'value' => new ApiStringConstraint(),
					],
					allowMissingFields: true
				),
			]),
		],
		allowMissingFields: true
	)]
	public mixed $projects_settings;

	#[ApiArrayConstraint]
	#[ApiCountConstraint(
		min: 1,
		max: 1
	)]
	#[ApiCollectionConstraint([
		'features' => new ApiCollectionConstraint([
			'online_payment' => new ApiBooleanConstraint(),
		]),
	])]
	public mixed $invoices_settings;

	#[ApiArrayConstraint]
	#[ApiCountConstraint(
		min: 1,
		max: 1
	)]
	#[ApiCollectionConstraint(
		fields: ['predefined_data' => new ApiArrayConstraint()]
	)]
	public mixed $reports_settings;

	public function __construct(array $params)
	{
		$this->allowEmpty = false;
		parent::__construct($params);
	}
}
