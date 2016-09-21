<?php

namespace Spellu\SyntaxTree;

class Identifier extends Node
{
	public $token;

	public function __construct($token)
	{
		$this->token = $token;
	}
}
