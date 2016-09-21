<?php

namespace Spellu\Platform;

class ReflectionNamespace extends Reflection
{
	protected $fullname;

	public function __construct($fullname)
	{
		parent::__construct(null);
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
