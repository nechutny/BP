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

class FunctionReturnType
{
	static $returnValues = [
		'acos'			=> Type::TYPE_FLOAT,
		'asin'			=> Type::TYPE_FLOAT,
		'atan'			=> Type::TYPE_FLOAT,
		'atan2'			=> Type::TYPE_FLOAT,
		'atanh'			=> Type::TYPE_FLOAT,
		'base64_decode'	=> Type::TYPE_STRING,
		'base64_encode'	=> Type::TYPE_STRING,
		'basename'		=> Type::TYPE_STRING,
		'base_convert'	=> Type::TYPE_STRING,
		'bin2hex'		=> Type::TYPE_STRING,
		'ceil'			=> Type::TYPE_FLOAT,
		'cos'			=> Type::TYPE_FLOAT,
		'cosh'			=> Type::TYPE_FLOAT,
		'count'			=> Type::TYPE_INT,
		'crc32'			=> Type::TYPE_INT,
		'date'			=> Type::TYPE_STRING,
		'decbin'		=> Type::TYPE_STRING,
		'dechex'		=> Type::TYPE_STRING,
		'decoct'		=> Type::TYPE_STRING,
		'define'		=> Type::TYPE_BOOLEAN,
		'defined'		=> Type::TYPE_BOOLEAN,
		'empty'			=> Type::TYPE_BOOLEAN,
		'stripos'		=> Type::TYPE_MIXED,
		'stristr'		=> Type::TYPE_STRING,
		'strlen'		=> Type::TYPE_INT,
		'strncmp'		=> Type::TYPE_INT,
		'strstr'		=> Type::TYPE_STRING,
		'str_pad'		=> Type::TYPE_STRING,
		'str_replace'	=> Type::TYPE_MIXED,
		'str_repeat'	=> Type::TYPE_STRING,
		'str_shuffle'	=> Type::TYPE_STRING,
		'substr'		=> Type::TYPE_STRING,
		'strtolower'	=> Type::TYPE_STRING,
		'strtoupper'	=> Type::TYPE_STRING,
		'is_null'		=> Type::TYPE_BOOLEAN,
	];

	public static function get($functionName)
	{
		$functionName = strtolower($functionName);

		if(isset(self::$returnValues[ $functionName ]))
		{
			return self::$returnValues[ $functionName ];
		}

		return Type::TYPE_MIXED;
	}
}

