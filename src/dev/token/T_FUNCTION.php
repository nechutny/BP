<?php
namespace Tokens;

class token_T_FUNCTION extends AToken
{
	public function __construct(IToken $prev = NULL)
	{
		if(!is_null($prev))
		{
			$this->parent = $prev->getParent();

			$this->parent->addChild($this);
		}
	}
}