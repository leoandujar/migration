<?php

namespace App\Connector\Utils;

class ConnectorsUtils
{
	public function translateUrlToBrowser(string $url): ?array
	{
		$filters = [];
		if (empty($url)) {
			return null;
		}
		$query = parse_url($url, PHP_URL_QUERY);
		if (!$query) {
			return null;
		}
		parse_str($query, $filters);
		$expectedKeys = ['viewId', 'page', 'filters'];
		$expectedKeys = array_combine($expectedKeys, $expectedKeys);
		foreach (array_keys($filters) as $key) {
			if (!isset($expectedKeys[$key])) {
				unset($filters[$key]);
			}
		}
		if (!empty(isset($filters['filters']))) {
			foreach (explode(';', $filters['filters']) as $filter) {
				$parts = explode(':', $filter);
				if (2 !== count($parts)) {
					continue;
				}
				$filters["q.$parts[0]"] = $parts[1];
			}
			unset($filters['filters']);
			$filters['useDeferredColumns'] = 'multipleRequests';
		}

		return $filters;
	}
}
