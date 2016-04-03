<?php

class FileGenerator
{
	/**
	 * @var FunctionGenerator[] $functions
	 */
	protected $functions = [];

	/**
	 * Add function to result code
	 *
	 * @param FunctionGenerator $generator Functgion to add
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
					'#include "cpp/PhpValString.cpp"'	."\n".
					'#include "cpp/PhpValFloat.cpp"'	."\n\n";

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
