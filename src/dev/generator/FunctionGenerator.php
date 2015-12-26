<?php

require_once( __DIR__ . '/VariableGenerator.php');
require_once( __DIR__ . '/CodeGenerator.php');

class FunctionGenerator
{
	/**
	 * Function name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Arguments
	 *
	 * @var array $args
	 */
	protected $args = [];

	/**
	 * Variables
	 *
	 * @var VariableGenerator[] $vars
	 */
	protected $vars = [];

	/**
	 * @var CodeGenerator $codeGenerator
	 */
	protected $codeGenerator;

	public function setFunctionName($name)
	{
		echo "Function name: ".$name."\n";

		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setArguments(array $args)
	{
		$this->args = $args;

		echo "Arguments:\n";
		print_r($args);

		foreach($args as $key => $arg)
		{
			$var = new VariableGenerator($arg['name']);
			$var->assignArgument($key);

			if($arg['value'][0])
			{
				$var->setArgumentDefaultValue($arg['value'][1]);
			}

			$this->vars[ $arg['name'] ] = $var;
		}
	}

	protected function convertToType($type)
	{
		$map = [
			'MIXED'	=> 'Php::Type::String',
			'ARRAY'	=> 'Php::Type::Array',
		];

		if(isset($map[ strtoupper($type)]))
		{
			return $map[ strtoupper($type)];
		}

		return '"'.$type.'"';
	}

	public function generateArguments()
	{
		$result = [];

		foreach($this->args as $arg)
		{
			$result[] = 'Php::ByVal("'.
					substr($arg['name'],1).'", '.
					$this->convertToType($arg['type']).', '.
					(in_array($arg['type'], ['mixed','array']) ? '' : ($arg['value'][0] == 1 && $arg['value'][0] == 'NULL' ? 'true' : 'false').', ' ).
					($arg['value'][0] == 1 ? 'false' : 'true').
				')';
		}

		if(count($result) > 0)
		{
			return ', {'."\n\t\t\t".implode(",\n\t\t\t",$result)."\n\t\t}";
		}

		return "";
	}

	protected function variablesFromCode()
	{
		foreach($this->codeGenerator->getVariables() as $var)
		{
			if(!isset($this->vars[ $var ]))
			{
				$this->vars[ $var ] = new VariableGenerator($var);
			}
		}
	}


	public function setCodeGenerator(CodeGenerator $generator)
	{
		$this->codeGenerator = $generator;
	}

	public function getCode()
	{
		$this->variablesFromCode();

		$variables = '';
		foreach($this->vars as $var)
		{
			$variables .= $var->getCode();
		}

		return	'Php::Value phpFunc_'.$this->name.'(Php::Parameters &args)'			."\n".
				'{'																	."\n".
				''.$variables														."\n".
				''.$this->codeGenerator->getCode()									."\n".
				'	return nullptr;'													."\n".
				'}'																	."\n\n";


	}

}
