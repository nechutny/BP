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

require_once(__DIR__ . '/CustomTokens.php');

class Scanner {
	protected $tokens = [];
	protected $index = 0;

	protected $comments = [T_COMMENT, T_DOC_COMMENT];

	/**
	 * Scanner constructor.
	 *
	 * @param string $file Path to source file
	 */
	public function __construct($file)
	{
		$file = file_get_contents($file);

		echo "Input file: \n\n";

		echo $file;

		echo "\n\n\n";

		$tokens = token_get_all($file);

		$tok = NULL;

		foreach($tokens as $token)
		{
			if(!is_array($token))
			{
				switch(strtoupper($token))
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
					case '.':
						$tname = 'T_CONCAT';
						break;
					case '=':
						$tname = 'T_ASSIGN';
						break;
					case '[':
						$tname = 'T_ARRAY_OPEN';
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
					case '%':
						$tname = 'T_MOD';
						break;
					case '!':
						$tname = 'T_NEG';
						break;
					case 'NULL':
						$tname = 'T_NULL';
						break;
					case '<':
						$tname = 'T_LESS';
						break;
					case '>':
						$tname = 'T_GREATER';
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

	/**
	 * Get token and move position to next token
	 *
	 * @param bool|FALSE $ignoreComment True if you want next token and ignore comments
	 * @return array
	 *
	 * @throws EndOfFileException
	 */
	public function next($ignoreComment = FALSE)
	{
		if(!isset($this->tokens[ $this->index ]))
		{
			throw new EndOfFileException('Token index out of range');
		}

		if($this->tokens[ $this->index ]['code'] == T_WHITESPACE || ($ignoreComment && in_array($this->tokens[ $this->index ]['code'], $this->comments)))
		{

			$this->index++;
			return $this->next($ignoreComment);
		}
		else
		{
			return $this->tokens[ $this->index++ ];
		}
	}

	/**
	 * Move position one step back
	 */
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
