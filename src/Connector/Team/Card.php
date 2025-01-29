<?php

namespace App\Connector\Team;

class Card
{
	public const STATUS_SUCCESS_COLOR = '01BC36'; // success
	public const STATUS_FAILURE_COLOR = 'FF0000'; // failure
	public const STATUS_SUCCESS = 'success';
	public const STATUS_FAILURE = 'failure';

	private mixed $data;

	/**
	 * Card constructor.
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function getMessage(): array
	{
		$data = [
			'@type' => 'MessageCard',
			'@context' => 'http://schema.org/extensions',
			'summary' => 'Subscription Card',
			'themeColor' => ($this->data['status'] ?? null === self::STATUS_SUCCESS) ? self::STATUS_SUCCESS_COLOR : self::STATUS_FAILURE_COLOR,
			'title' => 'Notification: '.$this->data['title'] ?? '',
			'sections' => [
				[
					'activityTitle' => '',
					'activitySubtitle' => '',
					'activityImage' => '',
					'facts' => [
						[
							'name' => 'Title:',
							'value' => $this->data['title'] ?? '',
						],
						[
							'name' => 'Date',
							'value' => (new \DateTime())->format('Y-m-d H:i:s'),
						],
					],
					'text' => $this->data['message'] ?? '',
				],
			],
		];
		if (isset($this->data['link'])) {
			$data['sections'][0]['facts'][] = [
				'name' => 'Link:',
				'value' => '[Download]('.$this->data['link'].')',
			];
		}

		return $data;
	}
}
