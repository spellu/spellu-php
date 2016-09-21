<?php

namespace Spellu\SyntaxTree;

class ComponentClass extends Node
{
	public $name;
	public $components;
	public $members;

	public function __construct($name, $components, $members)
	{
		$this->name = $name;
		$this->components = $components;
		$this->members = $members;
	}
}
