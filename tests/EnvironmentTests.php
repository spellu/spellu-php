<?php

use Spellu\Platform\Environment;

/**
 * @group unit
 */
class EnvironmentTests extends TestCase
{
	/** @tests */
	function package_and_symbol_can_load()
	{
		$env = new Environment($GLOBALS['loader'], __DIR__.'/../vendor');

		Assert::notEmpty($env->packages());
		Assert::notEmpty($env->symbols());
	}

	/** @tests */
	function package_psr0()
	{
		$env = new Environment($GLOBALS['loader'], __DIR__.'/../vendor');

		$package = $env->package('phpspec/prophecy');
		Assert::notNull($package);
		Assert::count(1, $package->namespaces());

		$symbol = current($package->namespaces());
		Assert::equals('Prophecy', $symbol->name());
	}

	/** @tests */
	function package_psr4()
	{
		$env = new Environment($GLOBALS['loader'], __DIR__.'/../vendor');

		$package = $env->package('nikic/php-parser');
		Assert::notNull($package);
		Assert::count(1, $package->namespaces());

		$symbol = current($package->namespaces());
		Assert::equals('PhpParser', $symbol->name());
	}

	/** @tests */
	function namespace()
	{
		$env = new Environment($GLOBALS['loader'], __DIR__.'/../vendor');

		$symbol = $env->symbol('Symfony::Component::Yaml');
		Assert::notNull($symbol);
		Assert::equals('Symfony::Component::Yaml', $symbol->name());
//		Assert::equals('Symfony\Component\Yaml', $symbol->phpName());
	}

	/** @tests */
	function searchClass()
	{
		$env = new Environment($GLOBALS['loader'], __DIR__.'/../vendor');

		$namespace = $env->symbol('Symfony::Component::Yaml');
		Assert::notNull($namespace);

		$class = $env->asClass('Symfony::Component::Yaml::Escaper');
		Assert::notNull($class);
		Assert::equals('Symfony::Component::Yaml::Escaper', $class->name());
		Assert::equals(true, $class->isClass());
		Assert::equals(false, $class->isInterface());
		Assert::equals(false, $class->isTrait());
	}
}
