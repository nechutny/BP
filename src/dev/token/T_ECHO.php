<?php
namespace Tokens;

class token_T_ECHO extends AToken
{
	public function __construct(IToken $prev = NULL, $value = NULL)
	{
		$this->parent = $prev->getParent();

		$this->parent->addChild($this);
	}
}