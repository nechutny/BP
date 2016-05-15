<?php
//include(__DIR__ . "/factorial.php");

$start = microtime(TRUE);
for($i = 0; $i < 100; $i++)
{
	factorial(5000000);
}
echo microtime(TRUE)-$start;
