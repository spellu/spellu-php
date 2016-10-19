<?php

namespace Spellu\Runtime;

class OperatorGroup
{
	const ASSOCIATIVITY_NONE = 'none';
	const ASSOCIATIVITY_LEFT = 'left';
	const ASSOCIATIVITY_RIGHT = 'right';

	public $name;
	public $associativity;		// none, left, right
	public $operators;

	public function __construct($name, $associativity)
	{
		$this->name = $name;
		$this->associativity = $associativity;
		$this->operators = [];
	}

	public function addOperator($operator)
	{
		$this->operators[] = $operator;
	}
}
