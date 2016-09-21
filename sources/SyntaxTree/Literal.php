<?php

namespace Spellu\SyntaxTree;

use Spellu\Source\Token;

class Literal extends Node
{
	public $token;

	public function __construct($token)
	{
		$this->token = $token;
	}

	public function value()
	{
		$string = $this->token->string;

		switch ($this->token->type) {
			case Token::INTEGER:
				return (int)$string;

			case Token::REAL:
				return (float)$string;

			case Token::STRING:
				return substr($string, 1, strlen($string) - 2);
		}
	}
}
