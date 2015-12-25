<?php

class CodeGenerator
{
	protected $variables = [];

	protected $commands = [];

	public function getCode($indent = 1)
	{
		$result = "";

		foreach($this->commands as $command)
		{
			switch($command['command'])
			{
				case 'echo':
					$result .= str_pad("\t", $indent).'Php::out << '.$command['args'][0].';'."\n";
					break;

				case 'return':
					$result .= str_pad("\t", $indent).'return '.$command['args'][0].';'."\n";
					break;

				case 'comment':
					$result .= str_pad("\t", $indent).''.$command['args'][0].''."\n";
					break;

				case 'expression':
					$result .= str_pad("\t", $indent).''.$command['args'][0].';'."\n";
					break;

				default:
					echo 'Not yet implemented';
					break;
			}
		}

		return $result;
	}

	public function addVariables(array $names)
	{
		$this->variables = array_merge($this->variables, array_flip($names));
	}

	public function getVariables()
	{
		return array_keys($this->variables);
	}

	public function addComment($comment)
	{
		$this->commands[] = [
				'command'	=> 'comment',
				'args'		=> [$comment]
		];
	}

	public function addReturn($expr)
	{
		$this->commands[] = [
				'command' => 'return',
				'args' => [
						$expr
				],
		];
	}

	public function addEcho($expr)
	{
		$this->commands[] = [
			'command' => 'echo',
			'args' => [
				$expr
			],
		];
	}

	public function addExpression($expr)
	{
		$this->commands[] = [
				'command' => 'expression',
				'args' => [
						$expr
				],
		];
	}

}
