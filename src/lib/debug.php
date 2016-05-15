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

class debug
{
	const TYPE_PRECEDENCE = 'Precedence';
	const TYPE_PARSER = 'Parser';

	static function printString($type, $string)
	{
		echo str_pad($type, 12, " ")."| ".$string."\n";
	}

	static function printStack($type, Stack $stack)
	{
		echo str_pad($type, 12, " ")."| ";
		$stack->debug();
		echo "\n";
	}
}
