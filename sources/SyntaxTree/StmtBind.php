<?php

namespace Spellu\SyntaxTree;

class StmtBind extends Stmt
{
	public $name;
	public $expr;

	public function __construct($name, $expr)
	{
		$this->name = $name;
		$this->expr = $expr;
	}
}
