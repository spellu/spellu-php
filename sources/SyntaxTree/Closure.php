<?php

namespace Spellu\SyntaxTree;

class Closure extends Node
{
	public $name;
	public $parameters;
	public $statements;

	public function __construct($name, $parameters, $statements)
	{
		$this->name = $name;
		$this->parameters = $parameters;
		$this->statements = $statements;
	}
}
