<?php
require_once(__DIR__ . '/Stack.php');


class Precedence
{

	protected $scanner;

	protected $stopTokens = [T_COMMA, T_SEMICOLON];

	protected $result = NULL;

	protected $variables = [];


	protected $tableMap = [ '+', '-', '*', '/', '**', '.', '<', '>', '<=', '>=', '===', '!==', '&&', '||', '=', 'and', 'or', '!', '(', ')', 'func', ',', '$', 'i' ];
	protected $table = [
		// +    -    *    /   **    .    <    >   <=   >=   ===  !==  &&   ||    =   and  or    !    (    )   func  ,    $   var
		[ '>', '>', '<', '<', '<', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // +
		[ '>', '>', '<', '<', '<', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // -
		[ '>', '>', '>', '>', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // *
		[ '>', '>', '>', '>', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // /
		[ '>', '>', '>', '>', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '>', '<', '>', '>', '<'],  // **
		[ '>', '>', '>', '>', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // .
		[ '<', '<', '<', '<', '<', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // <
		[ '<', '<', '<', '<', '<', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // >
		[ '<', '<', '<', '<', '<', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // <=
		[ '<', '<', '<', '<', '<', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // >=
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // ===
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // !==
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // &&
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // ||
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // =
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // and
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '>', '<', '<', '>', '<', '>', '>', '<'],  // or
		[ '>', '>', '>', '>', '<', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '<', '<', '>', '<', '>', '>', '<'],  // !
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '=', '<', '=', '#', '<'],  // (
		[ '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '#', '>', '#', '>', '>', '#'],  // )
		[ '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '=', '#', '#', '#', '#', '#'],  // func
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '=', '<', '=', '#', '<'],  // ,
		[ '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '<', '#', '<', '#', '#', '<'],  // $
		[ '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '>', '#', '#', '>', '#', '>', '>', '#']   // var
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
			'source'	=> ['E','=','E'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['E','==','E'],
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
			'source'	=> ['E','**','E'],
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

		// TODO: handle functions with arguments
		[
			'source'	=> ['func','(','E',')'],
			'target'	=> 'E',
		],
		[
			'source'	=> ['func','(',')'],
			'target'	=> 'E',
		],

		[
			'source'	=> ['i'],
			'target'	=> 'E',
		],
	];

	public function __construct($scanner)
	{
		$this->tableMap = array_flip($this->tableMap);

		$this->scanner = $scanner;
	}

	public function run()
	{

		$this->variables = [];

		$token = $this->scanner->next(TRUE);
		$stack = new Stack();
		$stack->push('$');
		do
		{
			$a = $stack->topTerminal();

			try
			{

				switch ($this->getFromTable($a, $token))
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

						$found = FALSE;

						foreach ($this->rules as $key => $rule)
						{
							$error = FALSE;

							for ($i = 0; $i < count($rule['source']); $i++)
							{
								$tmp = $stack->top($i);

								if (is_array($tmp) && !isset($tmp['nonTerminal']))
								{
									$tmp = $this->normalizeCodes($tmp);
								}

								if (
										(is_array($tmp) && $tmp['nonTerminal'] != $rule['source'][count($rule['source']) - 1 - $i])
										||
										(!is_array($tmp) && $tmp != $rule['source'][count($rule['source']) - 1 - $i])
								)
								{
									$error = TRUE;
									$i = count($rule['source']);
								}
							}

							$error = ($error || $stack->top(count($rule['source'])) != '<');

							if (!$error)
							{
								$result = [];
								foreach ($rule['source'] as $unused)
								{
									$result[] = $stack->pop();
								}

								$stack->pop();

								$stack->push(['nonTerminal' => $rule['target'], 'terminals' => $result]);

								$found = TRUE;

								break;
							}
						}

						if (!$found)
						{
							$stack->debug();
							throw new PrecedenceException("Error - rule not found! " . print_r($token, TRUE) . " " . print_r($a, TRUE));
						}
						else
						{
							echo "OK - rule: " . $key . " " . $rule['target'] . " -> " . implode(' ', $rule['source']) . "\n";
						}
						break;

					case '#':
						throw new PrecedenceException(print_r($a, TRUE) . print_r($token, TRUE));

					default:
						die("Chyba v precedenční tabulce");

				}
			}
			catch(PrecedenceNotInTableException $e)
			{
				$this->scanner->back();
				$token = ['code' => T_SEMICOLON, 'value' => ';'];
			}

			//$stack->debug();

		} while($this->normalizeCodes($token) != '$' || $stack->topTerminal() != '$'  );

		//print_r($stack->top());



		$this->result = $stack->top();
	}

	/**
	 * Generate C++ code of parsed expression
	 *
	 * @return string|void
	 * @throws PrecedenceException
	 * @throws PrecedenceUsageException
	 */
	public function getCode()
	{
		if(is_null($this->result))
		{
			throw new PrecedenceUsageException("Run first method 'run' ");
		}

		return $this->recursiveCode($this->result);
	}

	/**
	 * Recursive expression source code generation
	 *
	 * @param array $data Structure of expression to generate
	 * @return string|void
	 * @throws PrecedenceException
	 */
	protected function recursiveCode(array $data)
	{
		$result = '';

		$op = $data;

		if(isset($op['nonTerminal']))
		{
			// Pow
			if(count($op['terminals']) == 3 && isset($op['terminals'][1]) && isset($op['terminals'][1]['value']) && $op['terminals'][1]['value'] == '**')
			{
				return ' pow('.$this->recursiveCode($op['terminals'][2]).','.$this->recursiveCode($op['terminals'][0]).')';
			}
			// Concatenation
			elseif(count($op['terminals']) == 3 && isset($op['terminals'][1]) && isset($op['terminals'][1]['value']) && $op['terminals'][1]['value'] == '.')
			{
				return ' std::string('.$this->recursiveCode($op['terminals'][2]).').append('.$this->recursiveCode($op['terminals'][0]).')';
			}
			// OR, XOR, AND keywords, except ',' lowest priority (not (!!!) same as ||, && etc.)
			elseif(count($op['terminals']) == 3 && isset($op['terminals'][1]) && isset($op['terminals'][1]['value']) && in_array(strtoupper($op['terminals'][1]['value']), ['AND','OR','XOR']))
			{
				$op['terminals'][1]['value'] = str_replace(['AND', 'OR', 'XOR'],['&&', '||', '^'],strtoupper($op['terminals'][1]['value']));

				return '('.$this->recursiveCode($op['terminals'][2]).') '.$op['terminals'][1]['value'].' ('.$this->recursiveCode($op['terminals'][0]).')';
			}

			foreach(array_reverse($op['terminals']) as $term)
			{
				$result .= $this->recursiveCode($term);
			}
		}
		elseif(isset($op['value']))
		{
			if($op['code'] == T_VARIABLE)
			{
				return ' phpVar_'.substr($op['value'],1);
			}

			$result .= ' '.$op['value'];
		}
		else
		{
			throw new PrecedenceException('Got unexpected structure.');
		}

		return $result;
	}

	/**
	 * Get all variables used in expression
	 *
	 * @return array
	 */
	public function getUsedVariables()
	{
		return array_keys($this->variables);
	}

	protected function normalizeCodes($code)
	{
		if($code['code'] == T_VARIABLE)
		{
			$this->variables[ $code['value'] ] = TRUE;
		}

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

			T_ASSIGN		=> '=',
			T_PLUS_EQUAL	=> '=',
			T_MINUS_EQUAL	=> '=',
			T_MUL_EQUAL		=> '=',
			T_POW_EQUAL		=> '=',
			T_DIV_EQUAL		=> '=',
			T_CONCAT_EQUAL	=> '=',
			T_POW_EQUAL		=> '=',
			T_OR_EQUAL		=> '=',
			T_XOR_EQUAL		=> '=',
			T_SL_EQUAL		=> '=',
			T_SR_EQUAL		=> '=',

			// TODO
			T_LNUMBER => 'i',
			T_DNUMBER => 'i',
			T_VARIABLE => 'i',
			T_CONSTANT_ENCAPSED_STRING => 'i',
			T_SEMICOLON => '$',

			T_STRING => 'func',
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
			throw new PrecedenceNotInTableException('Not in precedence table '.print_r($stack,TRUE)." ".print_r($token,TRUE));
		}


		$tmp = $this->table[ $this->tableMap[$stack] ][ $this->tableMap[$token] ];

		return $tmp;
	}
}

class PrecedenceUsageException extends Exception {

}

class PrecedenceException extends Exception
{

}

class PrecedenceNotInTableException extends Exception
{

}