<?php

namespace Spellu\Source;

class CharacterStream
{
	protected $string;
	protected $line_no;
	protected $column_no;
	protected $index;

	public function __construct($string, $start_line_no = 1, $start_column_no = 1)
	{
		$this->string = preg_replace("/\\r\\n|\\r/", "\n", $string);
		$this->limit = strlen($string);
		$this->line_no = $start_line_no;
		$this->column_no = $start_column_no;
		$this->index = 0;
	}

	/**
	 * @return string | null
	 */
	public function next()
	{
		$ch = $this->peek();
		++$this->index;

		if ($ch == "\n") {
			++$this->line_no;
			$this->column_no = 0;
		}
		else {
			++$this->column_no;
		}

		return $ch;
	}

	/**
	 * @param string | array $characters
	 * @return string | null
	 */
	public function nextIf($characters)
	{
		$ch = $this->peek();
		if ($ch === null) return null;

		if (is_string($characters)) $characters = str_split($characters);
		if (! in_array($ch, $characters, true)) return null;

		++$this->index;
		return $ch;
	}

	/**
	 * @return void
	 */
	public function back()
	{
		assert($this->index > 0);
		--$this->index;
	}

	/**
	 * @return string | null
	 */
	public function peek()
	{
		if ($this->index >= $this->limit) return null;
		return $this->string[$this->index];
	}

	/**
	 * @param string $regex
	 * @return bool
	 */
	public function skipTo($regex)
	{
		$result = preg_match($regex, $this->string, $matches, PREG_OFFSET_CAPTURE, $this->index);
		assert($result !== false);

		if ($result) {
//var_dump($matches);
			list($string, $offset) = $matches[0];
			$this->index = $offset + strlen($string);
			return true;
		}
		else {
			return false;
		}
	}

	public function lineNo()
	{
		return $this->index;
	}

	public function columnNo()
	{
		return $this->index;
	}

	public function currentIndex()
	{
		return $this->index;
	}
}
