#!/bin/env php
<?php
require_once(__DIR__ . '/scanner/Scanner.php');
require_once(__DIR__ . '/parser/Parser.php');
require_once(__DIR__ . '/precedence/Precedence.php');

if($argc < 2)
{
	die("Wrong argument count. Usage is:
	./app source.php [--precendence]
	or
	./app folder");
}

try
{
	$scanner = new Scanner($argv[1]);

	if($argc > 2 AND $argv[2] == '--precendence')
	{
		$scanner->next(); // Skip open tag

		$precendence = new Precedence($scanner);
		$precendence->run();
	}
	else
	{
		$parser = new Parser($scanner);

		$parser->parse_file();
	}
}
catch(EndOfFileException $e)
{
	echo "Done\n";
}
/*catch(ParserError $e)
{
	echo $e;
}*/