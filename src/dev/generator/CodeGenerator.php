<?php

class CodeGenerator
{
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

				default:
					echo 'Not yet implemented';
					break;
			}
		}

		return $result;
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

}
