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

	public function assignArgument($num)
	{
		$this->argumentNum = $num;
	}

	public function getCode()
	{
		$argument = '';
		if(!is_null($this->argumentNum))
		{
			$argument = ' = args['.$this->argumentNum.']';
		}

		return "\t".$this->type.' phpVar_'.$this->name.$argument.';'."\n";
	}

}
