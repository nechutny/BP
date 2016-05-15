<?php
//include(__DIR__ . "/fibonaci.php");

$start = microtime(TRUE);
for($i = 0; $i < 100; $i++)
{
	fibonaci(27);
}
echo microtime(TRUE)-$start;
