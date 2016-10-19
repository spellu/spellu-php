<?php

namespace Spellu\Runtime\Interpreter;

trait Operator
{
	protected function add()
	{
		list($rt, $rv) = $this->machine->popStack();
		list($lt, $lv) = $this->machine->popStack();

		switch ($lt->type) {
			case Type::INTEGER:
			case Type::REAL:
				$this->machine->pushStack($lv + $rv, $lt);
				return;

			case Type::STRING:
				$this->machine->pushStack($lv . $rv, $lt);
				return;

			default:
				// 未定義
				return;
		}
	}

	protected function subtract()
	{
		list($rt, $rv) = $this->machine->popStack();
		list($lt, $lv) = $this->machine->popStack();

		switch ($lt->type) {
			case Type::INTEGER:
			case Type::REAL:
				$this->machine->pushStack($lv - $rv, $lt);
				return;

			default:
				// 未定義
				return;
		}
	}
}
