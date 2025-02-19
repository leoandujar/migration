<?php
	
	namespace App\Model\Functions;
	
	use Doctrine\ORM\Query\AST\Functions\FunctionNode;
	use Doctrine\ORM\Query\Parser;
	use Doctrine\ORM\Query\SqlWalker;
	use Doctrine\ORM\Query\TokenType;
	
	class DatePart extends FunctionNode
	{
		public mixed $dateString = null;
		
		public mixed $dateFormat = null;
		
		public function parse(Parser $parser): void
		{
			$parser->match(TokenType::T_IDENTIFIER);
			$parser->match(TokenType::T_OPEN_PARENTHESIS);
			$this->dateString = $parser->ArithmeticPrimary();
			$parser->match(TokenType::T_COMMA);
			$this->dateFormat = $parser->ArithmeticPrimary();
			$parser->match(TokenType::T_CLOSE_PARENTHESIS);
		}
		
		public function getSql(SqlWalker $sqlWalker): string
		{
			return 'DATE_PART(' .
				$this->dateString->dispatch($sqlWalker) . ', ' .
				$this->dateFormat->dispatch($sqlWalker) .
				')';
		}
	}

