<?php

namespace Spellu\Runtime;

class OperatorTable
{
	protected $priority;

	public static function standard()
	{
		$table = new static;

		$table->addPrefixGroup()
			->add('logical_not', '!')
			->add('bit_not', '~')
			->add('positive', '+')
			->add('negative', '-')
		;

		$table->addPostfixGroup()
			->add('safe_unwrap', '?')
			->add('checked_unwrap', '!')
		;

		$table->addInfixGroup('bit_shift', OperatorGroup::ASSOCIATIVITY_NONE)
			->add('lshift', '<<')
			->add('rshift', '>>')
		;

		$table->addInfixGroup()
			->add('mul', '*', Operator::LEFT)
			->add('div', '/', Operator::LEFT)
			->add('mod', '%', Operator::LEFT)
			->add('and', '&', Operator::LEFT)
		;

		$table->addInfixGroup()
			->add('add', '+', Operator::LEFT)
			->add('sub', '-', Operator::LEFT)
			->add('or', '|', Operator::LEFT)
			->add('xor', '^', Operator::LEFT)
		;

		$table->addInfixGroup()
			->add('add', '+', Operator::LEFT)
			->add('sub', '-', Operator::LEFT)
			->add('or', '|', Operator::LEFT)
			->add('xor', '^', Operator::LEFT)
		;

		$table->addInfixGroup()
			->add('is', 'is', Operator::NONE)
			->add('as', 'as', Operator::NONE)
		;

		$table->addInfixGroup()
			->add('', '<', Operator::NONE)
			->add('', '<=', Operator::NONE)
			->add('', '>', Operator::NONE)
			->add('', '>=', Operator::NONE)
			->add('', '==', Operator::NONE)
			->add('', '!=', Operator::NONE)
			->add('', '===', Operator::NONE)
			->add('', '!==', Operator::NONE)
		;

		$table->addInfixGroup()
			->add('', '&&', Operator::LEFT)
		;

		$table->addInfixGroup()
			->add('', '||', Operator::LEFT)
			->add('nil_coalescing', '??', Operator::RIGHT)
		;

		$table->addInfixGroup()
			->add('', '?:', Operator::RIGHT)
		;

		$table->addInfixGroup()
			->add('', '=', Operator::RIGHT)
			->add('', '*=', Operator::RIGHT)
			->add('', '/=', Operator::RIGHT)
			->add('', '%=', Operator::RIGHT)
			->add('', '+=', Operator::RIGHT)
			->add('', '-=', Operator::RIGHT)
			->add('', '<<=', Operator::RIGHT)
			->add('', '>>=', Operator::RIGHT)
			->add('', '&=', Operator::RIGHT)
			->add('', '^=', Operator::RIGHT)
			->add('', '|=', Operator::RIGHT)
			->add('', '&&=', Operator::RIGHT)
			->add('', '||=', Operator::RIGHT)
		;

		return $table;
	}

	public function __construct()
	{

	}

	public function addGroup()
	{

	}
}
