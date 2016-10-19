<?php

use Spellu\Platform\Environment;
use Spellu\Compiler;
use Spellu\Generator;

/**
 * @group unit
 */
class GeneratorStatementTests extends TestCase
{
	function setUp()
	{
		$this->env = new Environment($GLOBALS['loader'], __DIR__.'/../vendor');
		$this->compiler = new Compiler();
		$this->generator = new Generator($this->env);
	}

	protected function compile($source)
	{
		return $this->compiler->compile('Memory', $source);
	}

	protected function generate($source)
	{
		return $this->generator->generateInline($this->compile($source));
	}

	/** @test */
	function assignment_1()
	{
		$source = 'a = 1';
		$script = <<<EOS
\$a = 1;
EOS;

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function return_1()
	{
		$source = 'return 1';
		$script = <<<EOS
return 1;
EOS;

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function return_2()
	{
		$source = 'return func () {}';
		$script = <<<EOS
return function () {
};
EOS;

		Assert::equals($script, $this->generate($source));
	}
}
