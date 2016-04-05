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

	/**
	 * Set name of generated function
	 *
	 * @param string $name Function name
	 */
	public function setFunctionName($name)
	{
		echo "Function name: ".$name."\n";

		$this->name = $name;
	}

	/**
	 * Get function name
	 *
	 * @return string function name
	 */
	public function getName()
	{
		return "compiled_".$this->name;
	}

	/**
	 * Set function arguments
	 *
	 * @param array $args structure with information about arguments
	 */
	public function setArguments(array $args)
	{
		$this->args = $args;

		echo "Arguments:\n";
		print_r($args);

		$scope = $this->codeGenerator->getScope();

		var_dump($scope);

		foreach($args as $key => $arg)
		{
			$scope[ $arg['name'] ] = new Variable($arg['name']);
			$var = new VariableGenerator($arg['name']);
			$var->assignArgument($key);
			$var->setVariable($scope[ $arg['name'] ]);

			if($arg['value'][0])
			{
				$var->setArgumentDefaultValue($arg['value'][1]);
			}

			$this->vars[ $arg['name'] ] = $var;
		}
	}

	/**
	 * Convert function arguments to proper type
	 *
	 * @param string $type Type of variable
	 * @return string C++ code tu use in PHP-CPP definition
	 */
	protected function convertToType($type)
	{
		// Aliases not supported - specified in PHP documentation
		$map = [
			'MIXED'		=> 'Php::Type::Null',		// Default, anything
			'ARRAY'		=> 'Php::Type::Array',		// PHP 5.1
			'CALLABLE'	=> 'Php::Type::Callable',	// PHP 5.4
			'BOOL'		=> 'Php::Type::Bool', 		// PHP 7
			'FLOAT'		=> 'Php::Type::Float', 		// PHP 7
			'INT'		=> 'Php::Type::Numeric',	// PHP 7
			'STRING'	=> 'Php::Type::String',		// PHP 7
		];

		if(isset($map[ strtoupper($type)]))
		{
			return $map[ strtoupper($type)];
		}

		return '"'.$type.'"'; // Class name - PHP 5.0
	}

	/**
	 * Generate C++ code assigning arguments to variables
	 *
	 * @return string C++ code
	 */
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

	/**
	 * Extract all variables from code
	 */
	protected function variablesFromCode()
	{
		$tree = [];
		foreach($this->codeGenerator->getVariables($tree) as $var => $val)
		{
			if(!isset($this->vars[ $var ]))
			{
				$this->vars[ $var ] = new VariableGenerator($var);
			}
		}
	}

	/**
	 * Set code generator
	 *
	 * @param CodeGenerator $generator Generator
	 */
	public function setCodeGenerator(CodeGenerator $generator)
	{
		$this->codeGenerator = $generator;
	}

	/**
	 * Get C++ code for function
	 *
	 * @return string C++ code
	 */
	public function getCode()
	{
		$this->variablesFromCode();

		$this->codeGenerator->getScope()->debug();

		$variables = '';
		foreach($this->vars as $var)
		{
			$var->setVariable( $this->codeGenerator->getScope()[ '$'.$var->getName() ] );
			$variables .= $var->getCode();
		}

		return	'Php::Value phpFunc_'.$this->getName().'(Php::Parameters &args)'	."\n".
				'{'																	."\n".
				''.$variables														."\n".
				''.$this->codeGenerator->getCode()									."\n".
				'	return nullptr;'												."\n".
				'}'																	."\n\n";


	}

}
