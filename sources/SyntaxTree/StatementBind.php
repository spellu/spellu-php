<?php

namespace Spellu\SyntaxTree;

class StatementBind extends Statement
{
	public $name;
	public $expr;

	public function __construct($name, $expr)
	{
		$this->name = $name;
		$this->expr = $expr;
	}
}
