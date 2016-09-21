<?php

namespace Spellu\Platform;

class ReflectionClass extends Reflection
{
	protected $fullname;

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
		return $this->reflection->getName();
	}

	public function isClass()
	{
		return !$this->reflection->isInterface() && !$this->reflection->isTrait();
	}

	public function isInterface()
	{
		return $this->reflection->isInterface();
	}

	public function isTrait()
	{
		return $this->reflection->isTrait();
	}

	public function constant($name)
	{
		try {
			$value = $this->reflection->getConstant($name);
			return new ReflectionConstant($this->fullname.'.'.$name, $value);
		}
		catch (\ReflectionException $e) {
			return null;
		}
	}

	public function constants()
	{
		$result = [];
		foreach ($this->reflection->getConstants() as $name => $value) {
			$result[$name] = new ReflectionConstant($this->fullname.'.'.$name, $value);
		}
		return $result;
	}

	public function method($name)
	{
		try {
			return new ReflectionMethod($this->reflection->getMethod($name));
		}
		catch (\ReflectionException $e) {
			return null;
		}
	}

	public function methods($scope = \ReflectionMethod::IS_PUBLIC)
	{
		$result = [];
		foreach ($this->reflection->getMethods($scope) as $name => $value) {
			$result[$name] = new ReflectionMethod($this->fullname.'.'.$name, $value);
		}
		return $result;
	}

	public function property($name)
	{
		try {
			return new ReflectionProperty($this->reflection->getProperty($name));
		}
		catch (\ReflectionException $e) {
			return null;
		}
	}

	public function properties($scope = \ReflectionProperty::IS_PUBLIC)
	{
		$result = [];
		foreach ($this->reflection->getProperties($scope) as $name => $value) {
			$result[$name] = new ReflectionProperty($this->fullname.'.'.$name, $value);
		}
		return $result;
	}
}
