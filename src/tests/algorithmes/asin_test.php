<?php
//include(__DIR__ . "/asin.php");

$start = microtime(TRUE);
for($i = 0; $i < 100; $i++)
{
	my_asin(0.99999);
}
echo microtime(TRUE)-$start;
