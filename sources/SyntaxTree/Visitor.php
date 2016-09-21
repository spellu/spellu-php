<?php

namespace Spellu\SyntaxTree;

abstract class Visitor
{
	protected function visit($node)
	{
		$this->{'visit'.$node->type()}($node);
	}

	protected function visitStmtExpr($node)
	{
		echo __METHOD__, PHP_EOL;
	}

	protected function visitExprBinary($node)
	{
		echo __METHOD__, PHP_EOL;
	}

	protected function visitExprCall($node)
	{
		echo __METHOD__, PHP_EOL;
	}

	protected function visitExprProperty($node)
	{
		echo __METHOD__, PHP_EOL;
	}

	protected function visitIdentifier($node)
	{
		echo __METHOD__, PHP_EOL;
	}

	protected function visitLiteral($node)
	{
		echo __METHOD__, PHP_EOL;
	}
}
