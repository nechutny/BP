<?php

function main($arg)
{
	echo $arg."\n";
	echo strlen($arg);

	echo "\n";

	echo str_replace("a","b",$arg);
}
