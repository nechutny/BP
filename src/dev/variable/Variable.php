<?php

class Variable
{
	const TYPE_UNKNOWN = 'unknown';
	const TYPE_BOOL = 'boolean';
	const TYPE_INT = 'int';
	const TYPE_DOUBLE = 'double';
	const TYPE_STRING = 'string';
	const TYPE_ARRAY = 'array';
	const TYPE_OBJECT = 'object';

	/**
	 * @var string $name Variable name
	 */
	protected $name;

	/**
	 * @var array $type Variable type
	 */
	protected $type = [];

	/**
	 * @var int $special Is something special? (eg. function argument)
	 */
	protected $special;

	/**
	 * @var null|int $lineIsset Line where is set
	 */
	protected $lineIsset = NULL;

	/**
	 * @var bool $isUsed Was variable used in expression?
	 */
	protected $isUsed = FALSE;

	public function setType(array $tree, $type)
	{
		$ptr = &$this->type;

		foreach ($tree as $i)
		{
			if(!isset($ptr[ $i ]))
			{
				$ptr[ $i ] = [];
			}

			$ptr = & $ptr[ $i ];
		}

		$ptr = $type;
	}

	public function isOneType()
	{
		try {
			$type = $this->recursiveType($this->type, NULL);

		}
		catch (NotOneTypeException $e)
		{
			return NULL;
		}

		return $type;
	}

	protected function recursiveType($tree, $type = NULL)
	{
		if(!is_array($tree))
		{
			if($tree != $type)
			{
				if(is_null($type))
				{
					return $tree;
				}
				elseif($type == 'int' && $tree = 'float')
				{ // Int can be assigned to double
					return $tree;
				}
				else
				{
					throw new NotOneTypeException();
				}
			}

			return $type;
		}

		foreach ($tree as $val)
		{
			$type = $this->recursiveType($val, $type);
		}

		return $type;
	}

	public function debug()
	{
		echo "\n\nVariable name: ".$this->name."\nUsed:".$this->isUsed." \nIsset:".$this->lineIsset."\nTypes: ";
		print_r($this->type);
		echo "-----\n";
	}

	public function __construct($name, $type = Variable::TYPE_UNKNOWN)
	{
		$this->name = $name;
	}
}

class NotOneTypeException extends Exception
{

}