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

namespace php2cpp
{
	enum class arrayType {
		type_boolean,
		type_int,
		type_float,
		type_string,
	};

	class phpArrayKey
	{
		public:
			phpArrayKey(std::string val)
			{
				type = arrayType::type_string;
				this->t_string = val;
			}

			phpArrayKey(const char* val)
			{
				type = arrayType::type_string;
				this->t_string = std::string(val);
			}

			phpArrayKey(int val)
			{
				type = arrayType::type_int;
				this->t_int =val;
			}

			phpArrayKey(float val)
			{
				type = arrayType::type_float;
				this->t_float = val;
			}

			phpArrayKey(double val)
			{
				type = arrayType::type_float;
				this->t_float = val;
			}



		protected:
			arrayType type;

			union {
				std::string	t_string;
				long		t_int;
				double		t_float;

			};
	};

}
