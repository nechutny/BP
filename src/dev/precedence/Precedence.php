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


	protected $tableMap = [ '+', '-', '*', '/', '.', '<', '>', '<=', '>=', '===', '!==', '&&', '||', 'and', 'or', '!', '(', ')', 'func', ',', '$', 'i' ];
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
	];

	/*
	protected $tableMap = [ '+', '*', '(', ')', 'i', '$' ];
	protected $table = [
		// +    *    (    )    i    $
		[ '>', '<', '<', '>', '<', '>' ], // +
		[ '>', '>', '<', '>', '<', '>' ], // *
		[ '<', '<', '<', '=', '<', '#' ], // (
		[ '>', '>', '#', '>', '#', '>' ], // )
		[ '>', '>', '#', '>', '#', '>' ], // i
		[ '<', '<', '<', '#', '<', '#' ], // $
	];*/

	protected $rules = [
		[
			'source'	=> ['E','+','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','-','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['-','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','*','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','/','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','.','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','<','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','>','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','<=','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','>=','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','===','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','!==','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','&&','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','||','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','and','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','or','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['!','E'],
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
		$token = $this->scanner->next();
		$stack = new Stack();
		$stack->push('$');
		do
		{
			$a = $stack->topTerminal();

			switch($this->getFromTable($a, $token))
			{
				case '=':
					$stack->push($token);
					$token = $this->scanner->next();
					break;

				case '<':
					$stack->pushTerminal('<');

					$stack->push($token);
					$token = $this->scanner->next();
					break;

				case '>':
					// Hledáme pravidlo!

					$found = false;

					foreach($this->rules as $key => $rule)
					{
						$error = false;

						for($i = 0; $i < count($rule['source']); $i++ )
						{
							$tmp = $stack->top($i);

							if(is_array($tmp))
							{
								$tmp = $this->normalizeCodes($tmp['value']);
							}

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
						echo "Error - rule not found!\n";
					}
					else
					{
						echo "OK - rule: ".$key." ".$rule['target']." -> ".implode(' ', $rule['source'])."\n";
					}
					break;

				case '#':
					var_dump($token);
					echo "Chyba! - $a";
					die();
					break;

				default:
					echo "Chyba v precendenční tabulce";

			}

			//$stack->debug();

		} while($this->normalizeCodes($token['value']) != '$' || $stack->topTerminal() != '$'  );

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

		if(is_array($token))
		{
			$token = $this->normalizeCodes($token['value']);
		}

		if(is_array($stack))
		{
			$stack = $this->normalizeCodes($stack['value']);
		}

		$tmp = $this->table[ $this->tableMap[$stack] ][ $this->tableMap[$token] ];

		return $tmp;
	}
}