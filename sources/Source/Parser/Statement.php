<?php

namespace Spellu\Source\Parser;

use Spellu\SyntaxTree;
use Spellu\Source\Token;
use Spellu\SourceException;

trait Statement
{
	protected function parseStatement()
	{
		if ($this->nextTokenIfWord('var', 'let')) {
			if (! $this->nextTokenIf(Token::WORD)) {
				throw new SourceException('Expected <WORD>');
			}

			$left = $this->token;

			if (! $this->nextTokenIfOperator('=')) {
				throw new SourceException('Expected =');
			}

			$node = new SyntaxTree\StatementBind($left, $this->parseExpression());
		}
		else if ($this->nextTokenIfWord('if')) {
		}
		else if ($this->nextTokenIfWord('do')) {
		}
		else if ($this->nextTokenIfWord('while')) {
		}
		else if ($this->nextTokenIfWord('for')) {
		}
		else if ($this->nextTokenIfWord('return')) {
			$node = new SyntaxTree\StatementReturn($this->parseExpression());
		}
		else {
			$node = new SyntaxTree\StatementExpression($this->parseExpression());
		}

		while ($this->nextTokenIf(Token::SEMICOLON)) {
			// skip
		}

		return $node;
//echo '#'.$this->stream->peek();

//		throw new SourceException('Unexpected token: '.$this->nextToken()->text);
	}
}
