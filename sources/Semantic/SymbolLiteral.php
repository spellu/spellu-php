<?php

namespace Spellu\Semantic;

class SymbolLiteral extends Symbol
{
	public function __construct($node)
	{
		$this->node = $node;
	}
}
