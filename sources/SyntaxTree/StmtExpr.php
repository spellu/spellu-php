<?php

namespace Spellu\SyntaxTree;

class StmtExpr extends Stmt
{
	public $expr;

	public function __construct($expr)
	{
		$this->expr = $expr;
	}
}
