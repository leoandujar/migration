<?php

namespace App\Flow\Services;

class ConditionEvaluatorService
{
	/**
	 * Currently, the v3 feature of workflows supports a simple condition. Based on this condition, an action
	 * will or will not be executed. For example, in the OCR of the legacy workflow XTRFV2,if the OCR value of
	 * the params is false, the OCR process will not be executed.
	 *
	 * @param string $paramKeyToCompare This parameter is a key that should be in the general parameters of the entire flow. For example ocr, the new key is: "azureModelId", if its value is null, it is because perhaps you do not want to perform OCR,
	 * @param mixed  $operand           The value to compare. This may already be a primitive data type.
	 * @param string $operator          Type of condition (operator). For example: '==', '===', '!=', '!==', '>', '<', '>=', '<='.
	 * @param array  $params            the parameters ingeneral, where the paramKeyToCompare will be searched
	 *
	 * @return bool returns true if the condition is met, otherwise false
	 */
	private function conditionEval(string $paramKeyToCompare, string $operator, mixed $operand, array $params): bool
	{
		$paramKeyToCompare = $params[$paramKeyToCompare];

		return match ($operator) {
			'==' => $paramKeyToCompare == $operand,
			'===' => $paramKeyToCompare === $operand,
			'!=' => $paramKeyToCompare != $operand,
			'!==' => $paramKeyToCompare !== $operand,
			'>' => $paramKeyToCompare > $operand,
			'<' => $paramKeyToCompare < $operand,
			'>=' => $paramKeyToCompare >= $operand,
			'<=' => $paramKeyToCompare <= $operand,
			default => false,
		};
	}
}
