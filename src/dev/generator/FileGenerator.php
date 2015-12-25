<?php

class FileGenerator
{
	/**
	 * @var FunctionGenerator[] $functions
	 */
	protected $functions = [];

	public function addFunction(FunctionGenerator $generator)
	{
		$this->functions[] = $generator;
	}

	public function getCode()
	{
		$result =	'#include <phpcpp.h>'			."\n".
					'#include <math.h>'			."\n".
					'#include <iostream>'			."\n\n";

		foreach ($this->functions as $function)
		{
			$result .= $function->getCode();
		}

		$result .=	'extern "C"'														."\n".
					'{'																	."\n".
					'	PHPCPP_EXPORT void *get_module()'								."\n".
					'	{'																."\n".
					'		static Php::Extension extension("TestExtension", "1.0");'	."\n".
																						 "\n";

		foreach ($this->functions as $function)
		{
			$result .= '		extension.add("'.$function->getName().'", phpFunc_'.$function->getName().$function->generateArguments().');'."\n";
		}

		$result .=	'		return extension;'											."\n".
					'	}'																."\n".
					'}';

		return $result;
	}

}
