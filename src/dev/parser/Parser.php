<?php

require_once( __DIR__ . '/../precedence/Precedence.php');

class Parser
{
	/**
	 * @var Scanner
	 */
	protected $scanner;

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

	public function parse_comment()
	{
		$token = $this->scanner->next();
	}

	/**
	 * function name(args) { body }
	 */
	public function parse_function()
	{
		$this->check(T_FUNCTION);

		$token = $this->scanner->next();
		if($token['code'] !== T_STRING)
		{
			throw new ParserError($token);
		}

		echo "Read function name: ".$token['value']."\n";

		$this->check(T_LPARENTHESIS);

		$args = $this->parse_args();

		echo "Arguments: \n";

		print_r($args);

		$this->check(T_RPARENTHESIS);

		$this->check(T_LCURLY_PARENTHESIS);

		$this->parse_body();

		$this->check(T_RCURLY_PARENTHESIS);
	}

	public function parse_variable()
	{
		$expr = new Precedence($this->scanner);
		$expr->run(Precedence::CONTEXT_ASSIGN);
	}

	public function parse_return()
	{
		$expr = new Precedence($this->scanner);
		$expr->run(Precedence::CONTEXT_RETURN);
	}

	public function parse_echo()
	{
		$expr = new Precedence($this->scanner);
		$expr->run(Precedence::CONTEXT_ECHO);
	}

	public function parse_body()
	{
		while(1)
		{
			$token = $this->scanner->next();

			switch($token['code'])
			{
				case T_VARIABLE:
					$this->parse_variable();
					break;

				case T_COMMENT:
					$this->scanner->back();
					$this->parse_comment();
					break;

				case T_RETURN:
					$this->parse_return();
					break;

				case T_ECHO:
					$this->parse_echo();
					break;

				case T_RCURLY_PARENTHESIS:
					$this->scanner->back();
					return;

				default:
					var_dump($token);
			}
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

		if($token['code'] === T_ARRAY)
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