<?php
namespace Tokens;

class token_T_RPARENTHESIS extends AToken
{
	public function __construct(IToken $prev = NULL, $value = NULL)
	{
		$this->parent = $prev->getParent();

	}
}