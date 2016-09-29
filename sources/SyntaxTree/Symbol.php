<?php

namespace Spellu\SyntaxTree;

class Symbol extends Node
{
	public $symbol;
	public $postfix;

	public function __construct($symbol, $postfix)
	{
		$this->symbol = $symbol;
		$this->postfix = $postfix;
	}
}
