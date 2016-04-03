
namespace php2cpp
{
	double to_float(int value)
	{
		return (double) value;
	}

	double to_float(long value)
	{
		return (double) value;
	}

	double to_float(long long value)
	{
		return (double) value;
	}

	double to_float(unsigned value)
	{
		return (double) value;
	}

	double to_float(unsigned long value)
	{
		return (double) value;
	}

	double to_float(unsigned long long value)
	{
		return (double) value;
	}

	double to_float(float value)
	{
		return (double) value;
	}

	double to_float(double value)
	{
		return (double) value;
	}

	double to_float(long double value)
	{
		return (double) value;
	}

	double to_float(Php::Value value)
	{
		return value;
	}

	double to_float(std::string value)
	{
		return std::stod(value);
	}
}
