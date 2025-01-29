<?php

declare(strict_types=1);

namespace App\Model\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

class LikeJsonb extends FunctionNode
{
	public $field;
	public $pattern;

	public function parse(Parser $parser): void
	{
		$parser->match( TokenType::T_IDENTIFIER);
		$parser->match(TokenType::T_OPEN_PARENTHESIS);
		$this->field = $parser->ArithmeticPrimary();
		$parser->match(TokenType::T_COMMA);
		$this->pattern = $parser->ArithmeticPrimary();
		$parser->match(TokenType::T_CLOSE_PARENTHESIS);
	}

	public function getSql(SqlWalker $sqlWalker): string
	{
		return '('.$this->field->dispatch($sqlWalker).'::text LIKE '.$this->pattern->dispatch($sqlWalker).')';
	}
}
