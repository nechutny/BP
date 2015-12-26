<?php

class CodeGenerator
{
	protected $indent = 1;

	protected $variables = [];

	protected $commands = [];

	/**
	 * CodeGenerator constructor.
	 *
	 * @param int $indent Indention level
	 */
	public function __construct($indent = 1)
	{
		$this->indent = $indent;
	}

	/**
	 * Get indention level
	 *
	 * @return int Indention level
	 */
	public function getIndent()
	{
		return $this->indent;
	}

	/**
	 * Generate code depending on internal structure
	 *
	 * @return string
	 */
	public function getCode()
	{
		$indent = $this->indent;

		$result = "";

		foreach($this->commands as $command)
		{
			switch($command['command'])
			{
				case 'echo':
					$result .= str_pad('', $indent, "\t").'Php::out << ('.$command['args'][0]->getCode().') << std::flush;'."\n";
					break;

				case 'return':
					$result .= str_pad('', $indent, "\t").'return '.$command['args'][0]->getCode().';'."\n";
					break;

				case 'comment':
					$result .= str_pad('', $indent, "\t").''.$command['args'][0].''."\n";
					break;

				case 'expression':
					$result .= str_pad('', $indent, "\t").''.$command['args'][0]->getCode().';'."\n";
					break;

				case 'for':
					$result .=	str_pad('', $indent, "\t").'for('.$command['args'][0]->getCode().' ; '.$command['args'][1]->getCode().' ; '.$command['args'][2]->getCode().' )'	."\n".
							str_pad('', $indent, "\t").'{'																						."\n".
							$command['args'][3]->getCode().
							str_pad('', $indent, "\t").'}'																						."\n";
					break;

				case 'while':
					$result .=	str_pad('', $indent, "\t").'while'.$command['args'][0]->getCode().''														."\n".
							str_pad('', $indent, "\t").'{'																						."\n".
							$command['args'][1]->getCode().
							str_pad('', $indent, "\t").'}'																						."\n";
					break;

				case 'dowhile':
					$result .=	str_pad('', $indent, "\t").'do'																					."\n".
							str_pad('', $indent, "\t").'{'																						."\n".
							$command['args'][1]->getCode().
							str_pad('', $indent, "\t").'}'																						."\n".
							str_pad('', $indent, "\t").'while'.$command['args'][0]->getCode().''															."\n";
					break;

				case 'break':
					$result .= str_pad('', $indent, "\t").'break;'."\n";
					break;

				case 'continue':
					$result .= str_pad('', $indent, "\t").'continue;'."\n";
					break;

				case 'if':
					$result .=	str_pad('', $indent, "\t").'if'.$command['args'][0]->getCode().''			."\n".
								str_pad('', $indent, "\t").'{'									."\n".
								$command['args'][1]->getCode().
								str_pad('', $indent, "\t").'}'									."\n";
					break;

				case 'elseif':
					$result .=	str_pad('', $indent, "\t").'else if'.$command['args'][0]->getCode().''		."\n".
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

	/**
	 * Add variable names from childs
	 *
	 * @param array $names Variable names
	 */
	public function addVariables(array $names)
	{
		$this->variables = array_merge($this->variables, array_flip($names));
	}

	/**
	 * Get all variables used in block and childs
	 *
	 * @return array Variable names
	 */
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

	/**
	 * Add to source at current position comment
	 *
	 * @param string $comment Comment to add into source code
	 */
	public function addComment($comment)
	{
		$this->commands[] = [
				'command'	=> 'comment',
				'args'		=> [$comment]
		];
	}

	/**
	 * Return value at current position from function.
	 *
	 * @param ExprGenerator $expr Value to return
	 */
	public function addReturn(ExprGenerator $expr)
	{
		$this->commands[] = [
				'command' => 'return',
				'args' => [
						$expr
				],
		];
	}

	/**
	 * Print value to output and flush
	 *
	 * @param ExprGenerator $expr Value to print
	 */
	public function addEcho(ExprGenerator $expr)
	{
		$this->commands[] = [
			'command' => 'echo',
			'args' => [
				$expr
			],
		];
	}

	/**
	 * Add expression to source code
	 *
	 * @param ExprGenerator $expr Expression to add
	 */
	public function addExpression(ExprGenerator $expr)
	{
		$this->commands[] = [
				'command' => 'expression',
				'args' => [
						$expr
				],
		];
	}

	/**
	 * Add for loop
	 *
	 * @param ExprGenerator $initExpr Initial expression
	 * @param ExprGenerator $ifExpr Expression evaluted each iteration
	 * @param ExprGenerator $iterExpr Expression used for incrementing counter etc.
	 * @param CodeGenerator $bodyCode Loop body
	 */
	public function addFor(ExprGenerator $initExpr, ExprGenerator $ifExpr, ExprGenerator $iterExpr, CodeGenerator $bodyCode)
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

	/**
	 * While loop
	 *
	 * @param ExprGenerator $condition Loop condition
	 * @param CodeGenerator $block Loop body
	 */
	public function addWhile(ExprGenerator $condition, CodeGenerator $block)
	{
		$this->commands[] = [
				'command'	=> 'while',
				'args'		=> [
						$condition,
						$block
				]
		];
	}

	/**
	 * Add do-while loop
	 *
	 * @param ExprGenerator $condition Loop condition
	 * @param CodeGenerator $block Loop body
	 */
	public function addDoWhile(ExprGenerator $condition, CodeGenerator $block)
	{
		$this->commands[] = [
				'command'	=> 'dowhile',
				'args'		=> [
						$condition,
						$block
				]
		];
	}

	/**
	 * Add conditioned block of code
	 *
	 * @param ExprGenerator $expr Condition expression
	 * @param CodeGenerator $block Code block
	 */
	public function addIf(ExprGenerator $expr, CodeGenerator $block)
	{
		$this->commands[] = [
				'command'	=> 'if',
				'args'		=> [
					$expr,
					$block
				]
		];
	}

	/**
	 * Add elseif to code
	 *
	 * @param ExprGenerator $expr Condition
	 * @param CodeGenerator $block Code
	 *
	 * @throws ElseCodeException
	 */
	public function addElseif(ExprGenerator $expr, CodeGenerator $block)
	{
		if(!in_array($this->commands[ count($this->commands) -1 ]['command'], ['if', 'elseif']))
		{
			throw new ElseCodeException('Before elseif isn\'t  if, or elseif!');
		}

		$this->commands[] = [
				'command'	=> 'elseif',
				'args'		=> [
						$expr,
						$block
				]
		];
	}

	/**
	 * Add else to code
	 *
	 * @param CodeGenerator $block Code
	 *
	 * @throws ElseCodeException
	 */
	public function addElse(CodeGenerator $block)
	{
		if(!in_array($this->commands[ count($this->commands) -1 ]['command'], ['if', 'elseif']))
		{
			throw new ElseCodeException('Before else isn\'t  if, or elseif!');
		}

		$this->commands[] = [
				'command'	=> 'else',
				'args'		=> [
						$block
				]
		];
	}

	/**
	 * Add break; statement for loop/switch
	 */
	public function addBreak()
	{
		$this->commands[] = [
				'command'	=> 'break',
				'args'		=> []
		];
	}

	/**
	 * Add continue statement for loop
	 */
	public function addContinue()
	{
		$this->commands[] = [
				'command'	=> 'continue',
				'args'		=> []
		];
	}
}


class ElseCodeException extends Exception {

}