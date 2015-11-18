<?php
namespace Tokens;

require_once(__DIR__ . '/IToken.php');


abstract class AToken implements IToken
{
	protected $parent = NULL;
	protected $child = [];

	protected $value;

	public function __construct(IToken $prev = NULL, $value = NULL)
	{

	}

	public function toString()
	{

	}

	public function getParent()
	{
		return is_null($this->parent) ? $this : $this->parent;
	}

	public function addChild(IToken $child)
	{
		$this->child[] = $child;
	}

	public function getChildes()
	{
		return $this->child;
	}

	public function getValue()
	{
		return $this->value;
	}

}