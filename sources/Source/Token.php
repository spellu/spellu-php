<?php

namespace Spellu\Source;

class Token
{
	const OPERATOR_SIGNS = ['=', '-', '+', '!', '*', '/', '%', '<', '>', '&', '|', '^', '?', '~'];

	const EOF = 0;
	const WORD = 11;
	const NIL = 12;
	const TRUE = 13;
	const FALSE = 14;
	const INTEGER = 15;
	const REAL = 16;
	const STRING = 17;
	const OPERATOR = 31;	// =, -, +, !, *, /, %, <, >, &, |, ^, ?, ~
	const PERIOD = 32;		// .
	const PERIOD2 = 33;		// ..
	const PERIOD3 = 34;		// ...
	const COMMA = 41;		// ,
	const COLON = 42;		// :
	const SEMICOLON = 43;	// ;
	const L_PAREN = 61;		// (
	const R_PAREN = 62;		// )
	const L_BRACKET = 63;	// [
	const R_BRACKET = 64;	// ]
	const L_BRACE = 65;		// {
	const R_BRACE = 66;		// }
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
