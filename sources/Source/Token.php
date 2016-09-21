<?php

namespace Spellu\Source;

class Token
{
	const EOF = 0;
	const WORD = 11;
	const NIL = 12;
	const TRUE = 13;
	const FALSE = 14;
	const INTEGER = 15;
	const REAL = 16;
	const STRING = 17;
	const PERIOD = 31;
	const PLUS = 32;
	const MINUS = 33;
	const ASTERISK = 34;
	const SLASH = 35;
	const PERCENT = 36;
	const EXCLAMATION = 37;
	const EQUAL = 38;
	const COMMA = 40;
	const COLON = 41;
	const SEMICOLON = 42;
	const L_PAREN = 51;		// (
	const R_PAREN = 52;		// )
	const L_BRACKET = 53;	// [
	const R_BRACKET = 54;	// ]
	const L_BRACE = 55;		// {
	const R_BRACE = 56;		// }
	const SPACE = 91;
	const COMMENT = 92;

	public $type;
	public $string;
	public $line;
	public $column;
	public $line_first_token;

	public function __construct($type, $string, $line = 0)
	{
		$this->type = $type;
		$this->string = $string;
		$this->line = $line;
	}

	public function is($type)
	{
		return $this->type == $type;
	}

	public function isValue()
	{
		return $this->type == Token::NUMBER || $this->type == Token::WORD;
	}

	public function isUnaryOperator()
	{
		return $this->type == Token::EXCLAMATION;
	}

	public function isBinaryOperator()
	{
		return $this->type == Token::PLUS || $this->type == Token::MINUS || $this->type == Token::ASTERISK || $this->type == SLASH;
	}

	public function __toString()
	{
		return "{type: {$this->type}}";
	}
}
