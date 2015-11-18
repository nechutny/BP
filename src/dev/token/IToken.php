<?php
namespace Tokens;

interface IToken
{
	public function toString();

	public function getParent();

	public function getChildes();

	public function getValue();

	public function __construct(IToken $prev = NULL, $value = NULL);

}