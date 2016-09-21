<?php

namespace Spellu\Semantic;

class SymbolThis extends Symbol
{
	public function __construct($node)
	{
		$this->node = $node;
	}
}
