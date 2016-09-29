<?php

namespace Spellu\SyntaxTree;

class ExpressionBinary extends Expression
{
	public $operator;
	public $left;
	public $right;

	public function __construct($operator, $left, $right)
	{
		$this->operator = $operator;
		$this->left = $left;
		$this->right = $right;
	}
}
