<?php

namespace Spellu\Runtime;

use Composer\Autoload\ClassLoader;

abstract class Value
{
	protected $value;

	public function __construct($value)
	{
		$this->value = $value;
	}

	public function __get($property)
	{
		// Step 1: property
		if (property_exists($this->value, $property)) {
			return $this->value->{$property};
		}

		// Step 2: none parameter method
		if (method_exists($this->value, $method = $property)) {
			return $this->value->{$method}();
		}
		if (method_exists($this->value, $method = 'get'.ucfirst($property))) {
			return $this->value->{$method}();
		}
	}

	public function __set($property, $value)
	{
		// Step 1: property
		if (property_exists($this->value, $property)) {
			$this->value->{$property} = $value;
		}

		// Step 2: none parameter method
		if (method_exists($this->value, $method = 'set'.ucfirst($property))) {
			$this->value->{$method}($value);
		}
	}

	public function __call($method, array $args)
	{
		return call_user_func_array([$this, $method], $args);
	}
}
