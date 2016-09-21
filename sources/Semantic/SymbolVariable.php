<?php

namespace Spellu\Semantic;

class SymbolVariable extends Symbol
{
	public function __construct($node)
	{
		$this->node = $node;
	}

	public function name()
	{
		return $this->node->token->string;
	}
}
