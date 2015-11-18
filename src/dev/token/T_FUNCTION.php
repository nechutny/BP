<?php
namespace Tokens;

class token_T_FUNCTION extends AToken
{
	private $name;

	public function __construct(IToken $prev = NULL, $value = NULL)
	{
		if(!is_null($prev))
		{
			// Same level as previous
			$this->parent = $prev->getParent();

			// Add to him
			$this->parent->addChild($this);
		}
	}

	public function addChild(IToken $child)
	{
		if($child instanceof token_T_STRING)
		{
			$this->name = $child->getValue();
		}
		else
		{
			parent::addChild($child);
		}
	}
}