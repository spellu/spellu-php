<?php

namespace Spellu\SyntaxTree;

class Term extends Node
{
	public $object;	// Literal, Identifier, Expression
	public $postfix;

	public function __construct($object, $postfix)
	{
		$this->object = $object;
		$this->postfix = $postfix;
	}
}
