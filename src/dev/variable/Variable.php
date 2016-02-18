<?php

class Variable
{
	const TYPE_UNKNOWN = 1;
	const TYPE_BOOL = 2;
	const TYPE_INT = 4;
	const TYPE_DOUBLE = 8;
	const TYPE_STRING = 16;
	const TYPE_ARRAY = 32;
	const TYPE_OBJECT = 64;

	/**
	 * @var string $name Variable name
	 */
	protected $name;

	/**
	 * @var int $type Variable type
	 */
	protected $type;

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

	public function __construct($name, $type = 0)
	{
		$this->name = $name;
		$this->type = $type;
	}
}