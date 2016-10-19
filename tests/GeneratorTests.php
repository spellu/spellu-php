<?php

use Spellu\Platform\Environment;
use Spellu\Compiler;
use Spellu\Generator;

/*
use PhpParser\ParserFactory;
$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$code = $parser->parse('<?php Spellu\Test\Class01::$propertyStaticPublic;');
var_dump($code);
$code = $parser->parse('<?php Spellu\Source\Token::INTEGER;');
var_dump($code);
echo (new PhpParser\PrettyPrinter\Standard)->prettyPrint($code), PHP_EOL;
echo (new PhpParser\PrettyPrinter\Standard)->prettyPrint([new PhpParser\Node\Name\FullyQualified('Spellu\Source\Token::INTEGER')]), PHP_EOL;

var_dump(ast\flags\MAGIC_DIR);
var_dump(defined('MAGIC_DIR'));
var_dump(defined('ast\flags\MAGIC_DIR'));
var_dump(defined('Spellu\Source\Token::INTEGER'));
var_dump(defined('\Spellu\Source\Token::INTEGER'));
die;
*/
/*
use PhpParser\ParserFactory;
$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$code = $parser->parse('<?php 10;');
var_dump($code);
echo (new PhpParser\PrettyPrinter\Standard)->prettyPrint($code), PHP_EOL;
*/

/**
 * @group unit
 */
class GeneratorTests extends TestCase
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
	function literal_1()
	{
		$source = '1';
		$script = '1;';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function literal_2()
	{
		$source = '1.2';
		$script = '1.2;';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function literal_3()
	{
		$source = "'hoge'";
		$script = "'hoge';";

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_namespace_1()
	{
		$source = 'Spellu';
		$script = '\Spellu';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_namespace_2()
	{
		$source = 'Spellu.Test';
		$script = '\Spellu\Test';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_namespace_3()
	{
		$source = 'Spellu.Test.NotFound';
		$script = '\Spellu\Test\NotFound';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_constant_1()
	{
		$source = 'CONST_GLOBAL';
		$script = '\CONST_GLOBAL;';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_constant_2()
	{
		$source = 'Spellu.Test.CONST_NAMESPACE';
		$script = '\Spellu\Test\CONST_NAMESPACE;';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_function_1()
	{
		$source = 'functionGlobal';
		$script = '\functionGlobal();';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_function_2()
	{
		$source = 'functionGlobal()';
		$script = '\functionGlobal();';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_function_3()
	{
		$source = 'Spellu.Test.functionNamespace';
		$script = '\Spellu\Test\functionNamespace();';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_function_4()
	{
		$source = 'Spellu.Test.functionNamespace()';
		$script = '\Spellu\Test\functionNamespace();';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_class_1()
	{
		$source = 'Spellu.Test.Class01';
		$script = '\Spellu\Test\Class01';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_class_new_1()
	{
		$source = 'Spellu.Test.Class01()';
		$script = 'new \Spellu\Test\Class01();';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_class_new_2()
	{
		$source = 'Spellu.Test.Class01(1, 2+3)';
		$script = 'new \Spellu\Test\Class01(1, 2 + 3);';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_class_constant_1()
	{
		$source = 'Spellu.Test.Class01.CONSTANT_PUBLIC';
		$script = '\Spellu\Test\Class01::CONSTANT_PUBLIC;';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_class_method_1()
	{
		$source = 'Spellu.Test.Class01.methodStaticPublic';
		$script = '\Spellu\Test\Class01::methodStaticPublic();';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_class_property_1()
	{
		$source = 'Spellu.Test.Class01.propertyStaticPublic';
		$script = '\Spellu\Test\Class01::$propertyStaticPublic;';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_trait_1()
	{
		$source = 'Spellu.Test.Trait01';
		$script = '\Spellu\Test\Trait01';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function identifier_local_1()
	{
		$source = 'local';
		$script = '$local;';

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function class_1()
	{
		$source = 'class ABC {}';
		$script = <<<EOS
class ABC
{
}
EOS;

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function class_2()
	{
		$source = 'class ABC.DEF {}';
		$script = <<<EOS
namespace ABC {
    class DEF
    {
    }
}
EOS;

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function class_3()
	{
		$source = 'class ABC.DEF.GHI {}';
		$script = <<<EOS
namespace ABC\DEF {
    class GHI
    {
    }
}
EOS;

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function method_1()
	{
		$source = <<<EOS
class ABC {
	func a() {
	}
}
EOS;
		$script = <<<EOS
class ABC
{
    function a()
    {
    }
}
EOS;

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function method_2()
	{
		$source = <<<EOS
class ABC {
	func a(b) {
		let c = b
	}
}
EOS;
		$script = <<<EOS
class ABC
{
    function a(\$b)
    {
        \$c = \$b;
    }
}
EOS;

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function method_3()
	{
		$source = <<<EOS
class ABC {
	func a(b : int) {
		let c = b
	}
}
EOS;
		$script = <<<EOS
class ABC
{
    function a(int \$b)
    {
        \$c = \$b;
    }
}
EOS;

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function method_4()
	{
		$source = <<<EOS
class ABC {
	func a(b : int = 1) {
		let c = b
	}
}
EOS;
		$script = <<<EOS
class ABC
{
    function a(int \$b = 1)
    {
        \$c = \$b;
    }
}
EOS;

		Assert::equals($script, $this->generate($source));
	}

	/** @test */
	function method_5()
	{
		$source = <<<EOS
class ABC {
	func a() {
		let b = 62
	}
}
EOS;
		$script = <<<EOS
class ABC
{
    function a()
    {
        \$b = 62;
    }
}
EOS;

		Assert::equals($script, $this->generate($source));
	}
}
