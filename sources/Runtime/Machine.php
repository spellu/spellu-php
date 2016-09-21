<?php

namespace Spellu\Runtime;

class Machine
{
	const NIL = null;
	const ARRAY_EMPTY = [];
	const DICTIONARY_EMPTY = [];

	protected $interpreter;
	protected $globals;
	protected $locals = [];
	protected $stack = [];

	public function __construct($globals = [])
	{
		$this->interpreter = new Interpreter($this);
		$this->globals = $globals;
	}

	public function run($ast)
	{
		$this->interpreter->run($ast);
	}

	public function typeFrom($value)
	{
		if (is_null($value)) return Type::nil();
		if (is_bool($value)) return Type::bool();
		if (is_int($value)) return Type::integer();
		if (is_float($value)) return Type::real();
		if (is_callable($value)) return Type::function();
		if (is_array($value)) {
			if ($value === self::ARRAY_EMPTY) return Type::array();
			if ($value === self::DICTIONARY_EMPTY) return Type::dictionary();
			foreach ($value as $key => $_) return is_numeric($key) ? Type::array() : Type::dictionary();
		}
		if (is_object($value)) return Type::object(get_class($value));
		return Type::string();
	}

	public function resolve($name)
	{
		if (array_key_exists($name, $this->locals)) {
			return $this->locals[$name];
		}
		if (array_key_exists($name, $this->globals)) {
			$value = $this->globals[$name];
			if ($value instanceof ValueLazy) $value = $value->resolve();
			return [$this->typeFrom($value), $value];
		}

		throw new SandboxException("Unresolve identifier '{$name}'");
	}

	public function defineVariable($name, $type, $value)
	{
		if (array_key_exists($name, $this->locals)) {
			throw new SandboxException("Already defined variable '{$name}'");
		}

		$this->locals[$name] = [$type, $value];
	}

	public function pushStack($value, $type = null)
	{
//echo 'Push:', $value, PHP_EOL;
		if ($type === null) $type = $this->typeFrom($value);

		array_push($this->stack, [$type, $value]);
	}

	public function popStack()
	{
//echo 'Pop:', $this->stack[count($this->stack) - 1][1], PHP_EOL;
		return array_pop($this->stack);
	}

	public function markStack()
	{
		return count($this->stack);
	}

	public function resetStackToMark($mark)
	{
		$this->popStackToMark($mark);
	}

	public function popStackToMark($mark)
	{
		return array_splice($this->stack, $mark);
	}

	public function popStackAll()
	{
		return array_splice($this->stack, 0);
	}

	public function invokeSubscript($type, $value, $script)
	{
		switch ($type->type) {
			case Type::ARRAY:
				// fetch
				$this->pushStack($value[$script]);
				break;

			case Type::DICTIONARY:
				// fetch
				$this->pushStack($value[$script]);
				break;

			case Type::OBJECT:
			case Type::CLASS_:
				// get
				$this->fetchObjectProperty($value, $script);
				break;

			default:
				throw new SandboxException("illegal cell type '{$type->type}'.");
		}
	}

	public function invokeMember($type, $value, $property)
	{
		switch ($type->type) {
			case Type::BOOLEAN:
			case Type::INTEGER:
			case Type::REAL:
			case Type::STRING:
				// TODO: toString() とか
				break;

			case Type::ARRAY:
				$this->fetchArrayProperty($value, $property);
				break;

			case Type::DICTIONARY:
				$this->fetchDictionaryProperty($value, $property);
				break;

			case Type::OBJECT:
			case Type::CLASS_:
				$this->fetchObjectProperty($value, $property);
				break;

			default:
				throw new SandboxException("illegal cell type '{$type->type}'.");
		}
	}

	protected function fetchArrayProperty($value, $property)
	{
		switch ($property) {
			case 'get':
			case 'fetch':
				$this->pushStack(function ($property) use ($value) {
					return $value[$property];
				});
				break;

//			case 'type':
			case 'count':
				$this->pushStack(count($value));
				break;

			case 'toString':
			case 'toJson':
				$this->pushStack(json_encode($value, true), Type::string());
				break;

			// default is 'get'
			default:
				list($type, $value) = $value[$property];
				$this->pushStack($value, $type);
		}
	}

	protected function fetchDictionaryProperty($value, $property)
	{
		switch ($property) {
			case 'get':
			case 'fetch':
				$this->pushStack(function ($property) use ($value) {
					return $value[$property];
				});
				break;

			case 'keys':
				$this->pushStack(array_keys($value), Type::array());
				break;

			case 'values':
				$this->pushStack(array_values($value), Type::array());
				break;

			case 'toString':
			case 'toJson':
				$this->pushStack(json_encode($value, true), Type::string());
				break;

			// default is 'get'
			default:
				$this->pushStack($value[$property]);
		}
	}

	protected function fetchObjectProperty($value, $property)
	{
		// Step1: instance method
		if (method_exists($value, $property)) {
			$this->pushStack([$value, $property], Type::method());
			return;
		}

		// Step2: instance property
		if (property_exists($value, $property)) {
			$this->pushStack($value->{$property});
			return;
		}

		// Step3: instance []
		if ($value instanceof \ArrayAccess) {
			$this->pushStack($value[$property]);
			return;
		}

		throw new SandboxException("property '{$property}' does not exists.");
	}
}
