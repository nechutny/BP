<?php
require_once( __DIR__ . '/../variable/Type.php');
require_once( __DIR__ . '/../variable/Variable.php');
require_once( __DIR__ . '/../variable/TypeDetector.php');
require_once( __DIR__ . '/../variable/BuiltInFunctions.php');



class ExprGenerator
{
	protected $data = [];
	protected $varScope = NULL;

	public function __construct(array $data, Scope $scope)
	{
		$this->data = $data;
		$this->varScope = $scope;
	}

	public function analyse($tree)
	{
		// First analyse - original types
		$this->recursiveAnalyse($this->data);

		// Second analyse - expected output types
		$this->outputTypeAnalyse($this->data, NULL, $tree);

		echo "\n\nAnalyse tree: \n";
		print_r($this->data);
		echo "\n\n\n";
	}

	public function recursiveAnalyse(array &$data)
	{
		if(isset($data['nonTerminal']))
		{
			foreach($data['terminals'] as &$trm)
			{
				$this->recursiveAnalyse($trm);
			}
			$type = TypeDetector::analyseExpression($data['terminals'], $this->varScope);
			$data['type'] = $type;

		}
		elseif(isset($data['value']))
		{
			$data['type'] = Type::TYPE_MIXED;

			if($data['code'] == T_VARIABLE)
			{
				if(!isset($this->varScope[ $data['value'] ]))
				{
					$this->varScope[ $data['value'] ] = new Variable( $data['value'] );
				}
			}
			elseif($data['code'] == T_DNUMBER)
			{
				$data['type'] = Type::TYPE_FLOAT;
			}
			elseif($data['code'] == T_LNUMBER)
			{
				$data['type'] = Type::TYPE_INT;
			}
			elseif($data['code'] == T_CONSTANT_ENCAPSED_STRING)
			{
				$data['type'] = Type::TYPE_STRING;
			}
			elseif($data['code'] == T_STRING)
			{
				$data['type'] = FunctionReturnType::get($data['value']);
			}

		}
		else
		{
			throw new PrecedenceException('Got unexpected structure.  '.print_r($data));
		}
	}

	public function outputTypeAnalyse(array &$data, array $parent = NULL, array $tree = [])
	{
		if(isset($data['nonTerminal']))
		{
			if(isset($data['terminals'][2]) && isset($data['terminals'][1]['code']) && $data['terminals'][1]['code'] == T_ASSIGN)
			{ // Assign copy type from source to target
				$data['type'] = $data['terminals'][0]['type'];
			}

			if(!is_null($parent))
			{
				if($parent['type'] == Type::TYPE_NO_PROPAGATE)
				{
					$data['outType'] = $data['type'];
				}
				else
				{
					$data['outType'] = $parent['type'];
				}

			}
			else
			{ // top
				$data['outType'] = $data['type'];
			}


			foreach($data['terminals'] as $key => &$trm)
			{
				$treeCopy = $tree;
				$treeCopy[] = $key;
				$this->outputTypeAnalyse($trm, $data, $treeCopy);
			}
		}
		elseif(isset($data['value']))
		{
			$data['outType'] = $parent['outType'];

			if($data['code'] == T_VARIABLE)
			{
				$this->varScope[ $data['value'] ]->setType($tree, $data['outType'] );
			}
		}
		else
		{
			throw new PrecedenceException('Got unexpected structure.  '.print_r($data, true));
		}
	}


	public function getCode()
	{

		return $this->recursiveCode($this->data);
	}

	protected function stringOperator($op)
	{
		$arg = $this->recursiveCode($op);

		$arg = 'php2cpp::to_string('.$arg.')';

		return $arg;
	}

