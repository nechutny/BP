#!/bin/env php
<?php
require_once(__DIR__ . '/scanner/Scanner.php');
require_once(__DIR__ . '/parser/Parser.php');

if($argc != 2)
{
	die("Wrong argument count. Usage is:
	./app source.php
	or
	./app folder");
}

try
{
	$scanner = new Scanner($argv[1]);

	$parser = new Parser($scanner);

	$parser->parse_file();
}
catch(EndOfFileException $e)
{
	echo "Done\n";
}
/*catch(ParserError $e)
{
	echo $e;
}*/