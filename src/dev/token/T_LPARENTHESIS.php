<?php
namespace Tokens;

class token_T_LPARENTHESIS extends AToken
{
	public function __construct(IToken $prev = NULL, $value = NULL)
	{
		if($prev instanceof token_T_STRING)
		{
			$this->parent = $prev->getParent();

			// TODO: register arguments
		}
		else
		{
			var_dump($prev);
		}
	}
}