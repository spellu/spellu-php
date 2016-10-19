<?php

namespace Spellu\Source;

use Spellu\SourceException;
use Spellu\Dsl\Funcuit;

class Parser implements Funcuit
{
	use Parser\Funcuit;
	use Parser\Component;
	use Parser\Statement;
	use Parser\Expression;

	protected $stream;
	protected $token;

	/**
	 * @param \Generator $generator
	 * @return array
	 */
	public function parse($generator)
	{
		$this->stream = $this->tokenStream($generator);

		$ast = [];

		while (! $this->nextTokenIf(Token::EOF)) {
			if ($this->nextTokenIfWord('class')) {
				$ast[] = $this->parseClass();
			}
			else if ($this->nextTokenIfWord('func')) {
				$ast[] = $this->parseFunction();
			}
			else {
				$ast[] = $this->parseStatement();
			}
		}

		return $ast;
	}

	/**
	 * @param \Generator $generator
	 * @return \Generator
	 */
	protected function tokenStream($generator)
	{
		foreach ($generator as $token) {
			if ($token->type == Token::SPACE || $token->type == Token::COMMENT)
				continue;
			yield $token;
		}
	}

	protected function currentToken()
	{
		return $this->token = $this->stream->current();
	}

	protected function nextToken()
	{
		$this->token = $this->stream->current();

		$this->stream->next();

		return $this->token;
	}

	protected function currentTokenIf(...$types)
	{
		$token = $this->token = $this->stream->current();

		if (in_array($token->type, $types)) {
			return true;
		}

		return false;
	}

	protected function nextTokenIf(...$types)
	{
		$token = $this->token = $this->stream->current();

		if (in_array($token->type, $types)) {
			$this->stream->next();
			return true;
		}

		return false;
	}

	protected function nextTokenIfWord(...$keywords)
	{
		$token = $this->token = $this->stream->current();

		if ($token->type == Token::WORD && in_array($token->string, $keywords)) {
			$this->stream->next();
			return true;
		}

		return false;
	}

	protected function nextTokenIfOperator(...$operators)
	{
		$token = $this->token = $this->stream->current();

		if ($token->type == Token::OPERATOR && in_array($token->string, $operators)) {
			$this->stream->next();
			return true;
		}

		return false;
	}
}
