<?php

require_once( __DIR__ . '/../precedence/Precedence.php');
require_once( __DIR__ . '/../generator/Generator.php');

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

	public function __construct($scanner)
	{
		$this->scanner = $scanner;
	}

	public function check($what)
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

	public function parse_comment($codeGenerator = NULL)
	{
		$token = $this->scanner->next();

		if(!is_null($codeGenerator))
		{
			$codeGenerator->addComment($token['value']);
		}
	}

	/**
	 * function name(args) { body }
	 */
	public function parse_function()
	{
		$functionGenerator = new FunctionGenerator();

		$this->check(T_FUNCTION);

		$token = $this->scanner->next();
		if($token['code'] !== T_STRING)
		{
			throw new ParserError($token);
		}

		//echo "Read function name: ".$token['value']."\n";

		$functionGenerator->setFunctionName($token['value']);

		$this->check(T_LPARENTHESIS);

		$args = $this->parse_args();

		//echo "Arguments: \n";

		//print_r($args);

		$functionGenerator->setArguments($args);

		$this->check(T_RPARENTHESIS);

		$this->check(T_LCURLY_PARENTHESIS);

		$codeGenerator = new CodeGenerator();

		$this->parse_body($codeGenerator);

		$this->check(T_RCURLY_PARENTHESIS);

		$functionGenerator->setCodeGenerator($codeGenerator);

		$this->generator->addFunction($functionGenerator);
	}

	public function parse_variable($codeGenerator)
	{
		$expr = new Precedence($this->scanner);
		$expr->run();
		$exprGenerator = new ExprGenerator($expr->getData());

		$codeGenerator->addExpression($exprGenerator);
		$codeGenerator->addVariables($expr->getUsedVariables());
	}

	public function parse_expression($codeGenerator)
	{
		$expr = new Precedence($this->scanner);
		$expr->run();
		$exprGenerator = new ExprGenerator($expr->getData());

		$codeGenerator->addExpression($exprGenerator);
		$codeGenerator->addVariables($expr->getUsedVariables());
	}

	public function parse_return($codeGenerator)
	{
		$expr = new Precedence($this->scanner);
		$expr->run();
		$exprGenerator = new ExprGenerator($expr->getData());

		$codeGenerator->addReturn($exprGenerator);
	}

	public function parse_echo($codeGenerator)
	{
		$expr = new Precedence($this->scanner);
		$expr->run();
		$exprGenerator = new ExprGenerator($expr->getData());

		$codeGenerator->addEcho($exprGenerator);

	}


	public function parser_command($codeGenerator)
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

			default:
				var_dump($token);
		}
	}

	public function parse_body($codeGenerator)
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

	public function parse_for($codeGenerator)
	{
		// 'for' eaten

		$this->check(T_LPARENTHESIS);

		$prec = new Precedence($this->scanner);
		$prec->run();
		$codeGenerator->addVariables($prec->getUsedVariables());
		$initExpr = new ExprGenerator($prec->getData());

		$prec = new Precedence($this->scanner);
		$prec->run();
		$codeGenerator->addVariables($prec->getUsedVariables());
		$ifExpr = new ExprGenerator($prec->getData());

		$prec = new Precedence($this->scanner);
		$prec->addEndToken(T_RPARENTHESIS);
		$prec->run();
		$codeGenerator->addVariables($prec->getUsedVariables());
		$iterExpr = new ExprGenerator($prec->getData());

		$this->check(T_RPARENTHESIS);

		$token = $this->scanner->next();
		$bodyCode = new CodeGenerator($codeGenerator->getIndent()+1);

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

	public function parse_while($codeGenerator)
	{
		// 'while' eaten
		$prec = new Precedence($this->scanner);
		$prec->run();
		$codeGenerator->addVariables($prec->getUsedVariables());
		$ifExpr = new ExprGenerator($prec->getData());

		$token = $this->scanner->next();
		$bodyCode = new CodeGenerator($codeGenerator->getIndent()+1);

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

	public function parse_do($codeGenerator)
	{
		// 'do' eaten

		$token = $this->scanner->next();
		$bodyCode = new CodeGenerator($codeGenerator->getIndent()+1);

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
		$codeGenerator->addVariables($prec->getUsedVariables());
		$ifExpr = new ExprGenerator($prec->getData());

		$codeGenerator->addDoWhile($ifExpr, $bodyCode);
	}

	/**
	 * if(<expr>) [{] <code> [}] [elseif(<expr>) { <code> }]+ [else { <code> }]
	 *
	 * @param $codeGenerator
	 */
	public function parse_if($codeGenerator)
	{
		// 'if' eaten


		$prec = new Precedence($this->scanner);
		$prec->run();


		// If
		$token = $this->scanner->next();
		$bodyCode = new CodeGenerator($codeGenerator->getIndent()+1);

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
		$codeGenerator->addIf(new ExprGenerator($prec->getData()), $bodyCode);
		$codeGenerator->addVariables($prec->getUsedVariables());

		// Elseif
		$token = $this->scanner->next();
		while($token['code'] == T_ELSEIF)
		{
			$prec = new Precedence($this->scanner);
			$prec->run();

			$token = $this->scanner->next();

			$bodyCode = new CodeGenerator($codeGenerator->getIndent()+1);



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

			$codeGenerator->addVariables($prec->getUsedVariables());
			$codeGenerator->addElseif(new ExprGenerator($prec->getData()), $bodyCode);

			$token = $this->scanner->next();
		}

		// else
		if($token['code'] == T_ELSE)
		{
			$token = $this->scanner->next();
			$bodyCode = new CodeGenerator($codeGenerator->getIndent()+1);

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

			$codeGenerator->addVariables($prec->getUsedVariables());
			$codeGenerator->addElse($bodyCode);
		}
		else
		{
			$token = $this->scanner->back();
		}



	}

	/**
	 * [typ] $name [= default]
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

		print_r($token);
	}

	public function parse_use()
	{
		// TODO
	}

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