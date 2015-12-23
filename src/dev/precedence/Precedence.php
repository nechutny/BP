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
		$token = $this->scanner->next(TRUE);
		$stack = new Stack();
		$stack->push('$');
		do
		{
			$a = $stack->topTerminal();

			switch($this->getFromTable($a, $token))
			{
				case '=':
					$stack->push($token);
					$token = $this->scanner->next(TRUE);
					break;

				case '<':
					$stack->pushTerminal('<');

					$stack->push($token);
					$token = $this->scanner->next(TRUE);
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
								$tmp = $this->normalizeCodes($tmp);
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
						throw new PrecendenceException("Error - rule not found! ".print_r($token, true)." ".print_r($a,true));
					}
					else
					{
						echo "OK - rule: ".$key." ".$rule['target']." -> ".implode(' ', $rule['source'])."\n";
					}
					break;

				case '#':
					throw new PrecendenceException(print_r($a,true).print_r($token,true));

				default:
					die("Chyba v precendenční tabulce");

			}

			//$stack->debug();

		} while($this->normalizeCodes($token) != '$' || $stack->topTerminal() != '$'  );

	}

	protected function normalizeCodes($code)
	{


		if($code['code'] == T_STRING)
		{ // TODO
			if(strtoupper($code['value']) == 'TRUE')
			{
				return 'i';
			}

			if(strtoupper($code['value']) == 'FALSE')
			{
				return 'i';
			}

			if(strtoupper($code['value']) == 'NULL')
			{
				return 'i';
			}
		}

		$map = [
			T_LOGICAL_AND => 'and',
			T_LOGICAL_OR => 'or',
			T_LOGICAL_XOR => 'xor',

			T_BOOLEAN_AND => '&&',
			T_BOOLEAN_OR => '||',

			// TODO
			T_LNUMBER => 'i',
			T_DNUMBER => 'i',
			T_VARIABLE => 'i',
			T_CONSTANT_ENCAPSED_STRING => 'i',
			T_SEMICOLON => '$',
		];

		if(isset($map[ $code['code'] ]))
		{
			return $map[ $code['code'] ];
		}

		//var_dump($code);

		return $code['value'];
	}

	protected function getFromTable($stack, $token)
	{

		if(is_array($token))
		{
			$token = $this->normalizeCodes($token);
		}

		if(is_array($stack))
		{
			$stack = $this->normalizeCodes($stack);
		}

		if(!isset($this->tableMap[$stack]) || !isset($this->table[ $this->tableMap[$stack] ]) || !isset($this->tableMap[$token]) || !isset($this->table[ $this->tableMap[$stack] ][ $this->tableMap[$token] ]))
		{
			throw new PrecendenceException('Not in precendence table '.print_r($stack,TRUE)." ".print_r($token,TRUE));
		}


		$tmp = $this->table[ $this->tableMap[$stack] ][ $this->tableMap[$token] ];

		return $tmp;
	}
}

class PrecendenceException extends Exception
{

}