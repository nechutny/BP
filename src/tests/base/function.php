<?php

/**
 * Test function
 *
 * @param string $z
 * @param array $bar
 *
 * @return string
 */
function foo($z, array $bar = NULL)
{
	$x = 15;
	$y = ($x+15)**4.4;

	bar($z);
	$ccccc = bar();

	bar(1,2,3);


	return 15;
	//return "aaaa $y $bar";
}

function bar($x, $y = 15, array $z = [], $a = "aaa")
{
	# aaa
	echo "123";

	/* ccc */

	return $x*$y;
}
