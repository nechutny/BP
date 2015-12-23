<?php
require_once(__DIR__ . '/../lib/Stack.php');


class Precedence
{
	const CONTEXT_RETURN = 1;
	const CONTEXT_ASSIGN = 2;
	const CONTEXT_IF = 3;
	const CONTEXT_ECHO = 3;

	protected $scanner;

	protected $stopTokens = [T_COMMA, T_SEMICOLON];

	/*
	protected $table = [
		// +    -    *    /    .    <    >   <=   >=   ===  !==  &&   ||   and  or    !    (    )   func  ,    $   var
		[ '>', '>', '<', '<', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // +
		[ '>', '>', '<', '<', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // -
		[ '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // *
		[ '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // /
		[ '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // .
		[ '<', '<', '<', '<', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // <
		[ '<', '<', '<', '<', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // >
		[ '<', '<', '<', '<', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // <=
		[ '<', '<', '<', '<', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // >=
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // ===
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // !==
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // &&
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // ||
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // and
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '>', '<', '<', '>', '<', '>', '>', '<'],  // or
		[ '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // !
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '=', '<', '=', '#', '<'],  // (
		[ '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '#', '>', '#', '>', '>', '#'],  // )
		[ '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '=', '#', '#', '#', '#', '#'],  // func
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '=', '<', '=', '#', '<'],  // ,
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '#', '<', '#', '#', '<'],  // $
		[ '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '#', '#', '>', '#', '>', '>', '#']   // var
	];*/

	protected $tableMap = [ '+', '*', '(', ')', 'i', '$' ];

	protected $table = [
		// +    *    (    )    i    $
		[ '>', '<', '<', '>', '<', '>' ], // +
		[ '>', '>', '<', '>', '<', '>' ], // *
		[ '<', '<', '<', '=', '<', '#' ], // (
		[ '>', '>', '#', '>', '#', '>' ], // )
		[ '>', '>', '#', '>', '#', '>' ], // i
		[ '<', '<', '<', '#', '<', '#' ], // $
	];

	protected $rules = [
		[
			'source'	=> ['E','+','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','*','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['(','E',')'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['i'],
			'target'	=> 'E',
		]
	];

	public function __construct($scanner)
	{
		$this->tableMap = array_flip($this->tableMap);

		$this->scanner = $scanner;
	}

	public function run($context = 0)
	{
		$token = $this->normalizeCodes($this->scanner->next()['value']);
		$stack = new Stack();
		$stack->push('$');
		do
		{
			$a = $stack->topTerminal();

			switch($this->getFromTable($a, $token))
			{
				case '=':
					$stack->push($token);
					$token = $this->normalizeCodes($this->scanner->next()['value']);
					break;

				case '<':
					$stack->pushTerminal('<');

					$stack->push($token);
					$token = $this->normalizeCodes($this->scanner->next()['value']);
					break;

				case '>':
					// Hledáme pravidlo!

					$found = false;

					foreach($this->rules as $rule)
					{
						$error = false;

						for($i = 0; $i < count($rule['source']); $i++ )
						{
							$tmp = $stack->top($i);

							if($tmp != $rule['source'][ count($rule['source'])-1-$i ])
							{
								$error = true;
								$i = count($rule['source']);
							}
						}

						$error = ($error || $stack->top( count($rule['source'])) != '<');

						if(!$error)
						{
							foreach($rule['source'] as $unused)
							{
								$stack->pop();
							}

							$stack->pop();

							$stack->push($rule['target']);

							$found = true;

							break;
						}
					}

					if(!$found)
					{
						echo "Error - rule not found!";
					}
					else
					{
						echo "OK - rule: ".$rule['target']." -> ".implode(' ', $rule['source'])."\n";
					}
					break;

				case '#':
					var_dump($token);
					echo "Chyba! - $token $a";
					die();
					//$token = $this->normalizeCodes($this->scanner->next()['value']);
					break;

				default:
					echo "Chybaaaaa!";
					//$token = $this->normalizeCodes($this->scanner->next()['value']);
			}

			//$stack->debug();

		} while($token != '$' || $stack->topTerminal() != '$'  );

	}

	protected function normalizeCodes($code)
	{
		if(is_numeric($code))
		{
			return 'i';
		}

		if($code == ';')
		{
			return '$';
		}

		return $code;
	}

	protected function getFromTable($stack, $token)
	{

		$tmp = $this->table[ $this->tableMap[$stack] ][ $this->tableMap[$token] ];

		return $tmp;
	}
}