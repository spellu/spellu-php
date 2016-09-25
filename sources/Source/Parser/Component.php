<?php

namespace Spellu\Source\Parser;

use Spellu\SyntaxTree;
use Spellu\Source\Token;
use Spellu\SourceException;

trait Component
{
	protected function parseDottedName()
	{
		$names = [];

		do {
			if (! $this->nextTokenIf(Token::WORD)) {
				throw new SourceException('Expected <WORD>');
			}

			$names[] = $this->token;
		} while ($this->nextTokenIf(Token::PERIOD));

		return $names;
	}

	protected function parseClass()
	{
		$name = $this->parseDottedName();

		// TODO extends, implements

		if (! $this->nextTokenIf(Token::L_BRACE)) {
			throw new SourceException('Expected {');
		}

		$members = [];
		while (! $this->nextTokenIf(Token::R_BRACE)) {
			$members[] = $this->parseMember();
		}

		return new SyntaxTree\ComponentClass($name, [], $members);
	}

	protected function parseMember()
	{
		if ($this->nextTokenIfWord('var', 'let')) {
			return $this->parseField();
		}
		if ($this->nextTokenIfWord('func')) {
			return $this->parseMethod();
		}

		throw new SourceException('Expected var, let, func');
	}

	protected function parseField()
	{
		if (! $this->nextTokenIf(Token::WORD)) {
			throw new SourceException('Expected <WORD>');
		}

		$name = $this->token;

		if (! $this->nextTokenIf(Token::EQUAL)) {
			throw new SourceException('Expected =');
		}

		$expression = $this->parseExpression();

		return new SyntaxTree\ComponentField($name, $expression);
	}

	protected function parseMethod()
	{
		if (! $this->nextTokenIf(Token::WORD)) {
			throw new SourceException('Expected <WORD>');
		}
		$name = $this->token;

		$parameters = $this->parseParameterList();

		if (! $this->nextTokenIf(Token::L_BRACE)) {
			throw new SourceException('Expected {');
		}

		$statements = [];
		while (! $this->nextTokenIf(Token::R_BRACE)) {
			$statements[] = $this->parseStatement();
		}

		return new SyntaxTree\ComponentMethod($name, $parameters, $statements);
	}


	protected function parseParameterList()
	{
		$parameters = [];

		if (! $this->nextTokenIf(Token::L_PAREN)) {
			throw new SourceException('Expected (');
		}

		while (! $this->nextTokenIf(Token::R_PAREN)) {
			if (! $this->nextTokenIf(Token::WORD)) {
				throw new SourceException('Expected <WORD>');
			}
			$name = $this->token;
			$type = null;
			$default = null;

			if ($this->nextTokenIf(Token::COLON)) {
				if (! $this->nextTokenIf(Token::WORD)) {
					throw new SourceException('Expected <WORD>');
				}
				$type = $this->token;
			}

			if ($this->nextTokenIf(Token::EQUAL)) {
				$default = $this->parseExpression();
			}

			$parameters[] = new SyntaxTree\FunctionParameter($name, $type, $default);

			if ($this->nextTokenIf(Token::COMMA)) continue;
			if ($this->nextTokenIf(Token::R_PAREN)) break;

			throw new SourceException('Expected , or )');
		}

		return $parameters;
	}
}
