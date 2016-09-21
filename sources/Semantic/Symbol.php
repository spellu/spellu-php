<?php

namespace Spellu\Semantic;

abstract class Symbol
{
	protected $reflection;

	protected $type;

	public function __construct($reflection, $type)
	{
		$this->reflection = $reflection;
		$this->type = $type;
	}

	public function type()
	{
		return preg_replace('/^.*\\\\Symbol/', '', get_class($this));
	}

	public function reflection()
	{
		return $this->reflection;
	}

	public function __call($method, $args)
	{
		return call_user_func_array([$this->reflection, $method], $args);
	}
}
