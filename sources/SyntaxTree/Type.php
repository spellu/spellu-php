<?php

namespace Spellu\SyntaxTree;

class Type extends Node
{
	public $components;

	public function __construct($components)
	{
		$this->components = $components;
	}
}
