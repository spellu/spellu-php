<?php

namespace Spellu\Runtime;

use Composer\Autoload\ClassLoader;

class ValueDictionary extends Value implements \ArrayAccess
{
	public function offsetGet($subscript)
	{
		return $this->value[$subscript];
	}

	public function keys()
	{
		return array_keys($this->value);
	}

	public function values()
	{
		return array_values($this->value);
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
