<?php

namespace Spellu\SyntaxTree;

class LiteralPair
{
	public $key;
	public $value;

	public function __construct($key, $value)
	{
		$this->key = $key;
		$this->value = $value;
	}
}
