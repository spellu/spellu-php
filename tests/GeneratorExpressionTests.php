<?php

use Spellu\Platform\Environment;
use Spellu\Compiler;
use Spellu\Generator;

/**
 * @group unit
 */
class GeneratorExpressionTests extends TestCase
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

	/** @test */
	function closure_1()
	{
		$source = 'let a = func () {}';
		$script = <<<EOS
\$a = function () {
};
EOS;

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function closure_2()
	{
		$source = 'let a = func {}';
		$script = <<<EOS
\$a = function () {
};
EOS;

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function closure_final()
	{
		$source = <<<EOS
func addNumber(number) {
	let sum = 0
	return func {
/*		sum += number*/
		return sum
	}
}
EOS;
		$script = <<<EOS
function addNumber(\$number)
{
    \$sum = 0;
    return function () use (&\$sum, \$number) {
        \$sum += \$number;
        return \$sum;
    };
}
EOS;

		Assert::equals($script, $this->generate($source));
	}
}
