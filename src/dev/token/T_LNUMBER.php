<?php
namespace Tokens;

class token_T_LNUMBER extends AToken
{
	public function __construct(IToken $prev = NULL, $value = NULL)
	{
		$this->parent = $prev;

		$this->value = $value;

		$this->parent->addChild($this);
	}
}