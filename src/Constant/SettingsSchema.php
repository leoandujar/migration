<?php

namespace App\Constant;

use App\Linker\Services\RedisClients;

class SettingsSchema
{
	public static function features(): array
	{
		return [
			'projects' => [
				[
					'name' => 'autoStart',
					'type' => 'toggle',
				],
				[
					'name' => 'quickEstimate',
					'type' => 'toggle',
				],
				[
					'name' => 'workingFilesAsRefFiles',
					'type' => 'toggle',
				],
				[
					'name' => 'updateWorkingFiles',
					'type' => 'toggle',
				],
				[
					'name' => 'downloadConfirmation',
					'type' => 'toggle',
				],
				[
					'name' => 'duplicateTask',
					'type' => 'toggle',
				],
				[
					'name' => 'analyzeFiles',
					'type' => 'toggle',
				],
				[
					'name' => 'dearchive',
					'type' => 'toggle',
				],
				[
					'name' => 'maxFileSize',
					'type' => 'number',
				],
				[
					'name' => 'filesQueue',
					'type' => 'dropdown',
					'options' => [
						RedisClients::SESSION_KEY_PROJECTS_QUOTES_NORMAL,
						RedisClients::SESSION_KEY_PROJECTS_QUOTES_HIGH,
						RedisClients::SESSION_KEY_PROJECTS_QUOTES_URGENT,
					],
				],
				[
					'name' => 'deadlinePrediction',
					'type' => 'toggle',
				],
				[
					'name' => 'deadlineOptions',
					'type' => 'taglist',
					'options' => [
						1,
						2,
						5,
						10,
					],
				],
				[
					'name' => 'fileExtensionsWarning',
					'type' => 'taglist',
					'localized' => true,
					'options' => [
						'pdf',
						'jpg',
						'png',
						'indd',
						'wav',
						'mp3',
						'mp4',
						'avi',
						'mov',
						'zip',
					],
				],
				[
					'name' => 'categories',
					'type' => 'taglist',
					'localized' => true,
					'options' => [],
				],
			],

			'invoices' => [
				[
					'name' => 'onlinePayment',
					'type' => 'toggle',
				],
			],
		];
	}
}
