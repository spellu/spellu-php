<?php

namespace Spellu\SyntaxTree;

use Spellu\Source\Token;

class LiteralVector extends Node
{
	public $elements;

	public function __construct($elements)
	{
		$this->elements = $elements;
	}
}
