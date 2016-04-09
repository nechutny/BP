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

class TypeDetector
{
	/*
	const TYPE_NULL = 'null';
	const TYPE_BOOLEAN = 'boolean';
	const TYPE_INT = 'int';
	const TYPE_FLOAT = 'float';
	const TYPE_STRING = 'string';
	const TYPE_ARRAY = 'array';
	const TYPE_MIXED = 'mixed';
	*/


	static function detectInput($value)
	{

	}

	static function detectOperator($operator, $op1 = NULL, $op2 = NULL)
	{
		if($operator == T_NULL)
		{
			return Type::TYPE_NULL;
		}
		elseif(in_array($operator, [
			T_IS_EQUAL,
			T_IS_NOT_EQUAL,
			T_IS_IDENTICAL,
			T_IS_NOT_IDENTICAL,
			T_IS_GREATER_OR_EQUAL,
			T_IS_SMALLER_OR_EQUAL,
			T_AND_EQUAL,
			T_OR_EQUAL,
			T_XOR_EQUAL,
			T_BOOLEAN_AND,
			T_BOOLEAN_OR,
			T_LOGICAL_AND,
			T_LOGICAL_OR,
			T_LOGICAL_XOR,
			T_BOOL_CAST,
			T_NEG,
			T_LESS,
			T_GREATER,
		]))
		{
			return Type::TYPE_BOOLEAN;
		}
		elseif(in_array($operator, [
			T_DIV_EQUAL,
			T_MINUS_EQUAL,
			T_MOD_EQUAL,
			T_MUL_EQUAL,
			T_PLUS_EQUAL,
			T_POW_EQUAL,
			T_POW,
			T_DEC,
			T_INC,
			T_DIV,
			T_MUL,
			T_PLUS,
			T_MINUS,
			T_MOD,
		]))
		{
			if(in_array($operator, [T_MINUS_EQUAL, T_MOD_EQUAL, T_MUL_EQUAL, T_PLUS_EQUAL, T_MUL, T_PLUS, T_MINUS]))
			{
				if( isset($op1) && isset($op2)
					&& ($op1['type'] == Type::TYPE_INT )
					&& ($op2['type'] == Type::TYPE_INT )
				)
				{
					return Type::TYPE_INT;
				}
			}

			// int vs double
			return Type::TYPE_FLOAT;
		}
		elseif(in_array($operator, [
			T_CONCAT_EQUAL,
			T_CONCAT
		]))
		{
			return Type::TYPE_STRING;
		}
		elseif ($operator == T_COMMA)
		{
			// TODO Correct types
			return Type::TYPE_NO_PROPAGATE;
		}

		return $operator;
	}

	static function analyseExpression($expr, Scope $scope)
	{
		$type = Type::TYPE_MIXED;

		if(!isset($expr['terminals']))
		{
			if(isset($expr[0]) && !isset($expr[0]['terminals']))
			{
				if($expr[0]['code'] == T_VARIABLE)
				{
					// TODO - detect from scope
					$type = Type::TYPE_MIXED;
				}
				elseif($expr[0]['code'] == T_LNUMBER)
				{
					$type = Type::TYPE_INT;
				}
				elseif($expr[0]['code'] == T_DNUMBER)
				{
					$type = Type::TYPE_FLOAT;
				}
				elseif($expr[0]['code'] == T_RPARENTHESIS)
				{
					$type = $expr[1]['type'];
				}
				elseif($expr[0]['code']== T_CONSTANT_ENCAPSED_STRING)
				{
					$type = Type::TYPE_STRING;
				}
				if($expr[0]['code'] == T_RPARENTHESIS)
				{ // Function call?
					if(count($expr) >= 3)
					{
						if(
							isset($expr[ count($expr) - 1 ]['code']) &&
							isset($expr[ count($expr) - 2 ]['code']) &&
							$expr[ count($expr) - 1 ]['code'] == T_STRING &&
							$expr[ count($expr) - 2 ]['code'] == T_LPARENTHESIS
						)
						{
							$type = FunctionReturnType::get( $expr[ count($expr) - 1 ]['value'] );
						}
					}
				}
			}
			else
			{
				if(isset($expr[1]) && isset($expr[2]))
				{
					$type = self::detectOperator($expr[1]['code'], $expr[0], $expr[2]);
				}
				elseif(isset($expr[1]) && isset($expr[1]['code']))
				{ // Neg, pre inc/dec
					$type = self::detectOperator($expr[1]['code']);
				}
				elseif(isset($expr[1]) && isset($expr[0]['code']))
				{ // post inc/dec
					$type = self::detectOperator($expr[0]['code']);
				}
			}
		}


		return $type;
	}



}
