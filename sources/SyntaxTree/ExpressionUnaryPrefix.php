<?php

namespace Spellu\SyntaxTree;

class ExpressionUnaryPrefix extends Expression
{
	public $operator;
	public $expression;

	public function __construct($operator, $expression)
	{
		$this->operator = $operator;
		$this->expression = $expression;
	}
}
