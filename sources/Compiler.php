<?php

namespace Spellu;

use Spellu\Source\Lexer;
use Spellu\Source\Token;
use Spellu\Source\Parser;

class Compiler
{
	public function compileFile($source_filepath, $destination_filepath)
	{

	}

	public function compile($source, $code)
	{
		// Generate AST
		$ast = (new Parser)->parse(Lexer::tokenGenerator($code));

//		$resolved = (new Resolver)->resolve($ast);

		// Generate PHP Script
		return $ast;
	}
}
