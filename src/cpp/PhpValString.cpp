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


/*
 * 17.6.4.2.1 Namespace std [namespace.std]
 * The behavior of a C++ program is undefined if it adds declarations or definitions to namespace std or to a namespace within namespace std unless otherwise specified. A program may add a template specialization for any standard library template to namespace std only if the declaration depends on a user-defined type and the specialization meets the standard library requirements for the original template and is not explicitly prohibited.
*/

namespace php2cpp
{
	std::string to_string(int value)
	{
		return std::to_string(value);
	}

	std::string to_string(long value)
	{
		return std::to_string(value);
	}

	std::string to_string(long long value)
	{
		return std::to_string(value);
	}

	std::string to_string(unsigned value)
	{
		return std::to_string(value);
	}

	std::string to_string(unsigned long value)
	{
		return std::to_string(value);
	}

	std::string to_string(unsigned long long value)
	{
		return std::to_string(value);
	}

	std::string to_string(float value)
	{
		// %G instead of %f

		char buff[100];
		snprintf(buff, sizeof(buff), "%G", value);
		std::string tmp = buff;
		return tmp;
	}

	std::string to_string(double value)
	{
		// %G instead of %f

		char buff[100];
		snprintf(buff, sizeof(buff), "%G", value);
		std::string tmp = buff;
		return tmp;
	}

	std::string to_string(long double value)
	{
		// %G instead of %f

		char buff[100];
		snprintf(buff, sizeof(buff), "%G", (double)value);
		std::string tmp = buff;
		return tmp;
	}

	std::string to_string(const char* value)
    {
        return std::string(value);
    }

	std::string to_string(std::string value)
	{
	    return value;
	}

	std::string to_string(Php::Value value)
	{
		return value;
	}
}
