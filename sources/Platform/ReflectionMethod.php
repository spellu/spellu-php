<?php

namespace Spellu\Platform;

/**
 *  クラスメソッド、インスタンスメソッド
 */
class ReflectionMethod extends Reflection
{
	public function __construct($reflection)
	{
		parent::__construct($reflection);
	}

	public function phpName()
	{
		return $this->phpClassName().'::'.$this->phpMemberName();
	}

	public function phpClassName()
	{
		return $this->reflection->class;
	}

	public function phpMemberName()
	{
		return $this->reflection->name;
	}
}
