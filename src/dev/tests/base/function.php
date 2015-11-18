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
	$y = $x+15*4.4;

	bar($z);

	// return
	return "aaaa $y $bar";
}

function bar($x, $y = 15, array $z = [], $a = "aaa")
{
	# aaa
	echo "123";

	/* ccc */

	return $x*$y;
}