<?php

class Stack {

	protected $stack = [];
	protected $position = -1;

	public function push($value)
	{
		$this->stack[ ++$this->position ] = $value;
	}

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

		//die();



	}

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

	public function replaceTop($value)
	{
		$this->pop();
		$this->push($value);
	}

	public function debug()
	{
		echo "\n\n Stack debug:\n";

		foreach($this->stack as $item)
		{
			if(is_array($item))
			{
				if(isset($item['nonTerminal']))
				{
					echo $item['nonTerminal']."\n";
				}
				else
				{
					echo $item['value']."\n";
				}

			}
			else
			{
				echo $item."\n";
			}
		}
	}
}

class StackEmptyException extends Exception{

}