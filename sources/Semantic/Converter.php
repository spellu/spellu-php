<?php

namespace Spellu\Semantic;

use Spellu\Platform\Environment;
use Spellu\Source\Token;
use Spellu\SyntaxTree\Node;
use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt as PHPStmt;
use PhpParser\Node\Expr as PHPExpr;
use PhpParser\Node\Name as PHPName;
use PhpParser\Node\Scalar as PHPScalar;
use PhpParser\Node\Param as PHPParameter;
use Spellu\SemanticException;

class Converter
{
	protected $env;
	protected $factory;
	protected $local_frame;
	protected $stack;

	public function __construct(Environment $env)
	{
		$this->env = $env;
		$this->factory = new BuilderFactory();
		$this->local_frame = [];
		$this->stack = [];
	}

	public function convert(array $ast)
	{
		$php_nodes = [];

		foreach ($ast as $node) {
			$php_nodes[] = $this->visit($node);
		}

		return $php_nodes;
	}

	protected function visit($node)
	{
		return $this->{'visit'.$node->type()}($node);
	}

	protected function visitComponentClass($node)
	{
		$statements = [];
		foreach ($node->members as $member) {
			$statements[] = $this->visit($member);
		}

		return new PHPStmt\Class_($node->name->string, [
			'type' => 0,
			'extends' => null,
			'implements' => [],
			'stmts' => $statements,
		]);
	}

	protected function visitComponentMethod($node)
	{
		$parameters = [];
		foreach ($node->parameters as $parameter) {
			$parameters[] = $this->visit($parameter);
		}

		$statements = [];
		foreach ($node->statements as $statement) {
			$statements[] = $this->visit($statement);
		}

		return new PHPStmt\ClassMethod($node->name->string, [
			'type' => 0,
			'byRef' => false,
			'params' => $parameters,
			'returnType' => null,
			'stmts' => $statements,
		]);
	}

	protected function visitFunctionParameter($node)
	{
		$default = $node->default ? $this->visit($node->default) : null;

		$type = $node->type ? $node->type->string : null;

		return new PHPParameter($node->name->string, $default, $type);
	}

	protected function visitStmtBind($node)
	{
		$expr = $this->visit($node->expr);
		return new PHPExpr\Assign(new PHPExpr\Variable($node->name->string), $expr);
	}

	protected function visitStmtExpr($node)
	{
		return $this->visit($node->expr);
	}

	protected function visitExprBinary($node)
	{
		$left = $this->visit($node->left);
		$right = $this->visit($node->right);
		return new PHPExpr\BinaryOp\Plus($left, $right);
	}

	protected function visitTerm($node)
	{
		$resolver = new SymbolResolver($this->env, []);

		$node = $resolver->resolve($node);

		return $this->visit($node);
	}

	protected function visitSymbol($node)
	{
		$symbol = $node->symbol;
		$postfix = $node->postfix;

		switch ($symbol->type()) {
			case 'Namespace':
				$code = new PHPName\FullyQualified($symbol->phpName());
				break;

			case 'Constant':
				$code = new PHPExpr\ConstFetch(new PHPName\FullyQualified($symbol->phpName()));
				break;

			case 'Function':
				$arguments = [];
				if ($this->postfixIsCall($postfix)) {
					$arguments = $this->visitTermPostfixCall($postfix);
					$postfix = $postfix->next;
				}
				$code = new PHPExpr\FuncCall(new PHPName\FullyQualified($symbol->phpName()), $arguments);
				break;

			case 'Class':
				if ($this->postfixIsCall($postfix)) {
					$arguments = $this->visitTermPostfixCall($postfix);
					$postfix = $postfix->next;
					$code = new PHPExpr\New_(new PHPName\FullyQualified($symbol->phpName()), $arguments);
				}
				else {
					$code = new PHPName\FullyQualified($symbol->phpName());
				}
				break;

			case 'ClassConstant':
				$code = new PHPExpr\ClassConstFetch(new PHPName\FullyQualified($symbol->phpClassName()), $symbol->phpMemberName());
				break;

			case 'ClassMethod':
				$arguments = [];
				if ($this->postfixIsCall($postfix)) {
					$arguments = $this->visitTermPostfixCall($postfix);
					$postfix = $postfix->next;
				}
				$code = new PHPExpr\StaticCall(new PHPName\FullyQualified($symbol->phpClassName()), $symbol->phpMemberName(), $arguments);
				break;

			case 'ClassProperty':
				$code = new PHPExpr\StaticPropertyFetch(new PHPName\FullyQualified($symbol->phpClassName()), $symbol->phpMemberName());
				break;

			case 'InstanceMethod':
				$arguments = [];
				if ($this->postfixIsCall($postfix)) {
					$arguments = $this->visitTermPostfixCall($postfix);
					$postfix = $postfix->next;
				}
				$code = new PHPExpr\MethodCall(new PHPName\FullyQualified($symbol->phpClassName()), $symbol->phpMemberName(), $arguments);
				break;

			case 'InstanceProperty':
				$code = new PHPExpr\PropertyFetch(new PHPName\FullyQualified($symbol->phpClassName()), $symbol->phpMemberName());
				break;

			case 'Variable':
				$code = new PHPExpr\Variable($symbol->name());
				break;

			case 'Literal':
				$code = $this->convertLiteral($symbol);
				break;

			default:
var_dump($node->symbol);
				assert(false, __METHOD__.$symbol->type());
		}

		while ($postfix) {
			$code = $this->visitTermPostfix($code, $postfix);
			$postfix = $postfix->next;
		}

		return $code;
	}

	protected function postfixIsCall($node)
	{
		return $node && $node->type() == 'TermPostfixCall';
	}

	protected function convertLiteral($symbol)
	{
		assert($symbol);

		switch ($symbol->node->token->type) {
			case Token::INTEGER:
				return new PHPScalar\LNumber($symbol->node->value());
			case Token::REAL:
				return new PHPScalar\DNumber($symbol->node->value());
			case Token::STRING:
				return new PHPScalar\String_($symbol->node->value());
		}
	}

	protected function visitTermPostfix($code, $node)
	{
var_dump(3, $node);
		return $code;
	}

	protected function visitTermPostfixProperty($code, $node)
	{
		// class.property
		// function.property
		// constant.property
		// expression.property

var_dump(4, $code);
		return new PHPExpr\PropertyFetch($code, $node->property->string);
	}

	protected function visitTermPostfixCall($node)
	{
		$arguments = [];

		foreach ($node->arguments as $argument) {
			$arguments[] = $this->visit($argument);
		}

		return $arguments;
	}

	protected function visitExprSubscript($node)
	{
		echo __METHOD__, PHP_EOL;
	}

	protected function visitIdentifier($node)
	{
		return new PHPExpr\Variable($node->token->string);
	}

	protected function visitLiteral($node)
	{
		return new PHPScalar\String_($node->value());
	}

	protected function visitLiteralArray($node)
	{
		echo __METHOD__, PHP_EOL;
	}

	protected function visitLiteralDictionary($node)
	{
		echo __METHOD__, PHP_EOL;
	}
}
