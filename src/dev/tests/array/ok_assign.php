<?php

function main()
{
	$foo = array();
	
	$foo[15] = "b";
	
	$foo[1] = $foo[2] = $foo[3] = 1.5;
	
	return $foo;
}
