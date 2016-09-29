<?php

namespace Spellu\SyntaxTree;

class StatementReturn extends Statement
{
	public $expr;

	public function __construct($expr /*nullable*/)
	{
		$this->expr = $expr;
	}
}
