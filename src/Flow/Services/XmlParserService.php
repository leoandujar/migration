<?php

namespace App\Flow\Services;

class XmlParserService
{
	public function parse(string $xmlFilePath): array
	{
		if (!file_exists($xmlFilePath)) {
			throw new \Exception("File not found: {$xmlFilePath}");
		}

		$xml = simplexml_load_file($xmlFilePath);

		if (false === $xml) {
			return [];
		}

		return $this->xmlToArray($xml);
	}

	public function parseFromString(string $xmlFilePath): ?string
	{
		$xml = simplexml_load_file($xmlFilePath);

		if (!$xml) {
			return null;
		}

		return $xml->asXML();
	}

	private function xmlToArray(\SimpleXMLElement $xml): array
	{
		$json = json_encode($xml);

		return json_decode($json, true);
	}
}
