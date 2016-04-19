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

	public function debug()
	{
		foreach ($this->variables as $variable)
		{
			$variable->debug();
		}
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
