<?php

namespace Spellu\Runtime;

class Operator
{
	const NONE = 'none';
	const LEFT = 'left';
	const RIGHT = 'right';

	public $string;
	public $associativity;		// none, left, right
}
