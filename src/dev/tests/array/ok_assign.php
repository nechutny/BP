<?php

function main()
{
	$foo = array("a", "b", "c", "d");
	
	$foo[15] = "b";

	$foo[1] = $foo[2] = $foo[3] = 1.5;
	
	return $foo;
}
