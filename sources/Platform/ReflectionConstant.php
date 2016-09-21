<?php

namespace Spellu\Platform;

/**
 *  グローバル定数
 *  クラス定数
 */
class ReflectionConstant extends Reflection
{
	protected $fullname;
	protected $value;

	public function __construct($fullname, $value)
	{
		parent::__construct(null);
		$this->fullname = $fullname;
		$this->value = $value;
	}

	public function fullname()
	{
		return $this->fullname;
	}

	public function value()
	{
		return $this->value;
	}

	public function className()
	{
		return explode('.', $this->fullname)[0];
	}

	public function phpName()
	{
		$parts = explode('.', $this->fullname);
		return count($parts) == 2 ? Environment::toPhpSymbol($parts[0]).'::'.$parts[1] : Environment::toPhpSymbol($this->fullname);
	}

	public function phpClassName()
	{
		$parts = explode('.', $this->fullname);
		return count($parts) == 2 ? Environment::toPhpSymbol($parts[0]) : null;
	}

	public function phpMemberName()
	{
		$parts = explode('.', $this->fullname);
		return count($parts) == 2 ? $parts[1] : null;
	}

	public function onGlobal()
	{
		return strpos($this->fullname, '.') === false;
	}

	public function onClass()
	{
		return strpos($this->fullname, '.') !== false;
	}
}
