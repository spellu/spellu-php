<?php

namespace Spellu\Source\Parser;

use Spellu\SyntaxTree;
use Spellu\Source\Token;
use Spellu\SourceException;

trait Expression
{
	protected function parseExpression()
	{
		return $this->parseExpressionBinary1();
	}

	protected function parseExpressionBinary1()
	{
		$node = $this->parseExpressionBinary2();

		while ($this->nextTokenIf(Token::PLUS, Token::MINUS)) {
			$operator = $this->token;
			$right = $this->parseExpressionBinary2();
			$node = new SyntaxTree\ExpressionBinary($operator, $node, $right);
		}

		return $node;
	}

	protected function parseExpressionBinary2()
	{
		$node = $this->parseTerm();

		while ($this->nextTokenIf(Token::ASTERISK, Token::SLASH)) {
			$operator = $this->token;
			$right = $this->parseTerm();
			$node = new SyntaxTree\ExpressionBinary($operator, $node, $right);
		}

		return $node;
	}

	protected function parseTerm()
	{
		$node = $this->parseExprPrimary();
		$postfix_root = null;

		while (true) {
			if ($this->nextTokenIf(Token::PERIOD)) {
				if ($this->nextTokenIf(Token::WORD)) {
					$postfix = new SyntaxTree\TermPostfixProperty($this->token);
				}
				else if ($this->nextTokenIf(Token::L_BRACE)) {
					// TODO { expr }
				}
				else {
					throw new SourceException('Expected identifier');
				}
			}
			else if ($this->nextTokenIf(Token::L_PAREN)) {
				$arguments = $this->parseExpressionList(Token::R_PAREN);

				$postfix = new SyntaxTree\TermPostfixCall($arguments);
			}
			else if ($this->nextTokenIf(Token::L_BRACKET)) {
				$expr = $this->parseExpr();

				if (! $this->nextTokenIf(Token::R_BRACKET)) {
					// 構文エラー
					throw new SourceException('Expected ]');
				}

				$postfix = new SyntaxTree\TermPostfixSubscript($expr);
			}
			else if ($this->nextTokenIf(Token::L_BRACE)) {
				// TODO postブロック
			}
			else {
				// MEMO Termの終端
				break;
			}

			$this->addPostfix($postfix_root, $postfix);
		}

		return new SyntaxTree\Term($node, $postfix_root);
	}

	protected function addPostfix(&$root, $postfix)
	{
		if ($root === null) {
			$root = $postfix;
		}
		else {
			$last = $root;
			while ($last->next) $last = $last->next;
			$last->next = $postfix;
		}
	}

	protected function parseExprPrimary()
	{
		// nil or true or false or identifier
		if ($this->nextTokenIf(Token::WORD)) {
			switch ($this->token->string) {
				case 'nil':
					$this->token->type = Token::NIL;
					return new SyntaxTree\Literal($this->token);
				case 'true':
					$this->token->type = Token::TRUE;
					return new SyntaxTree\Literal($this->token);
				case 'false':
					$this->token->type = Token::FALSE;
					return new SyntaxTree\Literal($this->token);
				case 'self':
				case 'this':
					// TODO
					return null;
				case 'func':
					return $this->parseClosure();
				default:
					return new SyntaxTree\Identifier($this->token);
			}
		}
		// literal
		if ($this->nextTokenIf(Token::INTEGER, Token::REAL, Token::STRING)) {
			return new SyntaxTree\Literal($this->token);
		}
		// vector-literal
		if ($this->nextTokenIf(Token::L_BRACKET)) {
			$elements = [];
			if (! $this->nextTokenIf(Token::R_BRACKET)) {
				while (true) {
					$elements[] = $this->parseExpr();

					if ($this->nextTokenIf(Token::COMMA)) {
						continue;
					}
					if ($this->nextTokenIf(Token::R_BRACKET)) {
						break;
					}

					throw new SourceException('Expected , or ]');
				}
			}

			return new SyntaxTree\LiteralVector($elements);
		}
		// map-literal
		if ($this->nextTokenIf(Token::L_BRACE)) {
			$node = new SyntaxTree\LiteralMap;

			if (! $this->nextTokenIf(Token::R_BRACE)) {
				while (true) {
					$key = $this->parseExpr();

					if ($this->nextTokenIf(Token::COLON)) {
						$node->add($key, $this->parseExpr());
					}
					else {
						$node->add($key);
					}

					if ($this->nextTokenIf(Token::COMMA)) continue;
					if ($this->nextTokenIf(Token::R_BRACE)) break;

					throw new SourceException('Expected , or }');
				}
			}

			return $node;
		}
		// parenthesized-expression
		if ($this->nextTokenIf(Token::L_PAREN)) {
			$node = $this->parseExpr();

			if (! $this->nextTokenIf(Token::R_PAREN)) {
				// 構文エラー
				throw new SourceException('Expected )');
			}

			return $node;
		}

		// 構文エラー
		var_dump($this->token);
		var_dump($this->nextToken());
		throw new SourceException('Expected IDENTIFIER|LITERAL|(');
	}

	protected function parseExpressionList($terminateTokenType)
	{
		$list = [];

		if (! $this->nextTokenIf($terminateTokenType)) {
			while (true) {
				$list[] = $this->parseExpression();

				if ($this->nextTokenIf(Token::COMMA)) continue;
				if ($this->nextTokenIf($terminateTokenType)) break;

				throw new SourceException('Expected , or )');
			}
		}

		return $list;
	}

	protected function parseClosure()
	{
		$name = null;
		$parameters = [];
		$statements = [];

		// Optional: name
		if ($this->nextTokenIf(Token::WORD)) {
			$name = $this->token;
		}

		// Optional: ()
		if ($this->currentTokenIf(Token::L_PAREN)) {
			$parameters = $this->parseParameterList();
		}

		$statements = $this->parseStatementList();

		return new SyntaxTree\Closure($name, $parameters, $statements);
	}
}
