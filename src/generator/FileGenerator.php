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

class FileGenerator
{
	/**
	 * @var FunctionGenerator[] $functions
	 */
	protected $functions = [];

	/**
	 * Add function to result code
	 *
	 * @param FunctionGenerator $generator Function to add
	 */
	public function addFunction(FunctionGenerator $generator)
	{
		$this->functions[] = $generator;
	}

	/**
	 * Get final C++ code
	 *
	 * @return string C++ code
	 */
	public function getCode()
	{
		$result =	'#include <phpcpp.h>'				."\n".
					'#include <math.h>'					."\n".
					'#include <iostream>'				."\n".
					'#include "cpp/header.h"'           ."\n".
					'#include "cpp/PhpValue.cpp"'       ."\n".
					'#include "cpp/PhpValString.cpp"'	."\n".
					'#include "cpp/PhpValFloat.cpp"'	."\n".
					"\n\n";

		foreach ($this->functions as $function)
		{
			$result .= $function->getCode();
		}

		$result .=	'extern "C"'															."\n".
					'{'																		."\n".
					'	PHPCPP_EXPORT void *get_module()'									."\n".
					'	{'																	."\n".
					'		static Php::Extension extension("TestExtension-dev", "1.0");'	."\n".
																						 	"\n";

		foreach ($this->functions as $function)
		{
			$result .= '		extension.add("'.$function->getName().'", phpFunc_'.$function->getName().$function->generateArguments().');'."\n";
		}

		$result .=	'		return extension;'												."\n".
					'	}'																	."\n".
					'}';

		return $result;
	}

}
