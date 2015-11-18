<?php
namespace Tokens;

class token_T_STRING extends AToken
{
	public function __construct(IToken $prev = NULL, $value = NULL)
	{
		$this->value = $value;

		if(!is_null($prev))
		{
			// Same level as previous
			$this->parent = $prev;

			// Add to him
			$this->parent->addChild($this);
		}
	}
}