<?php

namespace Spellu\SyntaxTree;

use Spellu\Source\Token;

class LiteralMap extends Node
{
	public $properties;

	public function __construct($properties = [])
	{
		$this->properties = $properties;
	}

	public function add($key, $value = null)
	{
		if ($value === null) $value = $key;
		$this->properties[] = ['key' => $key, 'value' => $value];
	}
}
