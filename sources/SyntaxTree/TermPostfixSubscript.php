<?php

namespace Spellu\SyntaxTree;

class TermPostfixSubscript extends TermPostfix
{
	public $expression;	// expression

	public function __construct($expression)
	{
		$this->expression = $expression;
	}
}
