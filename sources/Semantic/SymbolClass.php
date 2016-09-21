<?php

namespace Spellu\Semantic;

class SymbolClass extends Symbol
{
	public function fullname($name = null)
	{
		if ($name) return $this->fullname().'.'.$name;
		else return $this->reflection->name();
	}
}
