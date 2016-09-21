<?php

namespace Spellu\SyntaxTree;

class TermPostfixCall extends TermPostfix
{
	public $arguments;

	public function __construct($arguments)
	{
		$this->arguments = $arguments;
	}
}
