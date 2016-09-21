<?php

namespace Spellu\Runtime;

class ValueLazy
{
	protected $binder;

	public function __construct(callable $binder)
	{
		$this->binder = $binder;
	}

	public function resolve()
	{
		return call_user_func($this->binder);
	}
}
