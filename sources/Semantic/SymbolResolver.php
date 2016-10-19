<?php

namespace Spellu\Semantic;

use Spellu\SyntaxTree\Term;
use Spellu\SyntaxTree\Symbol as SymbolNode;
use Spellu\SemanticException;

class SymbolResolver
{
	public function __construct($env, $frame = []/*TODO*/)
	{
		$this->env = $env;
		$this->frame = $frame;
		$this->symbol = null;
	}

	public function resolveName(array $tokens)
	{
		$symbol = null;

		foreach ($tokens as $token) {
			if ($symbol === null) {
				$symbol = $this->searchName($token->string);
				if ($symbol === null) return null;
			}
			else if ($symbol instanceof SymbolNamespace) {
				$symbol = $this->searchInNamespace($symbol, $token->string);
			}
			else if ($symbol instanceof SymbolClass) {
				$symbol = $this->searchInClass($symbol, $token->string);
			}
		}

		return $symbol;
	}

	public function resolve(Term $node)
	{
		$symbol = $this->visit($node->object);

		$postfix = $node->postfix;

		while ($postfix) {
			if ($postfix->type() != 'TermPostfixProperty')
				break;

			if ($symbol instanceof SymbolNamespace) {
				$symbol = $this->searchInNamespace($symbol, $postfix->property->string);
			}
			else if ($symbol instanceof SymbolClass) {
				$symbol = $this->searchInClass($symbol, $postfix->property->string);
			}
			else {
				break;
			}

			$postfix = $postfix->next;
		}

		return new SymbolNode($symbol, $postfix);
	}

	protected function visit($node)
	{
		return $this->{'visit'.$node->type()}($node);
	}

	protected function visitIdentifier($node)
	{
		$name = $node->token->string;

		$symbol = $this->searchName($name);

		if ($symbol === null) {
			// TODO: search in local frames
			return $this->symbol = new SymbolVariable($node);
		}

		return $symbol;
	}

	protected function visitLiteral($node)
	{
		return new SymbolLiteral($node);
	}

	protected function searchName($name)
	{
		if ($reflection = $this->env->asClass($name)) {
			return $this->makeSymbol($reflection);
		}
		if ($reflection = $this->env->asFunction($name)) {
			return $this->makeSymbol($reflection);
		}
		if ($reflection = $this->env->asConstant($name)) {
			return $this->makeSymbol($reflection);
		}
		if ($reflection = $this->env->asNamespace($name)) {
			return $this->makeSymbol($reflection);
		}
		return null;
	}

	protected function searchInNamespace($symbol, $name)
	{
		$name = $symbol->fullname($name);

		if ($reflection = $this->env->asClass($name)) {
			return $this->makeSymbol($reflection);
		}
		if ($reflection = $this->env->asFunction($name)) {
			return $this->makeSymbol($reflection);
		}
		if ($reflection = $this->env->asConstant($name)) {
			return $this->makeSymbol($reflection);
		}
		if ($reflection = $this->env->asNamespace($name)) {
			return $this->makeSymbol($reflection);
		}

		throw new SemanticException("'symbol {$name}' not found.");
	}

	protected function searchInClass($symbol, $name)
	{
		if ($reflection = $this->asMethodInClass($symbol, $name)) {
			return $this->makeSymbol($reflection);
		}
		if ($reflection = $this->asPropertyInClass($symbol, $name)) {
			return $this->makeSymbol($reflection);
		}
		if ($reflection = $this->asConstantInClass($symbol, $name)) {
			return $this->makeSymbol($reflection);
		}
		// TODO Method, Property, ...

		assert(false, 'visitTermPostfixProperty. '.$name);
	}

	protected function asConstantInClass($symbol, $name)
	{
		return $symbol->constant($name) ?? null;
	}

	protected function asMethodInClass($symbol, $name)
	{
		return $symbol->method($name) ?? null;
	}

	protected function asPropertyInClass($symbol, $name)
	{
		return $symbol->property($name) ?? null;
	}

	protected function makeSymbol($reflection)
	{
		return SymbolFactory::make($reflection);
	}
}
