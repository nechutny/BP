<?php

class Scope implements ArrayAccess, Iterator, Countable
{
	/**
	 * @var array $variables Variables defined in scope
	 */
	protected $variables = [];

	protected $position = 0;

	public function __construct()
	{
	}

	/* === Iterator ================================================================================================== */

	public function next()
	{
		++$this->position;
	}

	public function current()
	{
		return $this->variables[ array_keys($this->variables )[ $this->position ] ];
	}

	public function rewind()
	{
		$this->position = 0;
	}

	public function valid()
	{
		return ($this->position < count($this->variables));
	}

	public function key()
	{
		return array_keys($this->variables )[ $this->position ];
	}

	/* === Countable ================================================================================================= */

	public function count()
	{
		return count($this->variables);
	}

	/* === Array Access ============================================================================================== */

	public function offsetGet($offset)
	{
		if(!$this->offsetExists($offset))
		{
			throw new InvalidArgumentException('Variable "'.$offset.'" not set."');
		}
		return $this->variables[ $offset ];
	}

	public function offsetSet($offset, $value)
	{
		$this->variables[ $offset ] = $value;
	}

	public function offsetExists($offset)
	{
		return isset($this->variables[ $offset ]);
	}

	public function offsetUnset($offset)
	{
		if(!$this->offsetExists($offset))
		{
			throw new InvalidArgumentException('Variable "'.$offset.'" not set."');
		}

		unset($this->variables[ $offset ]);
	}

}
