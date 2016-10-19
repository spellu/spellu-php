<?php

namespace Spellu\SyntaxTree;

class StatementExpression extends Statement
{
	public $expr;

	public function __construct($expr)
	{
		$this->expr = $expr;
	}
}
