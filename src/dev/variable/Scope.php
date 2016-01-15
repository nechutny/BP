<?php

class Scope implements ArrayAccess, Iterator, Countable
{
	/**
	 * @var array $variables Variables defined in scope
	 */
	protected $variables = [];

	public function __construct()
	{
	}

	/* === Iterator ================================================================================================== */

	public function next()
	{
		// TODO: Implement next() method.
	}

	public function current()
	{
		// TODO: Implement current() method.
	}

	public function rewind()
	{
		// TODO: Implement rewind() method.
	}

	public function valid()
	{
		// TODO: Implement valid() method.
	}

	public function key()
	{
		// TODO: Implement key() method.
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
