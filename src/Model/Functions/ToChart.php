<?php

namespace App\Model\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

class ToChart extends FunctionNode
{
	public mixed $dateExpression;

	public mixed $patternExpression;

	public function parse(Parser $parser): void
	{
		$parser->match(TokenType::T_IDENTIFIER);
		$parser->match(TokenType::T_OPEN_PARENTHESIS);
		$this->dateExpression = $parser->ArithmeticExpression();
		$parser->match(TokenType::T_COMMA);
		$this->patternExpression = $parser->StringPrimary();
		$parser->match(TokenType::T_CLOSE_PARENTHESIS);
	}

	public function getSql(SqlWalker $sqlWalker): string
	{
		return 'TO_CHAR('.
			$this->dateExpression->dispatch($sqlWalker).', '.
			$this->patternExpression->dispatch($sqlWalker).
			')';
	}
}
