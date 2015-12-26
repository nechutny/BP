<?php

class VariableGenerator
{
	const TYPE_MIXED = 'Php::Value';
	const TYPE_STRING = 'std::string';
	const TYPE_FLOAT = 'double';
	const TYPE_INT = 'long';
	const TYPE_ARRAY = 'Php::Val';

	protected $name;

	protected $type;

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

	public function setArgumentDefaultValue($value)
	{
		if($value == 'NULL')
		{
			$value = 'nullptr';
		}

		$this->argumentDefaultValue = $value;
	}

	public function assignArgument($num)
	{
		$this->argumentNum = $num;
	}

	public function getCode()
	{

		if(!is_null($this->argumentNum))
		{
			if(is_null($this->argumentDefaultValue))
			{
				$argument = ' = args['.$this->argumentNum.']';
			}
			else
			{
				$argument = ' = args.size() > '.$this->argumentNum.' ? args['.$this->argumentNum.'] : '.$this->argumentDefaultValue.' ';
			}

		}
		else
		{
			$argument = ' = nullptr';
		}

		return "\t".$this->type.' phpVar_'.$this->name.$argument.';'."\n";
	}

}
