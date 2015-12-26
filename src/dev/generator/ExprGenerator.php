<?php

class ExprGenerator
{
	protected $data = [];

	public function __construct(array $data)
	{
		$this->data = $data;
	}


	public function getCode()
	{
		return $this->recursiveCode($this->data);
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
				return ' pow('.$this->recursiveCode($op['terminals'][2]).','.$this->recursiveCode($op['terminals'][0]).')';
			}
			// Concatenation
			elseif(count($op['terminals']) == 3 && isset($op['terminals'][1]) && isset($op['terminals'][1]['value']) && $op['terminals'][1]['value'] == '.')
			{
				return ' std::string( (const char*)'.$this->recursiveCode($op['terminals'][2]).').append( (const char*)'.$this->recursiveCode($op['terminals'][0]).')';
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
			// Divide - typecast to float
			elseif(count($op['terminals']) == 3 && isset($op['terminals'][1]) && isset($op['terminals'][1]['value']) && $op['terminals'][1]['value'] == '/')
			{
				return '(float)('.$this->recursiveCode($op['terminals'][2]).') '.$op['terminals'][1]['value'].' (float)('.$this->recursiveCode($op['terminals'][0]).')';
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

}
