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

			if (! $this->nextTokenIf(Token::EQUAL)) {
				throw new SourceException('Expected =');
			}

			$node = new SyntaxTree\StmtBind($left, $this->parseExpression());
		}
		else {
			$node = new SyntaxTree\StmtExpr($this->parseExpression());
		}

		while ($this->nextTokenIf(Token::SEMICOLON)) {
			// skip
		}

		return $node;
//echo '#'.$this->stream->peek();

//		throw new SourceException('Unexpected token: '.$this->nextToken()->text);
	}
}
