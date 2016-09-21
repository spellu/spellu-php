<?php

namespace Spellu\Semantic;

class Type
{
	const VARIANT = 'variant';
	const NIL = 'nil';
	const BOOLEAN = 'boolean';
	const INTEGER = 'integer';
	const REAL = 'real';
	const STRING = 'string';
	const ARRAY = 'array';
	const DICTIONARY = 'dictionary';
	const CALLABLE = 'callable';
	const OBJECT = 'object';
	const CLASS_ = 'class';
	const METHOD = 'method';

	public static function variant()
	{
		return new static(self::VARIANT);
	}

	public static function nil()
	{
		return new static(self::NIL);
	}

	public static function boolean()
	{
		return new static(self::BOOLEAN);
	}

	public static function integer()
	{
		return new static(self::INTEGER);
	}

	public static function real()
	{
		return new static(self::REAL);
	}

	public static function string()
	{
		return new static(self::STRING);
	}

	public static function array()
	{
		return new static(self::ARRAY);
	}

	public static function dictionary()
	{
		return new static(self::DICTIONARY);
	}

	public static function callable()
	{
		return new static(self::CALLABLE);
	}

	public static function object($class)
	{
		return new static(self::OBJECT, $class);
	}

	public static function class($class)
	{
		return new static(self::CLASS_, $class);
	}

	public static function method()
	{
		return new static(self::METHOD);
	}

	public $type;
	public $class;

	public function __construct($type, $class = null)
	{
		$this->type = $type;
		$this->class = $class;
	}

	public function __toString()
	{
		return $this->class ? "$this->type($this->class)" : $this->type;
	}
}
