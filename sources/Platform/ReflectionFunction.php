<?php

namespace Spellu\Platform;

/**
 *  グローバル関数
 */
class ReflectionFunction extends Reflection
{
	public $fullname;

	public function __construct($fullname, $reflection)
	{
		parent::__construct($reflection);
		$this->fullname = $fullname;
	}

	public function name()
	{
		return $this->fullname;
	}

	public function phpName()
	{
		return Environment::toPhpSymbol($this->fullname);
	}
}
