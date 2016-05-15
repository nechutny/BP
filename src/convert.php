#!/bin/env php
<?php
/*
 * Copyright 2016 Stanislav Nechutný
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if(PHP_VERSION_ID < 50501)
{
	echo "Neni splnena minimalni verze PHP 5.5";
	exit(15);
}

require_once(__DIR__ . '/lib/debug.php');
require_once(__DIR__ . '/scanner/Scanner.php');
require_once(__DIR__ . '/parser/Parser.php');
require_once(__DIR__ . '/precedence/Precedence.php');

if($argc < 2)
{
	die("Wrong argument count. Usage is:
	./app source.php [--precedence]
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

	$code = $parser->generator->getCode();

	file_put_contents('test.cpp', $code);

	echo $code;

	echo "\n\n\nDone\n";
}
catch(PrecedenceException $e)
{
	echo "Precedence errror\n";

	echo $e;

	exit(5);
}
catch(ParserError $e)
{
	echo "Parser errror\n";

	echo $e;

	exit(5);
}
