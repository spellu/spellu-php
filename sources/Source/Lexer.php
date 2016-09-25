<?php

namespace Spellu\Source;

use Spellu\SourceException;

class Lexer
{
	/**
	 * @param string $code
	 * @return \Generator
	 */
	public static function tokenGenerator($code)
	{
		$lexer = new Lexer($code);

		while ($token = $lexer->nextToken()) {
			yield $token;
		}

		$line_no = $lexer->stream->lineNo();
		$column_no = $lexer->stream->columnNo();
		yield new Token(Token::EOF, '', $line_no);
	}

	/**
	 * @param string $code
	 * @return array
	 */
	public static function getAllTokens($code)
	{
		$tokens = [];
		foreach (static::tokenGenerator($code) as $token) {
			if ($token->type == Token::SPACE || $token->type == Token::COMMENT)
				continue;
			$tokens[] = $token;
		}
		return $tokens;
	}

	protected $code;
	protected $stream;

	public function __construct($code)
	{
		$this->code = $code;
		$this->stream = new CharacterStream($code);
	}

	/**
	 * @return \Spellu\Source\Token | null
	 */
	public function nextToken()
	{
		$line_no = $this->stream->lineNo();
		$column_no = $this->stream->columnNo();
		$token_start = $this->stream->currentIndex();

		$id = $this->detectToken();
		if ($id === null) return null;
		$string = substr($this->code, $token_start, $this->stream->currentIndex() - $token_start);

		return new Token($id, $string, $line_no);
	}

	/**
	 * @return int | null
	 */
	protected function detectToken()
	{
		switch ($ch = $this->stream->next()) {
			case null:
				return null;
			case "\r":
			case "\n":
			case "\t":
			case ' ':
			case '　':
				return $this->consumeSpace();
			case '.':
				if ($this->stream->nextIf('.')) {
					return $this->stream->nextIf('.') ? Token::PERIOD3 : Token::PERIOD2;
				}
				return Token::PERIOD;
			case '+': return Token::PLUS;
			case '-': return Token::MINUS;
			case '*': return Token::ASTERISK;
			case '/':
				if ($this->stream->nextIf('/')) return $this->consumeLineComment();
				if ($this->stream->nextIf('*')) return $this->consumeBlockComment();
				return Token::SLASH;
			case '*': return Token::ASTERISK;
			case '%': return Token::PERCENT;
			case '?': return Token::QUESTION;
			case '!': return Token::EXCLAMATION;
			case '&': return Token::AMPERSAND;
			case ',': return Token::COMMA;
			case ':': return Token::COLON;
			case ';': return Token::SEMICOLON;
			case '=': return Token::EQUAL;
			case '(': return Token::L_PAREN;
			case ')': return Token::R_PAREN;
			case '[': return Token::L_BRACKET;
			case ']': return Token::R_BRACKET;
			case '{': return Token::L_BRACE;
			case '}': return Token::R_BRACE;
			case '\'': return $this->consumeString($ch);
			case '\"': return $this->consumeString($ch);
			default:
				if ($ch >= '0' && $ch <= '9') return $this->consumeNumber();
				if ($ch == '$' || $ch == '_' || ($ch >= 'a' && $ch <= 'z') || ($ch >= 'A' && $ch <= 'Z')) return $this->consumeWord();

				// Error: Illegal Character
				throw new SourceException('Illegal Character');
		}
	}

	protected function consumeSpace()
	{
		while ($this->stream->nextIf(" 　\t\r\n") !== null) {}
		return Token::SPACE;
	}

	protected function consumeLineComment()
	{
		$this->stream->skipTo('/\\s+/');
		return Token::COMMENT;
	}

	protected function consumeBlockComment()
	{
		if (!$this->stream->skipTo('/\\*\\//')) throw new SourceException('Expected */');
		return Token::COMMENT;
	}

	protected function consumeNumber()
	{
		$this->stream->back();

		if ($this->stream->skipTo('/[0-9]+\\.[0-9]+/')) {
			return Token::REAL;
		}
		if ($this->stream->skipTo('/[0-9]+/')) {
			return Token::INTEGER;
		}
		assert(false);
	}

	protected function consumeString($ch)
	{
		// TODO: エスケープシーケンス

		if ($this->stream->skipTo("/.*{$ch}/")) {
			return Token::STRING;
		}

		if (!$this->skipTo('/\\*\\//')) throw new SourceException('Expected '.$ch);
	}

	protected function consumeWord()
	{
		if ($this->stream->skipTo('/[0-9a-zA-Z_]*/')) {
			return Token::WORD;
		}
		assert(false);
	}
}
