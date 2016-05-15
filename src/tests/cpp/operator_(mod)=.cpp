#include <phpcpp.h>
#include <math.h>
#include <iostream>
#include "cpp/PhpValString.cpp"
#include "cpp/PhpValFloat.cpp"
#include "cpp/PhpValue.cpp"

Php::Value phpFunc_compiled_main(Php::Parameters &args)
{
	php2cpp::Value phpVar_b = 2;
	
	Php::out << ( phpVar_b) << "\n" << std::flush; // 2

	phpVar_b /= "aaaa";
	
	Php::out << ( phpVar_b) << "\n" << std::flush; // false

	phpVar_b = 2.0;

	phpVar_b /= "1";
	
	Php::out << ( phpVar_b) << "\n" << std::flush; // 2
	
	phpVar_b /= "2.0";
	
	Php::out << ( phpVar_b) << "\n" << std::flush; // 1.0
	
	phpVar_b /= 1;
	
	Php::out << ( phpVar_b) << "\n" << std::flush; // 1.0
	
	phpVar_b /= 2.0;
	
	Php::out << ( phpVar_b) << "\n" << std::flush; // 0.5
	
	phpVar_b /= std::string("0.5");
	
	Php::out << ( phpVar_b) << "\n" << std::flush; // 1.0
	
	phpVar_b /= Php::Value("0.25");
	
	Php::out << ( phpVar_b) << "\n" << std::flush; // 4.0
	
	phpVar_b /= Php::Value("aaa1.0");
	
	Php::out << ( phpVar_b) << "\n" << std::flush; // false

	phpVar_b = "9";
	phpVar_b /= false;
	
	Php::out << ( phpVar_b) << "\n" << std::flush; // false

	phpVar_b = "9.0";
	phpVar_b /= nullptr;
	
	Php::out << ( phpVar_b) << "\n" << std::flush; // false

	phpVar_b /= true;
	
	Php::out << ( phpVar_b) << "\n" << std::flush; // 0

	return nullptr;
}

extern "C"
{
	PHPCPP_EXPORT void *get_module()
	{
		static Php::Extension extension("TestExtension-dev", "1.0");

		extension.add("compiled_main", phpFunc_compiled_main);
		return extension;
	}
}