	protected function doubleOperator($op)
	{
		$arg = $this->recursiveCode($op);

		return 'php2cpp::to_float('.$arg.')';
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

		// TODO: Rewrite it!
		if(isset($op['nonTerminal']))
		{
			// Pow
			if(count($op['terminals']) == 3 && isset($op['terminals'][1]) && isset($op['terminals'][1]['value']) && $op['terminals'][1]['value'] == '**')
			{
				return ' pow('.$this->doubleOperator($op['terminals'][2]).','.$this->doubleOperator($op['terminals'][0]).')';
			}
			// Concatenation
			elseif(count($op['terminals']) == 3 && isset($op['terminals'][1]) && isset($op['terminals'][1]['value']) && $op['terminals'][1]['value'] == '.')
			{
				$arg1 = $this->stringOperator($op['terminals'][2]);
				$arg2 = $this->stringOperator($op['terminals'][0]);
				return ' std::string( '.$arg1.').append( '.$arg2.')';
			}
			// OR, XOR, AND keywords, except ',' lowest priority (not (!!!) same as ||, && etc.)
			elseif(count($op['terminals']) == 3 && isset($op['terminals'][1]) && isset($op['terminals'][1]['value']) && in_array(strtoupper($op['terminals'][1]['value']), ['AND','OR','XOR']))
			{
				$op['terminals'][1]['value'] = str_replace(['AND', 'OR', 'XOR'],['&&', '||', '^'],strtoupper($op['terminals'][1]['value']));

				return '('.$this->recursiveCode($op['terminals'][2]).') '.$op['terminals'][1]['value'].' ('.$this->recursiveCode($op['terminals'][0]).')';
			}
			// Convert !== and === to != and ==
			elseif(count($op['terminals']) == 3 && isset($op['terminals'][1]) && isset($op['terminals'][1]['value']) && in_array($op['terminals'][1]['value'], ['===','!==']))
			{
				$op['terminals'][1]['value'] = str_replace(['!==', '==='],['!=', '=='],strtoupper($op['terminals'][1]['value']));

				return $this->recursiveCode($op['terminals'][2]).' '.$op['terminals'][1]['value'].' '.$this->recursiveCode($op['terminals'][0]);
			}
			// mul, div, sub, add, ...
			elseif(count($op['terminals']) == 3 && isset($op['terminals'][1]) && isset($op['terminals'][1]['value']) && in_array($op['terminals'][1]['value'], ['/', '*', '+', '-', '%']))
			{
				$arg1 = $op['terminals'][2];
				$arg2 = $op['terminals'][0];

				if($op['type'] != Type::TYPE_INT)
				{
					$arg1 = $this->doubleOperator($arg1);
					$arg2 = $this->doubleOperator($arg2);
				}
				else
				{
					$arg1 = $this->recursiveCode($arg1);
					$arg2 = $this->recursiveCode($arg2);
				}


				return ''.$arg1.' '.$op['terminals'][1]['value'].' '.$arg2.' ';
			}
			// Function call
			elseif( count($op['terminals']) >= 3 &&
				isset($op['terminals'][0]['code']) && isset($op['terminals'][ count($op['terminals']) - 2 ]['code']) && isset($op['terminals'][ count($op['terminals']) - 1 ]['code']) &&
				$op['terminals'][0]['code'] == T_RPARENTHESIS &&
				$op['terminals'][ count($op['terminals']) - 2 ]['code'] == T_LPARENTHESIS &&
				$op['terminals'][ count($op['terminals']) - 1 ]['code'] == T_STRING
			)
			{
				// Arguments to string
				$args = '';
				$copy = $op['terminals'];
				$len = count($copy);

				// unset function name and parenthesis
				unset($copy[0]);
				unset($copy[ $len - 1 ]);
				unset($copy[ $len - 2 ]);

				foreach(array_reverse($copy) as $term)
				{
					$args .= ', '.$this->recursiveCode($term);
				}

				// Function call
				return 'Php::call("'.$op['terminals'][ count($op['terminals']) - 1 ]['value'].'"'.$args.')';
			}

			foreach(array_reverse($op['terminals']) as $term)
			{
				$result .= $this->recursiveCode($term);
			}
		}
		elseif(isset($op['value']))
		{
			if(in_array($op['code'], [ T_LNUMBER, T_DNUMBER, T_CONSTANT_ENCAPSED_STRING]))
			{
				// Value

				$outVal = $op['value'];
				if($op['code'] == T_CONSTANT_ENCAPSED_STRING)
				{
					$outVal = substr($outVal, 1, -1);
				}

				if($op['outType'] == Type::TYPE_STRING)
				{

					if(!in_array($op['type'], [Type::TYPE_INT, Type::TYPE_FLOAT]))
					{
						$outVal = '"' . $outVal . '"';
					}
				}
				elseif($op['outType'] == Type::TYPE_INT)
				{
					$outVal = (int)$outVal;
				}
				elseif($op['outType'] == Type::TYPE_FLOAT)
				{
					$outVal = (float)$outVal;
				}

				$result .= ''.$outVal;
			}
			elseif($op['code'] == T_VARIABLE)
			{
				// Variable

				return ' phpVar_'.substr($op['value'],1);
			}
			elseif($data['code'] == T_STRING)
			{
				return 'Php::constant("'.$data['value'].'")';
			}
			else
			{

				// Operators

				return ' '.$op['value'];
			}


		}
		else
		{
			throw new PrecedenceException('Got unexpected structure.');
		}

		return $result;
	}

}
