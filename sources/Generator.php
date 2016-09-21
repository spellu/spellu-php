<?php

namespace Spellu;

use PhpParser\PrettyPrinter\Standard as SourcePrinter;

class Generator
{
	public function __construct(Platform\Environment $env)
	{
		$this->env = $env;
	}

	public function generate(array $ast)
	{
		$nodes = (new Semantic\Converter($this->env))->convert($ast);

		$printer = new SourcePrinter([
		]);

		return $printer->prettyPrintFile($nodes);
	}

	public function generateInline(array $ast)
	{
		$nodes = (new Semantic\Converter($this->env))->convert($ast);

		$printer = new SourcePrinter([
		]);

		return $printer->prettyPrint($nodes);
	}

	public function generateFile(array $ast, $path)
	{
		file_put_contents($path, $this->generate($ast));
	}
}
