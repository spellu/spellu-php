<?php

namespace Spellu\SyntaxTree;

class TermPostfixProperty extends TermPostfix
{
	public $property;	// identifier, {expression}

	public function __construct($property)
	{
		$this->property = $property;
	}
}
