<?php
/*
 * Copyright 2016 Stanislav Nechutný
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once( __DIR__ . '/../precedence/Precedence.php');
require_once( __DIR__ . '/../generator/Generator.php');
require_once( __DIR__ . '/../variable/Scope.php');

class Parser
{
	/**
	 * @var Scanner $scanner
	 */
	protected $scanner;

	/**
	 * @var Generator $generator
	 */
	public $generator;

	/**
	 * Parser constructor.
	 * @param Scanner $scanner Scanner with parsed input file
	 */
	public function __construct(Scanner $scanner)
	{
		$this->scanner = $scanner;
	}

	/**
	 * Check if is in parser expected sequence
	 *
	 * @param int|array $what Which token(s) are expected
	 *
	 * @throws EndOfFileException
	 * @throws ParserError
	 */
	protected function check($what)
	{
		if(!is_array($what))
		{
			$what = [ $what ];
		}

		foreach($what as $w)
		{
			$token = $this->scanner->next();
			if($token['code'] != $w)
			{
				throw new ParserError($token, $w);
			}
		}
	}

	/**
	 * Parse whole PHP file
	 *
	 * @throws EndOfFileException
	 * @throws ParserError
	 */
	public function parse_file()
	{
		$this->check(T_OPEN_TAG);

		$this->generator = new FileGenerator();

		while(1)
		{
		$token = $this->scanner->next();
		$this->scanner->back();

			switch($token['code'])
			{
				case T_FUNCTION:
					$this->parse_function();
					break;
				case T_USE:
					$this->parse_use();
					break;
				case T_NAMESPACE:
					$this->parse_namespace();
					break;
				case T_COMMENT:
					$this->parse_comment();
					break;
				case T_DOC_COMMENT:
					$this->parse_comment();
					break;
				default:
					throw new ParserError($token);
			}
		}
	}

	/**
	 * Parse comments
	 *
	 * @param CodeGenerator|NULL $codeGenerator
	 * @throws EndOfFileException
	 */
	protected function parse_comment(CodeGenerator $codeGenerator = NULL)
	{
		$token = $this->scanner->next();

		if(!is_null($codeGenerator))
		{
			$codeGenerator->addComment($token['value']);
		}
	}

	/**
	 * Parse function declaration
	 *
	 * function name(args) { body }
	 *
	 * @throws EndOfFileException
	 * @throws ParserError
	 */
	protected function parse_function()
	{
		$functionGenerator = new FunctionGenerator();

		$this->check(T_FUNCTION);

		$token = $this->scanner->next();
		if($token['code'] !== T_STRING)
		{
			throw new ParserError($token);
		}


		$functionGenerator->setFunctionName($token['value']);

		$varScope = new Scope();
		$codeGenerator = new CodeGenerator(1, $varScope);

		$functionGenerator->setCodeGenerator($codeGenerator);

		$this->check(T_LPARENTHESIS);

		$args = $this->parse_args();
		$functionGenerator->setArguments($args);

		$this->check(T_RPARENTHESIS);
		$this->check(T_LCURLY_PARENTHESIS);


		$this->parse_body($codeGenerator);


		$this->check(T_RCURLY_PARENTHESIS);

		$this->generator->addFunction($functionGenerator);
	}

	/**
	 * Parse variable assign
	 *
	 * @param CodeGenerator $codeGenerator
	 * @throws PrecedenceException
	 */
	protected function parse_variable(CodeGenerator $codeGenerator)
	{
		$expr = new Precedence($this->scanner);
		$expr->run();
		$exprGenerator = new ExprGenerator($expr->getData(), $codeGenerator->getScope());

		$codeGenerator->addExpression($exprGenerator);
		//$codeGenerator->addVariables($expr->getUsedVariables());
	}

	/**
	 * Parse expression
	 *
	 * @param CodeGenerator $codeGenerator
	 * @throws PrecedenceException
	 */
	public function parse_expression(CodeGenerator $codeGenerator)
	{
		$expr = new Precedence($this->scanner);
		$expr->run();
		$exprGenerator = new ExprGenerator($expr->getData(), $codeGenerator->getScope());

		$codeGenerator->addExpression($exprGenerator);
		//$codeGenerator->addVariables($expr->getUsedVariables());
	}

	/**
	 * Parse return from function
	 *
	 * return <expr>;
	 *
	 * @param CodeGenerator $codeGenerator
	 * @throws PrecedenceException
	 */
	public function parse_return(CodeGenerator $codeGenerator)
	{
		$expr = new Precedence($this->scanner);
		$expr->run();
		$exprGenerator = new ExprGenerator($expr->getData(), $codeGenerator->getScope());

		$codeGenerator->addReturn($exprGenerator);
	}

	/**
	 * Parse echo
	 *
	 * echo <expr>;
	 *
	 * @param CodeGenerator $codeGenerator
	 * @throws PrecedenceException
	 */
	public function parse_echo(CodeGenerator $codeGenerator)
	{
		$expr = new Precedence($this->scanner);
		$expr->run();
		$exprGenerator = new ExprGenerator($expr->getData(), $codeGenerator->getScope());

		$codeGenerator->addEcho($exprGenerator);

	}

	/**
	 * Parse one PHP command
	 *
	 * @param CodeGenerator $codeGenerator
	 * @throws EndOfFileException
	 */
	public function parser_command(CodeGenerator $codeGenerator)
	{
		$token = $this->scanner->next();

		switch($token['code'])
		{
			case T_VARIABLE:
				$this->scanner->back();
				$this->parse_variable($codeGenerator);
				break;

			case T_COMMENT:
				$this->scanner->back();
				$this->parse_comment($codeGenerator);
				break;

			case T_STRING:
				$this->scanner->back();
				$this->parse_expression($codeGenerator);
				break;

			case T_LNUMBER:
				$this->scanner->back();
				$this->parse_expression($codeGenerator);
				break;

			case T_DNUMBER:
				$this->scanner->back();
				$this->parse_expression($codeGenerator);
				break;

			case T_FOR:
				$this->parse_for($codeGenerator);
				break;

			case T_WHILE:
				$this->parse_while($codeGenerator);
				break;

			case T_DO:
				$this->parse_do($codeGenerator);
				break;

			case T_RETURN:
				$this->parse_return($codeGenerator);
				break;

			case T_ECHO:
				$this->parse_echo($codeGenerator);
				break;

			case T_IF:
				$this->parse_if($codeGenerator);
				break;

			case T_FOREACH:
				$this->parse_foreach($codeGenerator);
				break;

			case T_BREAK:
				$this->parse_break($codeGenerator);
				break;

			default:
				var_dump($token);
		}
	}

	/**
	 * Parse N commands until }
	 *
	 * @param CodeGenerator $codeGenerator
	 * @throws EndOfFileException
	 */
	public function parse_body(CodeGenerator $codeGenerator)
	{
		while(1)
		{
			$token = $this->scanner->next();

			if($token['code'] == T_RCURLY_PARENTHESIS)
			{
				$this->scanner->back();
				return;
			}

			$this->scanner->back();

			$this->parser_command($codeGenerator);
		}
	}

	/**
	 * Parse loop/switch break
	 *
	 * @param CodeGenerator $codeGenerator
	 * @throws ParserError
	 */
	public function parse_break(CodeGenerator $codeGenerator)
	{
		$codeGenerator->addBreak();

		$this->check(T_SEMICOLON);
	}

	/**
	 * Parse loop continue
	 *
	 * @param CodeGenerator $codeGenerator
	 * @throws ParserError
	 */
	public function parse_continue(CodeGenerator $codeGenerator)
	{
		$codeGenerator->addContinue();

		$this->check(T_SEMICOLON);
	}

	/**
	 * Parse for loop
	 *
	 * for(<expr>;<expr>;<expr>) { ... }
	 *
	 * @param CodeGenerator $codeGenerator
	 * @throws EndOfFileException
	 * @throws ParserError
	 * @throws PrecedenceException
	 */
	public function parse_for(CodeGenerator $codeGenerator)
	{
		// 'for' eaten

		$this->check(T_LPARENTHESIS);

		$prec = new Precedence($this->scanner);
		$prec->run();
		//$codeGenerator->addVariables($prec->getUsedVariables());
		$initExpr = new ExprGenerator($prec->getData(), $codeGenerator->getScope());

		$prec = new Precedence($this->scanner);
		$prec->run();
		//$codeGenerator->addVariables($prec->getUsedVariables());
		$ifExpr = new ExprGenerator($prec->getData(), $codeGenerator->getScope());

		$prec = new Precedence($this->scanner);
		$prec->addEndToken(T_RPARENTHESIS);
		$prec->run();
		//$codeGenerator->addVariables($prec->getUsedVariables());
		$iterExpr = new ExprGenerator($prec->getData(), $codeGenerator->getScope());

		$this->check(T_RPARENTHESIS);

		$token = $this->scanner->next();
		$bodyCode = new CodeGenerator($codeGenerator->getIndent()+1, $codeGenerator->getScope());

		if($token['code'] == T_LCURLY_PARENTHESIS)
		{
			$this->parse_body($bodyCode);

			$this->check(T_RCURLY_PARENTHESIS);
		}
		else
		{
			$this->scanner->back();
			$this->parser_command($bodyCode);
		}

		$codeGenerator->addFor($initExpr, $ifExpr, $iterExpr, $bodyCode);
	}


	public function parse_foreach(CodeGenerator $codeGenerator)
	{
		// 'foreach' eaten

		$this->check(T_LPARENTHESIS);

		$prec = new Precedence($this->scanner);
		$prec->addEndToken(T_AS);
		$prec->run();

		$source = new ExprGenerator($prec->getData(), $codeGenerator->getScope());

		$this->check(T_AS);

		$token = $this->scanner->next(TRUE);
		if($token['code'] != T_VARIABLE)
		{
			throw new ParserError($token, T_VARIABLE);
		}

		$varName = $token['value'];


		$this->check(T_RPARENTHESIS);

		$token = $this->scanner->next();
		$bodyCode = new CodeGenerator($codeGenerator->getIndent()+1, $codeGenerator->getScope());

		if($token['code'] == T_LCURLY_PARENTHESIS)
		{
			$this->parse_body($bodyCode);

			$this->check(T_RCURLY_PARENTHESIS);
		}
		else
		{
			$this->scanner->back();
			$this->parser_command($bodyCode);
		}

		$codeGenerator->addForeach($source, $varName, $bodyCode);
	}

	/**
	 * Parse while loop
	 *
	 * while(<expr>) { ... }
	 *
	 * @param CodeGenerator $codeGenerator
	 * @throws EndOfFileException
	 * @throws ParserError
	 * @throws PrecedenceException
	 */
	public function parse_while(CodeGenerator $codeGenerator)
	{
		// 'while' eaten
		$prec = new Precedence($this->scanner);
		$prec->run();
		//$codeGenerator->addVariables($prec->getUsedVariables());
		$ifExpr = new ExprGenerator($prec->getData(), $codeGenerator->getScope());

		$token = $this->scanner->next();
		$bodyCode = new CodeGenerator($codeGenerator->getIndent()+1, $codeGenerator->getScope());

		if($token['code'] == T_LCURLY_PARENTHESIS)
		{
			$this->parse_body($bodyCode);

			$this->check(T_RCURLY_PARENTHESIS);
		}
		else
		{
			$this->scanner->back();
			$this->parser_command($bodyCode);
		}

		$codeGenerator->addWhile($ifExpr, $bodyCode);
	}

	/**
	 * Parse do-while loop
	 *
	 * do { ... } while(<expr>)
	 *
	 * @param CodeGenerator $codeGenerator
	 * @throws EndOfFileException
	 * @throws ParserError
	 * @throws PrecedenceException
	 */
	public function parse_do(CodeGenerator $codeGenerator)
	{
		// 'do' eaten

		$token = $this->scanner->next();
		$bodyCode = new CodeGenerator($codeGenerator->getIndent()+1, $codeGenerator->getScope());

		if($token['code'] == T_LCURLY_PARENTHESIS)
		{
			$this->parse_body($bodyCode);

			$this->check(T_RCURLY_PARENTHESIS);
		}
		else
		{
			$this->scanner->back();
			$this->parser_command($bodyCode);
		}

		$this->check(T_WHILE);

		$prec = new Precedence($this->scanner);
		$prec->run();
		//$codeGenerator->addVariables($prec->getUsedVariables());
		$ifExpr = new ExprGenerator($prec->getData(), $codeGenerator->getScope());

		$codeGenerator->addDoWhile($ifExpr, $bodyCode);
	}

	/**
	 * if(<expr>) [{] <code> [}] [elseif(<expr>) { <code> }]+ [else { <code> }]
	 *
	 * @param $codeGenerator
	 */
	public function parse_if(CodeGenerator $codeGenerator)
	{
		// 'if' eaten


		$prec = new Precedence($this->scanner);
		$prec->run();


		// If
		$token = $this->scanner->next();
		$bodyCode = new CodeGenerator($codeGenerator->getIndent()+1, $codeGenerator->getScope());

		if($token['code'] == T_LCURLY_PARENTHESIS)
		{
			$this->parse_body($bodyCode);

			$this->check(T_RCURLY_PARENTHESIS);
		}
		else
		{
			$this->scanner->back();
			$this->parser_command($bodyCode);
		}
		$codeGenerator->addIf(new ExprGenerator($prec->getData(), $codeGenerator->getScope()), $bodyCode);
		//$codeGenerator->addVariables($prec->getUsedVariables());

		// Elseif
		$token = $this->scanner->next();
		while($token['code'] == T_ELSEIF)
		{
			$prec = new Precedence($this->scanner);
			$prec->run();

			$token = $this->scanner->next();

			$bodyCode = new CodeGenerator($codeGenerator->getIndent()+1, $codeGenerator->getScope());



			if($token['code'] == T_LCURLY_PARENTHESIS)
			{
				$this->parse_body($bodyCode);


				$this->check(T_RCURLY_PARENTHESIS);
			}
			else
			{
				$this->scanner->back();
				$this->parser_command($bodyCode);
			}

			//$codeGenerator->addVariables($prec->getUsedVariables());
			$codeGenerator->addElseif(new ExprGenerator($prec->getData(),$codeGenerator->getScope()), $bodyCode);

			$token = $this->scanner->next();
		}

		// else
		if($token['code'] == T_ELSE)
		{
			$token = $this->scanner->next();
			$bodyCode = new CodeGenerator($codeGenerator->getIndent()+1, $codeGenerator->getScope());

			if($token['code'] == T_LCURLY_PARENTHESIS)
			{
				$this->parse_body($bodyCode);

				$this->check(T_RCURLY_PARENTHESIS);
			}
			else
			{
				$this->scanner->back();
				$this->parser_command($bodyCode);
			}

			//$codeGenerator->addVariables($prec->getUsedVariables());
			$codeGenerator->addElse($bodyCode);
		}
		else
		{
			$this->scanner->back();
		}
	}

	/**
	 * [type] $name [= default]
	 *
	 * @return array
	 * @throws Exception
	 */
	public function parse_args()
	{
		$token = $this->scanner->next();
		if($token['code'] === T_RPARENTHESIS)
		{ // No arguments
			$this->scanner->back();
			return [];
		}

		$arg = [
			'type'	=> 'mixed',
			'name'	=> NULL,
			'value'	=> [FALSE, NULL]
		];

		if($token['code'] === T_ARRAY || $token['code'] === T_STRING)
		{ // type
			$arg['type'] = $token['value'];
			$token = $this->scanner->next();
		}

		if($token['code'] === T_VARIABLE)
		{
			$arg['name'] = $token['value'];
			$token = $this->scanner->next();
		}

		if($token['code'] === T_ASSIGN)
		{
			$token = $this->scanner->next();

			$arg['value'] = [TRUE, $token['value']];

			if($token['value'] == '[')
			{
				// TODO: allow initialization with non-empty arrays
				$this->check(T_ARRAY_CLOSE);
			}

			$token = $this->scanner->next();
		}

		if($token['code'] === T_RPARENTHESIS)
		{
			$this->scanner->back();
			if(is_null($arg['name']))
			{
				return [];
			}
			return [ $arg ];
		}
		elseif($token['code'] == T_COMMA)
		{
			return array_merge([$arg], $this->parse_args());
		}
	}

	/**
	 * Parse use statement for classes
	 */
	public function parse_use()
	{
		// TODO
	}

	/**
	 * Parse namespace definitions
	 */
	public function parse_namespace()
	{
		// TODO
	}
}

class ParserError extends Exception
{
	private $token;
	private $expected;

	public function __construct($token, $expected = NULL)
	{
		$this->token = $token;
		$this->expected = $expected;
	}

	public function __toString()
	{
		echo "\n\nException\n";
		print_r($this->token);
		if(!is_null($this->expected))
		{
			echo "\n Expected: ".$this->expected;
		}

		return "";
	}
}
