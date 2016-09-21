<?php

namespace Spellu\SyntaxTree;

abstract class Node
{
	public function type()
	{
		return preg_replace('/^.*\\\\/', '', get_class($this));
	}

	public function __toString()
	{
		return $this->type();
	}
}
