<?php

namespace App\Model\Functions;

use Doctrine\ORM\Query\AST\ASTException;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

class Extract extends FunctionNode
{
	private string $field;
	private mixed $value;

	/** @throws ASTException */
	public function getSql(SqlWalker $sqlWalker): string
	{
		return sprintf(
			'EXTRACT(%s FROM %s)',
			$this->field,
			$this->value->dispatch($sqlWalker)
		);
	}

	/** @throws QueryException */
	public function parse(Parser $parser): void
	{
		$parser->match(TokenType::T_IDENTIFIER);
		$parser->match(TokenType::T_OPEN_PARENTHESIS);

		$parser->match(TokenType::T_IDENTIFIER);
		$this->field = $parser->getLexer()->token->value;

		$parser->match(TokenType::T_FROM);

		$this->value = $parser->ScalarExpression();

		$parser->match(TokenType::T_CLOSE_PARENTHESIS);
	}
}
