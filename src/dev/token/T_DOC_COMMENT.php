<?php
namespace Tokens;

class token_T_DOC_COMMENT extends AToken
{
	public function __construct(IToken $prev = NULL, $value = NULL)
	{
		if(!is_null($prev))
		{
			$this->parent = $prev->getParent();

			$this->parent->addChild($this);
		}

		$this->value = $value;
	}

	public function addChild(IToken $child)
	{
		$this->parent->addChild($child);
	}
}