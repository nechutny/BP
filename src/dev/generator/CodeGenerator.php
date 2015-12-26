<?php

class CodeGenerator
{
	protected $indent = 1;

	protected $variables = [];

	protected $commands = [];

	public function __construct($indent = 1)
	{
		$this->indent = $indent;
	}

	public function getIndent()
	{
		return $this->indent;
	}

	public function getCode()
	{
		$indent = $this->indent;

		$result = "";

		foreach($this->commands as $command)
		{
			switch($command['command'])
			{
				case 'echo':
					$result .= str_pad('', $indent, "\t").'Php::out << ('.$command['args'][0].') << std::flush;'."\n";
					break;

				case 'return':
					$result .= str_pad('', $indent, "\t").'return '.$command['args'][0].';'."\n";
					break;

				case 'comment':
					$result .= str_pad('', $indent, "\t").''.$command['args'][0].''."\n";
					break;

				case 'expression':
					$result .= str_pad('', $indent, "\t").''.$command['args'][0].';'."\n";
					break;

				case 'for':
					$result .=	str_pad('', $indent, "\t").'for('.$command['args'][0].' ; '.$command['args'][1].' ; '.$command['args'][2].' )'	."\n".
							str_pad('', $indent, "\t").'{'																						."\n".
							$command['args'][3]->getCode().
							str_pad('', $indent, "\t").'}'																						."\n";
					break;

				case 'while':
					$result .=	str_pad('', $indent, "\t").'while'.$command['args'][0].''														."\n".
							str_pad('', $indent, "\t").'{'																						."\n".
							$command['args'][1]->getCode().
							str_pad('', $indent, "\t").'}'																						."\n";
					break;

				case 'dowhile':
					$result .=	str_pad('', $indent, "\t").'do'																					."\n".
							str_pad('', $indent, "\t").'{'																						."\n".
							$command['args'][1]->getCode().
							str_pad('', $indent, "\t").'}'																						."\n".
							str_pad('', $indent, "\t").'while'.$command['args'][0].''															."\n";
					break;

				case 'if':
					$result .=	str_pad('', $indent, "\t").'if'.$command['args'][0].''			."\n".
								str_pad('', $indent, "\t").'{'									."\n".
								$command['args'][1]->getCode().
								str_pad('', $indent, "\t").'}'									."\n";
					break;

				case 'elseif':
					$result .=	str_pad('', $indent, "\t").'else if'.$command['args'][0].''		."\n".
							str_pad('', $indent, "\t").'{'										."\n".
							$command['args'][1]->getCode().
							str_pad('', $indent, "\t").'}'										."\n";
					break;

				case 'else':
					$result .=	str_pad('', $indent, "\t").'else'								."\n".
							str_pad('', $indent, "\t").'{'										."\n".
							$command['args'][0]->getCode().
							str_pad('', $indent, "\t").'}'										."\n";
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
		foreach($this->commands as $command)
		{
			foreach($command['args'] as $arg)
			{
				if($arg instanceof CodeGenerator)
				{
					$this->addVariables($arg->getVariables());
				}
			}
		}

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

	public function addFor($initExpr, $ifExpr, $iterExpr, $bodyCode)
	{
		$this->commands[] = [
				'command' => 'for',
				'args' => [
						$initExpr,
						$ifExpr,
						$iterExpr,
						$bodyCode
				],
		];
	}

	public function addWhile($condition, $block)
	{
		$this->commands[] = [
				'command'	=> 'while',
				'args'		=> [
						$condition,
						$block
				]
		];
	}

	public function addDoWhile($condition, $block)
	{
		$this->commands[] = [
				'command'	=> 'dowhile',
				'args'		=> [
						$condition,
						$block
				]
		];
	}

	public function addIf($expr, $block)
	{
		$this->commands[] = [
				'command'	=> 'if',
				'args'		=> [
					$expr,
					$block
				]
		];
	}

	public function addElseif($expr, $block)
	{
		$this->commands[] = [
				'command'	=> 'elseif',
				'args'		=> [
						$expr,
						$block
				]
		];
	}

	public function addElse($block)
	{
		$this->commands[] = [
				'command'	=> 'else',
				'args'		=> [
						$block
				]
		];
	}
}
