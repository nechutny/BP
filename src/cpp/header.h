namespace php2cpp
{
	class Value;
	
	double to_float(int value);
	double to_float(long value);
	double to_float(long long value);
	double to_float(unsigned value);
	double to_float(unsigned long value);
	double to_float(unsigned long long value);
	double to_float(float value);
	double to_float(double value);
	double to_float(long double value);
	double to_float(Php::Value value);
	double to_float(std::string value);
	double to_float(const char* value);
	double to_float(php2cpp::Value& value);
	
	std::string to_string(int value);
	std::string to_string(long value);
	std::string to_string(long long value);
	std::string to_string(unsigned value);
	std::string to_string(unsigned long value);
	std::string to_string(unsigned long long value);
	std::string to_string(float value);
	std::string to_string(double value);
	std::string to_string(long double value);
	std::string to_string(const char* value);
	std::string to_string(std::string value);
	std::string to_string(Php::Value value);
}
