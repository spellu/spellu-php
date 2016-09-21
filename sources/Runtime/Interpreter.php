<?php

namespace Spellu\Runtime;

use Spellu\SyntaxTree\Node;
use Spellu\SyntaxTree\Visitor;
use Spellu\Source\Token;

class Interpreter extends Visitor
{
	protected $machine;
	protected $operator;

	public function __construct($machine)
	{
		$this->machine = $machine;
		$this->operator = new Operator($this->machine);
	}

	public function run($ast)
	{
		foreach ($ast as $node) {
			$this->visit($node);
		}
	}

	protected function visitStmtBind($node)
	{
		$mark = $this->machine->markStack();

		$this->visit($node->expr);

		list($t, $v) = $this->machine->popStack();
		$this->machine->defineVariable($node->name->string, $t, $v);

		echo 'Variable: ', $node->name->string, PHP_EOL;

		$this->machine->resetStackToMark($mark);
	}

	protected function visitStmtExpr($node)
	{
		$mark = $this->machine->markStack();

		$this->visit($node->expr);

		echo 'Result: ';
		foreach ($this->machine->popStackAll() as $cell) {
			echo $cell[0].':'.print_r($cell[1], true), ', ';
		}
		echo PHP_EOL;

		$this->machine->resetStackToMark($mark);
	}

	protected function visitExprBinary($node)
	{
		$this->visit($node->left);
		$this->visit($node->right);

		switch ($node->operator->type) {
			case Token::PLUS:
				$this->operator->add();
				break;

			case Token::MINUS:
				$this->operator->subtract();
				break;
		}
	}

	protected function visitTerm($node)
	{
		$this->visit($node->object);

		$postfix = $node->postfix;

		while ($postfix) {
			$this->visit($postfix);
			$postfix = $postfix->next;
		}
	}

	protected function visitTermPostfixProperty($node)
	{
		list($type, $value) = $this->machine->popStack();

		$this->machine->invokeMember($type, $value, $node->property->string);
	}

	protected function visitTermPostfixCall($node)
	{
		list($type, $callable) = $this->machine->popStack();

		$mark = $this->machine->markStack();

		foreach ($node->arguments as $argument) {
			$this->visit($argument);
		}

		$args = $this->machine->popStackToMark($mark);

		$v = call_user_func_array($callable, array_map(function ($cell) { return $cell[1]; }, $args));

		$this->machine->pushStack($v);
	}

	protected function visitTermPostfixSubscript($node)
	{
		list($type, $value) = $this->machine->popStack();

		$this->visit($node->expr);

		list(, $script) = $this->machine->popStack();

		$this->machine->invokeSubscript($type, $value, $script);
	}

	protected function visitIdentifier($node)
	{
		list($type, $value) = $this->machine->resolve($node->token->string);

		$this->machine->pushStack($value, $type);
	}

	protected function visitLiteral($node)
	{
		switch ($node->token->type) {
			case Token::NIL:
				$type = Type::nil();
				break;

			case Token::TRUE:
			case Token::FALSE:
				$type = Type::boolean();
				break;

			case Token::INTEGER:
				$type = Type::integer();
				break;

			case Token::REAL:
				$type = Type::real();
				break;

			case Token::STRING:
			default:
				$type = Type::string();
				break;
		}

		$this->machine->pushStack($node->value(), $type);
	}

	protected function visitLiteralArray($node)
	{
		$mark = $this->machine->markStack();

		foreach ($node->elements as $node) {
			$this->visit($node);
		}

		$args = $this->machine->popStackToMark($mark);

		// fetch value only
		$this->machine->pushStack(array_map(function ($element) {
			return $element[1];
		}, $args), Type::array());
	}

	protected function visitLiteralDictionary($node)
	{
		$mark = $this->machine->markStack();

		$properties = [];
		foreach ($node->properties as $pair) {
			// eval key
			if ($pair['key']->type() == 'Identifier')
				$key = $pair['key']->token->string;
			elseif ($pair['key']->type() == 'Literal')
				$key = $pair['key']->value();
			else
				throw new SandboxException("key error.");

			// eval value
			$this->visit($pair['value']);
			list($value_type, $value) = $this->machine->popStack();

			// fetch value only
			$properties[$key] = $value;
		}

		$this->machine->pushStack($properties, Type::dictionary());
	}
}
