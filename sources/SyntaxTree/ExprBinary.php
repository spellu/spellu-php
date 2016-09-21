<?php

namespace Spellu\SyntaxTree;

class ExprBinary extends Expr
{
	public $left;
	public $right;
	public $operator;

	public function __construct($left, $right, $operator)
	{
		$this->left = $left;
		$this->right = $right;
		$this->operator = $operator;
	}
}
