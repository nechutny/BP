<?php

require_once(__DIR__ . '/../token/CustomTokens.php');
//require_once(__DIR__ . '/../token/AToken.php');
//foreach(glob(__DIR__ . '/../token/T_*.php') as $tokenFile)
//{
//	require_once($tokenFile);
//}

class Scanner {
	protected $tokens = [];
	protected $index = 0;

	public function __construct($file)
	{
		$file = file_get_contents($file);
		$tokens = token_get_all($file);

		$tok = NULL;

		foreach($tokens as $token)
		{
			if(!is_array($token))
			{
				switch($token)
				{
					case '(':
						$tname = 'T_LPARENTHESIS';
						break;
					case ')':
						$tname = 'T_RPARENTHESIS';
						break;
					case '{':
						$tname = 'T_LCURLY_PARENTHESIS';
						break;
					case '}':
						$tname = 'T_RCURLY_PARENTHESIS';
						break;
					case ';':
						$tname = 'T_SEMICOLON';
						break;
					case ',':
						$tname = 'T_COMMA';
						break;
					case '=':
						$tname = 'T_ASSIGN';
						break;
					case ']':
						$tname = 'T_ARRAY_CLOSE';
						break;
					case '+':
						$tname = 'T_PLUS';
						break;
					case '-':
						$tname = 'T_MINUS';
						break;
					case '*':
						$tname = 'T_MUL';
						break;
					case '/':
						$tname = 'T_DIV';
						break;
					default:
						$tname = 'T_UNSUPPORTED';
				}

				$tcode = constant($tname);

				$token = [
					'code'	=> $tcode,
					'name'	=> $tname,
					'value'	=> $token,
					'line'	=> NULL,
				];
			}
			else
			{
				$tname = token_name($token[0]);

				$token = [
					'code'	=> $token[0],
					'name'	=> $tname,
					'value'	=> $token[1],
					'line'	=> $token[2],
				];
			}

			$this->tokens[] = $token;
		}

		$this->index = 0;
	}

	public function next()
	{
		if(!isset($this->tokens[ $this->index ]))
		{
			throw new EndOfFileException('Token index out of range');
		}

		if($this->tokens[ $this->index ]['code'] == T_WHITESPACE)
		{
			$this->index++;
			return $this->next();
		}
		else
		{
			return $this->tokens[ $this->index++ ];
		}
	}

	public function back()
	{
		$this->index--;
		if($this->index < 0)
		{
			$this->index = 0;
		}
	}
}

class EndOfFileException extends Exception
{

}