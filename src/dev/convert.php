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

	if($argc > 2 AND $argv[2] == '--precedence')
	{
		$scanner->next(); // Skip open tag

		$precedence = new Precedence($scanner);
		$precedence->run();

		echo "Generated expression: ". $precedence->getCode()."\n";
	}
	else
	{
		$parser = new Parser($scanner);

		$parser->parse_file();
	}
}
catch(EndOfFileException $e)
{
	echo "Code:\n\n\n";

	echo $parser->generator->getCode();

	echo "\n\n\nDone\n";
}
catch(PrecedenceException $e)
{
	echo "Precendence errror\n";

	echo $e;

	exit(5);
}
catch(ParserError $e)
{
	echo "Parser errror\n";

	echo $e;

	exit(5);
}