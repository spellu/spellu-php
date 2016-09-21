<?php

namespace Spellu\Platform;

class Reflection
{
	protected $reflection;

	public function __construct($reflection = null)
	{
		$this->reflection = $reflection;
	}

	public function __get($property)
	{
		if ($this->reflection) {
			return $this->reflection->{$property};
		}
		else {
			throw new \BadMethodCallException($method);
		}
	}

	public function __call($method, $args)
	{
		if ($this->reflection) {
			return call_user_func_array([$this->reflection, $method], $args);
		}
		else {
			throw new \BadMethodCallException($method);
		}
	}
};
