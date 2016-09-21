<?php

use Spellu\Source\Lexer;
use Spellu\Source\Token;

/**
 * @group unit
 * @group lex
 */
class LexerTests extends TestCase
{
	/** @test */
	function generator_1()
	{
		$source = '1+2';

		$generator = Lexer::tokenGenerator($source);

		$token = $generator->current();
		Assert::isTrue($generator->valid());
		Assert::equals(Token::INTEGER, $token->type);

		$generator->next();
		$token = $generator->current();
		Assert::isTrue($generator->valid());
		Assert::equals(Token::PLUS, $token->type);

		$generator->next();
		$token = $generator->current();
		Assert::isTrue($generator->valid());
		Assert::equals(Token::INTEGER, $token->type);

		$generator->next();
		$token = $generator->current();
		Assert::isTrue($generator->valid());
		Assert::equals(Token::EOF, $token->type);

		$generator->next();
		$token = $generator->current();
		Assert::isFalse($generator->valid());
		Assert::isNull($token);
	}

	/** @test */
	function 整数_1桁()
	{
		$source = '1';

		$tokens = $this->getAllTokens($source);
		Assert::count(2, $tokens);
		Assert::equals(Token::INTEGER, $tokens[0]->type);
		Assert::equals(Token::EOF, $tokens[1]->type);
	}

	/** @test */
	function literal_integer_複数桁()
	{
		$source = '1234';

		$tokens = $this->getAllTokens($source);
		Assert::count(2, $tokens);
		Assert::equals(Token::INTEGER, $tokens[0]->type);
		Assert::equals(Token::EOF, $tokens[1]->type);
	}

	/** @test */
	function literal_real()
	{
		$source = '1.1';

		$tokens = $this->getAllTokens($source);
		Assert::count(2, $tokens);
		Assert::equals(Token::REAL, $tokens[0]->type);
		Assert::equals(Token::EOF, $tokens[1]->type);
	}

	/** @test */
	function literal_integer_and_member()
	{
		$source = '1.a';

		$tokens = $this->getAllTokens($source);
		Assert::count(4, $tokens);
		Assert::equals(Token::INTEGER, $tokens[0]->type);
		Assert::equals(Token::PERIOD, $tokens[1]->type);
		Assert::equals(Token::WORD, $tokens[2]->type);
		Assert::equals(Token::EOF, $tokens[3]->type);
	}

	/** @test */
	function two_word()
	{
		$source = 'class Class';

		$tokens = $this->getAllTokens($source);
		Assert::count(3, $tokens);
		Assert::equals(Token::WORD, $tokens[0]->type);
		Assert::equals(Token::WORD, $tokens[1]->type);
		Assert::equals(Token::EOF, $tokens[2]->type);
	}

	protected function getAllTokens($source)
	{
		return Lexer::getAllTokens($source);
	}
}
