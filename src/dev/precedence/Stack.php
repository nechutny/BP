<?php

class Stack {

	protected $stack = [];
	protected $position = -1;

	/**
	 * Push new value on top of stack
	 *
	 * @param mixed $value Value to push
	 */
	public function push($value)
	{
		$this->stack[ ++$this->position ] = $value;
	}

	/**
	 * Push new value on top except non-terminals
	 *
	 * @param mixed $value Value to push
	 *
	 * @throws StackEmptyException
	 */
	public function pushTerminal($value)
	{
		$i = 0;
		while(1)
		{
			$tmp = $this->top($i);
			if(!isset($tmp['nonTerminal']))
			{
				break;
			}
			$i++;
		}

		for($j = count($this->stack); $j >= count($this->stack)-$i; $j--)
		{
			$this->stack[ $j ] = $this->stack[ $j-1 ];
		}

		$this->position++;

		$this->stack[count($this->stack)-$i-1] = $value;
	}

	/**
	 * Get value from top of stack and remove it
	 *
	 * @return mixed
	 *
	 * @throws StackEmptyException
	 */
	public function pop()
	{
		if(!isset($this->stack[ $this->position ]))
		{
			throw new StackEmptyException();
		}

		$val = $this->stack[ $this->position ];
		unset($this->stack[$this->position ]);

		$this->position--;

		return $val;
	}

	/**
	 * Get value from top of stack and keep it in stack
	 *
	 * @param int $offset Optional variable allowing to get items from stack under top (0 = top, 1 = first under top etc.)
	 * @return mixed
	 *
	 * @throws StackEmptyException
	 */
	public function top($offset = 0)
	{
		if(isset($this->stack[ $this->position-$offset ]))
		{
			return $this->stack[ $this->position-$offset ];
		}
		else
		{
			throw new StackEmptyException();
		}
	}

	/**
	 * Get terminal from top of stack (ignore non-terminals)
	 *
	 * @return mixed
	 *
	 * @throws StackEmptyException
	 */
	public function topTerminal()
	{
		$i = 0;
		while(1)
		{
			$tmp = $this->top($i);
			if(!isset($tmp['nonTerminal']))
			{
				return $tmp;
			}
			$i++;
		}
	}

	/**
	 * Debug output
	 */
	public function debug()
	{
		echo "Stack: ";

		foreach($this->stack as $item)
		{
			if(is_array($item))
			{
				if(isset($item['nonTerminal']))
				{
					echo $item['nonTerminal'].", ";
				}
				else
				{
					echo $item['value'].", ";
				}

			}
			else
			{
				echo $item.", ";
			}
		}
	}
}

class StackEmptyException extends Exception{

}
