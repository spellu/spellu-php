<?php

namespace Spellu\Runtime;

class ValueArray extends Value implements \ArrayAccess
{
	public function offsetGet($subscript)
	{
		return $this->value[$subscript];
	}

	public function count()
	{
		return count($this->value);
	}

	public function map(callable $callable)
	{
		return array_map($callable, $this->value);
	}

	public function filter(callable $callable)
	{
		return array_filter($this->value, $callable);
	}

	public function reduce(callable $callable)
	{
		return array_reduce($this->value, $callable);
	}

	public function toString()
	{
		return $this->toJSON();
	}

	public function toJSON()
	{
		return json_encode($this->value);
	}
}
