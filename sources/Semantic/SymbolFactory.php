<?php

namespace Spellu\Semantic;

class SymbolFactory
{
	public static function make($reflection)
	{
		$class = static::detectSymbolClass($reflection);

		// TODO 型サポート v1ではVariantにする。
		return new $class($reflection, null);
	}

	protected static function detectSymbolClass($reflection)
	{
		$method = 'for'.static::reflectionType($reflection);

		return static::$method($reflection);
	}

	protected static function reflectionType($reflection)
	{
		return preg_replace('/^.*\\\\/', '', get_class($reflection));
	}

	protected static function forReflectionConstant($reflection)
	{
		if ($reflection->onClass())
			return SymbolClassConstant::class;
		else
			return SymbolConstant::class;
	}

	protected static function forReflectionFunction($reflection)
	{
		return SymbolFunction::class;
	}

	protected static function forReflectionNamespace($reflection)
	{
		return SymbolNamespace::class;
	}

	protected static function forReflectionClass($reflection)
	{
		if ($reflection->isInterface())
			return SymbolInterface::class;
		if ($reflection->isTrait())
			return SymbolTrait::class;
		return SymbolClass::class;
	}

	protected static function forReflectionMethod($reflection)
	{
		if ($reflection->isStatic()) return SymbolClassMethod::class;
		else return SymbolInstanceMethod::class;
	}

	protected static function forReflectionProperty($reflection)
	{
		if ($reflection->isStatic()) return SymbolClassProperty::class;
		else return SymbolInstanceProperty::class;
	}
}
