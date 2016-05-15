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

class VariableGenerator
{
	/**
	 * @var string Which C++ class use for dynamic types
	 */
	static $varClass = 'Php::Value';
	//static $varClass = 'php2cpp::Value';

	const TYPE_MIXED = '%varClass%';
	const TYPE_STRING = 'std::string';
	const TYPE_FLOAT = 'double';
	const TYPE_INT = 'long';
	const TYPE_ARRAY = '%varClass%';

	protected $name;

	protected $type;

	/**
	 * @var Variable
	 */
	protected $variable;

	protected $argumentNum;
	protected $argumentDefaultValue = NULL;

	/**
	 * VariableGenerator constructor.
	 *
	 * @param string $name Variable name with $ at beginning
	 * @param null|string $type Variable type (from TYPE_* constants)
	 */
	public function __construct($name, $type = NULL)
	{
		$this->name = substr($name,1); // Remove $ from beginning

		$this->type = is_null($type) ? self::TYPE_MIXED : $type;
	}

	public function getName()
	{
		return $this->name;
	}

	/**
	 * If is variable from function arguments and have default value, then set it here
	 *
	 * @param mixed $value Default value
	 */
	public function setArgumentDefaultValue($value)
	{
		if($value == 'NULL')
		{
			$value = 'nullptr';
		}
		elseif($value == '[')
		{
			$value = NULL;
		}

		$this->argumentDefaultValue = $value;
	}

	/**
	 * Set variable as function argument
	 *
	 * @param int $num Argument position (indexed from 0)
	 */
	public function assignArgument($num)
	{
		$this->argumentNum = $num;
	}

	public function setVariable(Variable $var)
	{
		$this->variable = $var;
	}

	/**
	 * Generate variable definition code
	 *
	 * @return string C++ code
	 */
	public function getCode()
	{
		if(!is_null($this->variable))
		{
			$type = $this->variable->isOneType();
			if($type)
			{
				switch($type)
				{
					case Type::TYPE_INT:
						$this->type = self::TYPE_INT;
						break;
					case Type::TYPE_FLOAT:
						$this->type = self::TYPE_FLOAT;
						break;
					case Type::TYPE_STRING:
						$this->type = self::TYPE_STRING;
						break;
				}
			}
		}

		if(!is_null($this->argumentNum))
		{
			if(is_null($this->argumentDefaultValue))
			{
				$argument = ' = args['.$this->argumentNum.']';
			}
			else
			{
				$argument = '; if(args.size() > '.$this->argumentNum.') phpVar_'.$this->name.' = args['.$this->argumentNum.']; else phpVar_'.$this->name.' = '.$this->argumentDefaultValue.' ';
			}

		}
		else
		{
			if($this->type == self::TYPE_INT)
			{
				$argument = ' = 0';
			}
			elseif($this->type == self::TYPE_FLOAT)
			{
				$argument = ' = 0.0';
			}
			else
			{
				$argument = ' = nullptr';
			}

		}

		$this->type = str_replace('%varClass%', self::$varClass, $this->type);

		return "\t".$this->type.' phpVar_'.$this->name.$argument.';'."\n";
	}

}
