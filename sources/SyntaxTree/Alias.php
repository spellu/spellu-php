<?php

namespace Spellu\SyntaxTree;

class Alias extends Node
{
	public $name;
	public $components;

	public function __construct($name, $components)
	{
		$this->name = $name;
		$this->components = $components;
	}
}
