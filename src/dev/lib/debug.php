<?php

class debug
{
	const TYPE_PRECEDENCE = 'Precedence';
	const TYPE_PARSER = 'Parser';

	static function printString($type, $string)
	{
		echo str_pad($type, 12, " ")."| ".$string."\n";
	}

	static function printStack($type, Stack $stack)
	{
		echo str_pad($type, 12, " ")."| ";
		$stack->debug();
		echo "\n";
	}
}