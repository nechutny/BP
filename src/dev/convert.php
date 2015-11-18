#!/bin/env php
<?php

if($argc != 2)
{
	die("Wrong argument count. Usage is:
	./app source.php
	or
	./app folder");
}


$file = file_get_contents($argv[1]);
$tokens = token_get_all($file);


/*
 * Include all supported token classes
 */
require_once(__DIR__ . '/token/AToken.php');
foreach(glob(__DIR__ . "/token/T_*.php") as $tokenFile)
{
	require_once($tokenFile);
}

$tok = NULL;

foreach($tokens as $token)
{
	if(is_array($token))
	{
		$tname = token_name($token[0]);
		$cname = '\Tokens\token_'.$tname;

		if(class_exists($cname))
		{
			echo "implemented - ";

			$prev = $tok[ count($tok) -1];
			$tok[] = new $cname($prev);
		}

		echo $tname." - ".$token[1]."\n";

	}
	else
	{
		echo $token."\n";
	}

}

print_r($tok);