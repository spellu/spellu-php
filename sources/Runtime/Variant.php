<?php

namespace Spellu\Runtime;

final class Variant extends Value
{
	public $type;
	public $class;

	public static function from($value)
	{
		if (is_null($value)) return new static($value, Type::NIL);
		if (is_bool($value)) return new static($value, Type::BOOLEAN);
		if (is_int($value)) return new static($value, Type::INTEGER);
		if (is_float($value)) return new static($value, Type::REAL);
		if (is_callable($value)) return new static($value, Type::CALLABLE);
		if (is_array($value)) {
			// TODO
			if (count($value) === 0) return new static($value, Type::NIL);
			foreach ($value as $key => $_) {
				return new static($value, is_numeric($key) ? Type::ARRAY : Type::DICTIONARY);
			}
		}
		if (is_object($value)) return static::object(get_class($value));
		return static::string();
	}

	public function __construct($value, $type, $class = null)
	{
		parent::__construct($value);
		$this->type = $type;
		$this->class = $class;
	}

	public function __get($property)
	{
		switch ($this->type) {
			case Type::BOOLEAN:
				return booleanGet($property);
			case Type::INTEGER:
				return integerGet($property);
			case Type::REAL:
				return realGet($property);
			default:
				return parent::__get($property);
		}
	}

	public function __call($method, array $args)
	{
		switch ($this->type) {
			case Type::BOOLEAN:
				return booleanCall($property);
			case Type::INTEGER:
				return integerCall($property);
			case Type::REAL:
				return realCall($property);
			default:
				return parent::__call($property);
		}
	}
}
