<?php
namespace Tokens;

class token_T_LCURLY_PARENTHESIS extends AToken
{
	public function __construct(IToken $prev = NULL, $value = NULL)
	{
		$this->parent = $prev->getParent();

	}
}