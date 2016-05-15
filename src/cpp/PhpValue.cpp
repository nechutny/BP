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

#define IS_LONG 1
#define IS_DOUBLE 2

namespace php2cpp
{
	class Value
	{
		public:
			~Value()
			{

			}

			Value()
			{

			}

			/*
			 * Null
			 */
			Value(std::nullptr_t val)
			{
				Php::out << "\n - create nullptr \n";
				type = Php::Type::Null;

				this->cacheValid = false;
			}

			/*
			 * Strings
			 */
			Value(std::string val)
			{
				Php::out << "\n - create string \n";
				type = Php::Type::String;
				this->t_string = new std::string(val);
				this->cacheValid = false;
			}

			Value(const char* val)
			{
				Php::out << "\n - create const char \n";
				type = Php::Type::String;
				this->t_string = new std::string(val);
				this->cacheValid = false;
			}

			/*
			 * Integers
			 */
			Value(int val)
			{
				Php::out << "\n - create int \n";
				this->type = Php::Type::Numeric;
				this->t_int = val;
				this->cacheValid = false;
			}

			Value(int64_t val)
			{
				Php::out << "\n - create int \n";
				this->type = Php::Type::Numeric;
				this->t_int = val;
				this->cacheValid = false;
			}

			/*
			 * Float
			 */
			Value(double val)
			{
				Php::out << "\n - create double \n";
				type = Php::Type::Float;
				this->t_float = val;
				this->cacheValid = false;
			}

			/*
			 * Mixed
			 */
			Value(Php::Value &value)
			{
				Php::out << "\n - create php \n";
				type = value.type();
				switch(type)
				{
					case Php::Type::Null:
						// nothing
						break;
					case Php::Type::Bool:
						this->t_bool = value.boolValue();
						break;
					case Php::Type::Numeric:
						this->t_int = value.numericValue();
						break;
					case Php::Type::Float:
						this->t_float = value.floatValue();
						break;
					case Php::Type::String:
						this->t_string = new std::string(value.stringValue());
						break;
						// TODO: implement arrays, resources, objects
					case Php::Type::Array:
					case Php::Type::Object:
					case Php::Type::Resource:
					case Php::Type::Constant:
					case Php::Type::ConstantArray:
					case Php::Type::Callable:
						break;
				}

				this->cacheValid = false;
			}

		Value(const Php::Value &value)
		{
			Php::out << "\n - create php \n";
			type = value.type();
			switch(type)
			{
				case Php::Type::Null:
					// nothing
					break;
				case Php::Type::Bool:
					this->t_bool = value.boolValue();
					break;
				case Php::Type::Numeric:
					this->t_int = value.numericValue();
					break;
				case Php::Type::Float:
					this->t_float = value.floatValue();
					break;
				case Php::Type::String:
					this->t_string = new std::string(value.stringValue());
					break;
					// TODO: implement arrays, resources, objects
				case Php::Type::Array:
				case Php::Type::Object:
				case Php::Type::Resource:
				case Php::Type::Constant:
				case Php::Type::ConstantArray:
				case Php::Type::Callable:
					break;
			}

			this->cacheValid = false;
		}

			Value(const php2cpp::Value &value)
			{
				Php::out << "\n - create php2cpp \n";
				type = value.getType();
				switch(type)
				{
					case Php::Type::Null:
						// nothing
						break;
					case Php::Type::Bool:
						this->t_bool = value.getBool();
						break;
					case Php::Type::Numeric:
						this->t_int = value.getInt();
						break;
					case Php::Type::Float:
						this->t_float = value.getFloat();
						break;
					case Php::Type::String:
						this->t_string = new std::string(value.getString());
						break;
						// TODO: implement arrays, resources, objects
					case Php::Type::Array:
					case Php::Type::Object:
					case Php::Type::Resource:
					case Php::Type::Constant:
					case Php::Type::ConstantArray:
					case Php::Type::Callable:
						break;
				}

				this->cacheValid = false;
			}

			Value(php2cpp::Value* value)
			{
				Php::out << "\n - create php2cpp \n";
				type = value->getType();
				switch(type)
				{
					case Php::Type::Null:
						// nothing
						break;
					case Php::Type::Bool:
						this->t_bool = value->getBool();
						break;
					case Php::Type::Numeric:
						this->t_int = value->getInt();
						break;
					case Php::Type::Float:
						this->t_float = value->getFloat();
						break;
					case Php::Type::String:
						this->t_string = new std::string(value->getString());
						break;
						// TODO: implement arrays, resources, objects
					case Php::Type::Array:
					case Php::Type::Object:
					case Php::Type::Resource:
					case Php::Type::Constant:
					case Php::Type::ConstantArray:
					case Php::Type::Callable:
						break;
				}

				this->cacheValid = false;
			}

// ===[ Operator = ]====================================================================================================

			/**
			 *  Assignment operator for various types
			 *  @param  value
			 *  @return Value
			 */
			Value &operator=(std::nullptr_t value)
			{
				Php::out << "\n - = nullptr \n";
				type = Php::Type::Null;
				this->cacheValid = false;

				return *this;
			}

			Value &operator=(int16_t value)
			{
				Php::out << "\n - = i16 \n";
				type = Php::Type::Numeric;
				this->t_int = value;
				this->cacheValid = false;

				return *this;
			}
			Value &operator=(int32_t value)
			{
				Php::out << "\n - = i32 \n";
				type = Php::Type::Numeric;
				this->t_int = value;
				this->cacheValid = false;

				return *this;
			}
			Value &operator=(int64_t value)
			{
				Php::out << "\n - = i64 \n";
				type = Php::Type::Numeric;
				this->t_int = value;
				this->cacheValid = false;

				return *this;
			}
			Value &operator=(bool value)
			{
				Php::out << "\n - = bool \n";
				type = Php::Type::Bool;
				this->t_bool = value;
				this->cacheValid = false;

				return *this;
			}
			Value &operator=(char value)
			{
				Php::out << "\n - = char \n";
				type = Php::Type::String;
				this->t_string = new std::string(&value, &value +1);
				this->cacheValid = false;

				return *this;
			}
			Value &operator=(const std::string &value)
			{
				Php::out << "\n - = string \n";
				type = Php::Type::String;
				this->t_string = new std::string(value);
				this->cacheValid = false;

				return *this;
			}
			Value &operator=(const char *value)
			{
				Php::out << "\n - = const char \n";
				type = Php::Type::String;
				this->t_string = new std::string(value);
				this->cacheValid = false;

				return *this;
			}
			Value &operator=(double value)
			{
				Php::out << "\n - = double \n";
				type = Php::Type::Float;
				this->t_float = value;
				this->cacheValid = false;

				return *this;
			}

		Value &operator=(php2cpp::Value value)
		{
			Php::out << "\n - = php2cpp \n";
			type = value.getType();
			switch(type)
			{
				case Php::Type::Null:
					// nothing
					break;
				case Php::Type::Bool:
					this->t_bool = value.getBool();
					break;
				case Php::Type::Numeric:
					this->t_int = value.getInt();
					break;
				case Php::Type::Float:
					this->t_float = value.getFloat();
					break;
				case Php::Type::String:
					this->t_string = new std::string(value.getString());
					break;
					// TODO: implement arrays, resources, objects
				case Php::Type::Array:
				case Php::Type::Object:
				case Php::Type::Resource:
				case Php::Type::Constant:
				case Php::Type::ConstantArray:
				case Php::Type::Callable:
					break;
			}
			this->cacheValid = false;

			return *this;
		}

		Value &operator=(Php::Value value)
		{
			Php::out << "\n - = php \n";
			Php::out << "\nAssign string: " << value << " / " << value.type() << "\n";
			type = value.type();
			switch(type)
			{
				case Php::Type::Bool:
					this->t_bool = value.boolValue();
					Php::out << "\n - (y) bool \n";
					break;
				case Php::Type::Numeric:
					this->t_int = value.numericValue();
					Php::out << "\n - (y) numeric \n";
					break;
				case Php::Type::Float:
					this->t_float = value.floatValue();
					Php::out << "\n - (y) float \n";
					break;
				case Php::Type::String:
					this->t_string = new std::string(value.stringValue());
					Php::out << "\n - (y) string \n";
					break;
					// TODO: implement arrays, resources, objects
				case Php::Type::Array:
				case Php::Type::Object:
				case Php::Type::Resource:
				case Php::Type::Constant:
				case Php::Type::ConstantArray:
				case Php::Type::Callable:
					break;
				case Php::Type::Null:
				default:
				{
					// PHP-CPP bug - null is not null :)
					/*const char* tmp = value.stringValue().c_str();
					double dval;
					long lval;
					char ptype = this->isNumeric(tmp, strlen(tmp), &lval, &dval, 1, NULL);
					if(ptype == IS_LONG)
					{
						type = Php::Type::Numeric;
						this->t_int = lval;

						Php::out << "\n - long \n";
					}
					else if(ptype == IS_DOUBLE)
					{
						type = Php::Type::Float;
						this->t_float = dval;

						Php::out << "\n - double \n";
					}
					else if(strlen(tmp) == 0)
					{
						type = Php::Type::Null;

						Php::out << "\n - null \n";
					}
					else
					{
						type = Php::Type::String;
						this->t_string = new std::string(tmp);

						Php::out << "\n - string \n";
					}*/
					break;
				}
			}
			this->cacheValid = false;

			return *this;
		}


			// TODO: Hash member
			//Value &operator=(const Php::HashMember<std::string> &value);
			//Value &operator=(const Php::HashMember<int> &value);

// ===[ Operator += ]===================================================================================================

			/**
			 *  Add a value to the object
			 *  @param  value
			 *  @return Value
			 */
			Value &operator+=(const Php::Value &value)
			{
				double dval;
				long lval;
				char itype = -1;

				if(value.type() == Php::Type::String)
				{ // String? Detect type from string value
					const char* input = value.stringValue().c_str();
					itype = this->isNumeric(input, strlen(input), &lval, &dval, 1, NULL);

				}

				if(itype == -1)
				{ // Not string
					if(value.type() == Php::Type::Float || this->detectType() == Php::Type::Float)
					{ // Any float?
						type = Php::Type::Float;
						this->t_float = this->getFloat() + value.floatValue();
					}
					else
					{ // Numerics
						type = Php::Type::Numeric;
						this->t_int = this->getInt() + value.numericValue();
					}
				}
				else
				{
					// string
					if(itype == 0)
					{
						// nothing to do - only convert to int/float

						if(this->detectType() == Php::Type::Float)
						{
							this->t_float = this->getFloat();
							this->type = Php::Type::Float;
						}
						else
						{
							this->t_int = this->getInt();
							this->type = Php::Type::Numeric;
						}
					}
					else if(itype == IS_DOUBLE)
					{
						this->t_float = this->getFloat() + dval;
						this->type = Php::Type::Float;
					}
					else
					{
						if(this->detectType() == Php::Type::Float)
						{
							this->t_float = this->getFloat() + lval;
							this->type = Php::Type::Float;
						}
						else
						{
							this->t_int = this->getInt() + lval;
							this->type = Php::Type::Numeric;
						}
					}


				}
				this->cacheValid = false;

				return *this;
			}
			Value &operator+=(php2cpp::Value &value)
			{
				Php::Type typeIn = value.detectType();
				Php::Type typeThis = value.detectType();

				if(typeIn == Php::Type::Float || typeThis == Php::Type::Float)
				{
					this->t_float = this->getFloat() + value.getFloat();
					this->type = Php::Type::Float;
				}
				else
				{
					this->t_int = this->getInt() + value.getInt();
					this->type = Php::Type::Numeric;
				}
				this->cacheValid = false;

				return *this;
			}
			Value &operator+=(int16_t value)
			{
				if(this->detectType() == Php::Type::Float)
				{
					this->t_float = this->getFloat()+value;
					this->type = Php::Type::Float;
				}
				else
				{
					this->t_int = this->getInt()+value;
					this->type = Php::Type::Numeric;
				}
				this->cacheValid = false;

				return *this;
			}
			Value &operator+=(int32_t value)
			{
				if(this->detectType() == Php::Type::Float)
				{
					this->t_float = this->getFloat()+value;
					this->type = Php::Type::Float;
				}
				else
				{
					this->t_int = this->getInt()+value;
					this->type = Php::Type::Numeric;
				}
				this->cacheValid = false;

				return *this;
			}
			Value &operator+=(int64_t value)
			{
				if(this->detectType() == Php::Type::Float)
				{
					this->t_float = this->getFloat()+value;
					this->type = Php::Type::Float;
				}
				else
				{
					this->t_int = this->getInt()+value;
					this->type = Php::Type::Numeric;
				}
				this->cacheValid = false;

				return *this;
			}
			Value &operator+=(bool value)
			{
				if(this->detectType() == Php::Type::Float)
				{
					this->t_float = this->getFloat()+(value ? 1 : 0);
					this->type = Php::Type::Float;
				}
				else
				{
					this->t_int = this->getInt()+(value ? 1 : 0);
					this->type = Php::Type::Numeric;
				}
				this->cacheValid = false;

				return *this;
			}
			//Value &operator+=(char value);
			Value &operator+=(const std::string &value)
			{
				double dval;
				long lval;
				char itype = -1;

				const char* input = value.c_str();
				itype = this->isNumeric(input, strlen(input), &lval, &dval, 1, NULL);

				// string
				if(itype == 0)
				{
					// nothing to do - only convert to int/float
					if(this->detectType() == Php::Type::Float)
					{
						this->t_float = this->getFloat();
						this->type = Php::Type::Float;
					}
					else
					{
						this->t_int = this->getInt();
						this->type = Php::Type::Numeric;
					}
				}
				else if(itype == IS_DOUBLE)
				{
					this->t_float = this->getFloat() + dval;
					this->type = Php::Type::Float;
				}
				else
				{
					if(this->detectType() == Php::Type::Float)
					{
						this->t_float = this->getFloat() + lval;
						this->type = Php::Type::Float;
					}
					else
					{
						this->t_int = this->getInt() + lval;
						this->type = Php::Type::Numeric;
					}
				}

				this->cacheValid = false;

				return *this;
			}
			Value &operator+=(const char *value)
			{
				Php::out << "\n - const char " << (uint32_t)type << "\n";

				double dval;
				long lval;
				char itype = -1;

				itype = this->isNumeric(value, strlen(value), &lval, &dval, 1, NULL);

				Php::out << "\n - const char " << (uint32_t)type << " / " << (uint32_t)itype << "\n";

				// string
				if(itype == 0)
				{
					// nothing to do - only convert to int/float
					if(this->detectType() == Php::Type::Float)
					{
						this->t_float = this->getFloat();
						this->type = Php::Type::Float;
					}
					else
					{
						this->t_int = this->getInt();
						this->type = Php::Type::Numeric;
					}
				}
				else if(itype == IS_DOUBLE)
				{
					this->t_float = this->getFloat() + dval;
					this->type = Php::Type::Float;
				}
				else
				{
					if(this->detectType() == Php::Type::Float)
					{
						this->t_float = this->getFloat() + lval;
						this->type = Php::Type::Float;
					}
					else
					{
						this->t_int = this->getInt() + lval;
						this->type = Php::Type::Numeric;
					}
				}

				this->cacheValid = false;

				return *this;
			}
			Value &operator+=(double value)
			{
				this->t_float = this->getFloat()+value;
				this->type = Php::Type::Float;

				return *this;
			}
			Value &operator+=(std::nullptr_t val)
			{
				if(this->detectType() == Php::Type::Float)
				{
					this->t_float = this->getFloat();
					this->type = Php::Type::Float;
				}
				else
				{
					this->t_int = this->getInt();
					this->type = Php::Type::Numeric;
				}


				return *this;
			}

// ===[ Operator -= ]===================================================================================================

			/**
			 *  Subtract a value from the object
			 *  @param  value
			 *  @return Value
			 */
			Value &operator-=(const Php::Value &value)
			{
				double dval;
				long lval;
				char itype = -1;

				if(value.type() == Php::Type::String)
				{ // String? Detect type from string value
					const char* input = value.stringValue().c_str();
					itype = this->isNumeric(input, strlen(input), &lval, &dval, 1, NULL);

				}

				if(itype == -1)
				{ // Not string
					if(value.type() == Php::Type::Float || this->detectType() == Php::Type::Float)
					{ // Any float?
						type = Php::Type::Float;
						this->t_float = this->getFloat() - value.floatValue();
					}
					else
					{ // Numerics
						type = Php::Type::Numeric;
						this->t_int = this->getInt() - value.numericValue();
					}
				}
				else
				{
					// string
					if(itype == 0)
					{
						// nothing to do - only convert to int/float

						if(this->detectType() == Php::Type::Float)
						{
							this->t_float = this->getFloat();
							this->type = Php::Type::Float;
						}
						else
						{
							this->t_int = this->getInt();
							this->type = Php::Type::Numeric;
						}
					}
					else if(itype == IS_DOUBLE)
					{
						this->t_float = this->getFloat() - dval;
						this->type = Php::Type::Float;
					}
					else
					{
						if(this->detectType() == Php::Type::Float)
						{
							this->t_float = this->getFloat() - lval;
							this->type = Php::Type::Float;
						}
						else
						{
							this->t_int = this->getInt() - lval;
							this->type = Php::Type::Numeric;
						}
					}


				}
				this->cacheValid = false;

				return *this;
			}
		Value &operator-=(php2cpp::Value &value)
		{
			Php::Type typeIn = value.detectType();
			Php::Type typeThis = value.detectType();

			if(typeIn == Php::Type::Float || typeThis == Php::Type::Float)
			{
				this->t_float = this->getFloat() - value.getFloat();
				this->type = Php::Type::Float;
			}
			else
			{
				this->t_int = this->getInt() - value.getInt();
				this->type = Php::Type::Numeric;
			}
			this->cacheValid = false;

			return *this;
		}
		Value &operator-=(int16_t value)
		{
			if(this->detectType() == Php::Type::Float)
			{
				this->t_float = this->getFloat()-value;
				this->type = Php::Type::Float;
			}
			else
			{
				this->t_int = this->getInt()-value;
				this->type = Php::Type::Numeric;
			}
			this->cacheValid = false;

			return *this;
		}
		Value &operator-=(int32_t value)
		{
			if(this->detectType() == Php::Type::Float)
			{
				this->t_float = this->getFloat()-value;
				this->type = Php::Type::Float;
			}
			else
			{
				this->t_int = this->getInt()-value;
				this->type = Php::Type::Numeric;
			}
			this->cacheValid = false;

			return *this;
		}
		Value &operator-=(int64_t value)
		{
			if(this->detectType() == Php::Type::Float)
			{
				this->t_float = this->getFloat()-value;
				this->type = Php::Type::Float;
			}
			else
			{
				this->t_int = this->getInt()-value;
				this->type = Php::Type::Numeric;
			}
			this->cacheValid = false;

			return *this;
		}
		Value &operator-=(bool value)
		{
			if(this->detectType() == Php::Type::Float)
			{
				this->t_float = this->getFloat()-(value ? 1 : 0);
				this->type = Php::Type::Float;
			}
			else
			{
				this->t_int = this->getInt()-(value ? 1 : 0);
				this->type = Php::Type::Numeric;
			}
			this->cacheValid = false;

			return *this;
		}
		//Value &operator+=(char value);
		Value &operator-=(const std::string &value)
		{
			double dval;
			long lval;
			char itype = -1;

			const char* input = value.c_str();
			itype = this->isNumeric(input, strlen(input), &lval, &dval, 1, NULL);

			// string
			if(itype == 0)
			{
				// nothing to do - only convert to int/float
				if(this->detectType() == Php::Type::Float)
				{
					this->t_float = this->getFloat();
					this->type = Php::Type::Float;
				}
				else
				{
					this->t_int = this->getInt();
					this->type = Php::Type::Numeric;
				}
			}
			else if(itype == IS_DOUBLE)
			{
				this->t_float = this->getFloat() - dval;
				this->type = Php::Type::Float;
			}
			else
			{
				if(this->detectType() == Php::Type::Float)
				{
					this->t_float = this->getFloat() - lval;
					this->type = Php::Type::Float;
				}
				else
				{
					this->t_int = this->getInt() - lval;
					this->type = Php::Type::Numeric;
				}
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator-=(const char *value)
		{
			Php::out << "\n - const char " << (uint32_t)type << "\n";

			double dval;
			long lval;
			char itype = -1;

			itype = this->isNumeric(value, strlen(value), &lval, &dval, 1, NULL);

			Php::out << "\n - const char " << (uint32_t)type << " / " << (uint32_t)itype << "\n";

			// string
			if(itype == 0)
			{
				// nothing to do - only convert to int/float
				if(this->detectType() == Php::Type::Float)
				{
					this->t_float = this->getFloat();
					this->type = Php::Type::Float;
				}
				else
				{
					this->t_int = this->getInt();
					this->type = Php::Type::Numeric;
				}
			}
			else if(itype == IS_DOUBLE)
			{
				this->t_float = this->getFloat() - dval;
				this->type = Php::Type::Float;
			}
			else
			{
				if(this->detectType() == Php::Type::Float)
				{
					this->t_float = this->getFloat() - lval;
					this->type = Php::Type::Float;
				}
				else
				{
					this->t_int = this->getInt() - lval;
					this->type = Php::Type::Numeric;
				}
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator-=(double value)
		{
			this->t_float = this->getFloat()-value;
			this->type = Php::Type::Float;

			return *this;
		}
		Value &operator-=(std::nullptr_t val)
		{
			if(this->detectType() == Php::Type::Float)
			{
				this->t_float = this->getFloat();
				this->type = Php::Type::Float;
			}
			else
			{
				this->t_int = this->getInt();
				this->type = Php::Type::Numeric;
			}


			return *this;
		}

// ===[ Operator *= ]===================================================================================================

			/**
			 *  Multiply the object with a certain value
			 *  @param  value
			 *  @return Value
			 */
			Value &operator*=(const Php::Value &value)
			{
				double dval;
				long lval;
				char itype = -1;

				if(value.type() == Php::Type::String)
				{ // String? Detect type from string value
					const char* input = value.stringValue().c_str();
					itype = this->isNumeric(input, strlen(input), &lval, &dval, 1, NULL);

				}

				if(itype == -1)
				{ // Not string
					if(value.type() == Php::Type::Float || this->detectType() == Php::Type::Float)
					{ // Any float?
						type = Php::Type::Float;
						this->t_float = this->getFloat() * value.floatValue();
					}
					else
					{ // Numerics
						type = Php::Type::Numeric;
						this->t_int = this->getInt() * value.numericValue();
					}
				}
				else
				{
					// string
					if(itype == 0)
					{
						// nothing to do - only convert to int/float

						if(this->detectType() == Php::Type::Float)
						{
							this->t_float = 0;
							this->type = Php::Type::Float;
						}
						else
						{
							this->t_int = 0;
							this->type = Php::Type::Numeric;
						}
					}
					else if(itype == IS_DOUBLE)
					{
						this->t_float = this->getFloat() * dval;
						this->type = Php::Type::Float;
					}
					else
					{
						if(this->detectType() == Php::Type::Float)
						{
							this->t_float = this->getFloat() * lval;
							this->type = Php::Type::Float;
						}
						else
						{
							this->t_int = this->getInt() * lval;
							this->type = Php::Type::Numeric;
						}
					}


				}
				this->cacheValid = false;

				return *this;
			}
		Value &operator*=(php2cpp::Value &value)
		{
			Php::Type typeIn = value.detectType();
			Php::Type typeThis = value.detectType();

			if(typeIn == Php::Type::Float || typeThis == Php::Type::Float)
			{
				this->t_float = this->getFloat() * value.getFloat();
				this->type = Php::Type::Float;
			}
			else
			{
				this->t_int = this->getInt() * value.getInt();
				this->type = Php::Type::Numeric;
			}
			this->cacheValid = false;

			return *this;
		}
		Value &operator*=(int16_t value)
		{
			if(this->detectType() == Php::Type::Float)
			{
				this->t_float = this->getFloat()*value;
				this->type = Php::Type::Float;
			}
			else
			{
				this->t_int = this->getInt()*value;
				this->type = Php::Type::Numeric;
			}
			this->cacheValid = false;

			return *this;
		}
		Value &operator*=(int32_t value)
		{
			if(this->detectType() == Php::Type::Float)
			{
				this->t_float = this->getFloat()*value;
				this->type = Php::Type::Float;
			}
			else
			{
				this->t_int = this->getInt()*value;
				this->type = Php::Type::Numeric;
			}
			this->cacheValid = false;

			return *this;
		}
		Value &operator*=(int64_t value)
		{
			if(this->detectType() == Php::Type::Float)
			{
				this->t_float = this->getFloat()*value;
				this->type = Php::Type::Float;
			}
			else
			{
				this->t_int = this->getInt()*value;
				this->type = Php::Type::Numeric;
			}
			this->cacheValid = false;

			return *this;
		}
		Value &operator*=(bool value)
		{
			if(this->detectType() == Php::Type::Float)
			{
				this->t_float = this->getFloat()*(value ? 1 : 0);
				this->type = Php::Type::Float;
			}
			else
			{
				this->t_int = this->getInt()*(value ? 1 : 0);
				this->type = Php::Type::Numeric;
			}
			this->cacheValid = false;

			return *this;
		}
		//Value &operator+=(char value);
		Value &operator*=(const std::string &value)
		{
			double dval;
			long lval;
			char itype = -1;

			const char* input = value.c_str();
			itype = this->isNumeric(input, strlen(input), &lval, &dval, 1, NULL);

			// string
			if(itype == 0)
			{
				// nothing to do - only convert to int/float
				if(this->detectType() == Php::Type::Float)
				{
					this->t_float = 0;
					this->type = Php::Type::Float;
				}
				else
				{
					this->t_int = 0;
					this->type = Php::Type::Numeric;
				}
			}
			else if(itype == IS_DOUBLE)
			{
				this->t_float = this->getFloat() * dval;
				this->type = Php::Type::Float;
			}
			else
			{
				if(this->detectType() == Php::Type::Float)
				{
					this->t_float = this->getFloat() * lval;
					this->type = Php::Type::Float;
				}
				else
				{
					this->t_int = this->getInt() * lval;
					this->type = Php::Type::Numeric;
				}
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator*=(const char *value)
		{
			Php::out << "\n - const char " << (uint32_t)type << "\n";

			double dval;
			long lval;
			char itype = -1;

			itype = this->isNumeric(value, strlen(value), &lval, &dval, 1, NULL);

			Php::out << "\n - const char " << (uint32_t)type << " / " << (uint32_t)itype << "\n";

			// string
			if(itype == 0)
			{
				// nothing to do - only convert to int/float
				if(this->detectType() == Php::Type::Float)
				{
					this->t_float = 0;
					this->type = Php::Type::Float;
				}
				else
				{
					this->t_int = 0;
					this->type = Php::Type::Numeric;
				}
			}
			else if(itype == IS_DOUBLE)
			{
				this->t_float = this->getFloat() * dval;
				this->type = Php::Type::Float;
			}
			else
			{
				if(this->detectType() == Php::Type::Float)
				{
					this->t_float = this->getFloat() * lval;
					this->type = Php::Type::Float;
				}
				else
				{
					this->t_int = this->getInt() * lval;
					this->type = Php::Type::Numeric;
				}
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator*=(double value)
		{
			this->t_float = this->getFloat()*value;
			this->type = Php::Type::Float;

			return *this;
		}
		Value &operator*=(std::nullptr_t val)
		{
			this->t_int = 0;
			this->type = Php::Type::Numeric;

			return *this;
		}

// ===[ Operator /= ]===================================================================================================

			/**
			 *  Divide the object with a certain value
			 *  @param  value
			 *  @return Value
			 */
			Value &operator/=(const Php::Value &value)
			{
				double dval = 0;
				long lval = 0;
				char itype = -1;

				if(value.type() == Php::Type::String)
				{ // String? Detect type from string value
					const char* input = value.stringValue().c_str();
					itype = this->isNumeric(input, strlen(input), &lval, &dval, 1, NULL);

				}

				if(itype == -1)
				{ // Not string
					if(value.floatValue() == 0)
					{
						type = Php::Type::Bool;
						this->t_bool = false;
					}
					else
					{
						type = Php::Type::Float;
						this->t_float = this->getFloat() / value.floatValue();
					}

				}
				else
				{
					// string
					if(itype == 0 || (dval == 0 && lval == 0))
					{
						this->t_bool = false;
						this->type = Php::Type::Bool;
					}
					else if(itype == IS_DOUBLE)
					{
						this->t_float = this->getFloat() / dval;
						this->type = Php::Type::Float;
					}
					else
					{
						this->t_float = this->getFloat() / lval;
						this->type = Php::Type::Float;
					}


				}
				this->cacheValid = false;

				return *this;
			}
		Value &operator/=(php2cpp::Value &value)
		{
			if(value.getFloat() == 0)
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			else
			{
				this->t_float = this->getFloat() / value.getFloat();
				this->type = Php::Type::Float;
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator/=(int16_t value)
		{
			if(this->getFloat() == 0)
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			else
			{
				this->t_float = this->getFloat() / value;
				this->type = Php::Type::Float;
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator/=(int32_t value)
		{
			if(this->getFloat() == 0)
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			else
			{
				this->t_float = this->getFloat() / value;
				this->type = Php::Type::Float;
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator/=(int64_t value)
		{
			if(this->getFloat() == 0)
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			else
			{
				this->t_float = this->getFloat() / value;
				this->type = Php::Type::Float;
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator/=(bool value)
		{
			if(value)
			{
				this->t_float = this->getFloat();
				this->type = Php::Type::Float;
			}
			else
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			this->cacheValid = false;

			return *this;
		}
		//Value &operator+=(char value);
		Value &operator/=(const std::string &value)
		{
			double dval = 0;
			long lval = 0;
			char itype = -1;

			const char* input = value.c_str();
			itype = this->isNumeric(input, strlen(input), &lval, &dval, 1, NULL);

			// string
			if(itype == 0 || (lval == 0 && dval == 0))
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			else if(itype == IS_DOUBLE)
			{
				this->t_float = this->getFloat() / dval;
				this->type = Php::Type::Float;
			}
			else
			{
				this->t_float = this->getFloat() / lval;
				this->type = Php::Type::Float;
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator/=(const char *value)
		{
			Php::out << "\n - const char " << (uint32_t)type << "\n";

			double dval = 0;
			long lval = 0;
			char itype = -1;

			itype = this->isNumeric(value, strlen(value), &lval, &dval, 1, NULL);

			Php::out << "\n - const char " << (uint32_t)type << " / " << (uint32_t)itype << "\n";

			// string
			if(itype == 0 || (lval == 0 && dval == 0))
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			else if(itype == IS_DOUBLE)
			{
				this->t_float = this->getFloat() / dval;
				this->type = Php::Type::Float;
			}
			else
			{
				this->t_float = this->getFloat() / lval;
				this->type = Php::Type::Float;
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator/=(double value)
		{
			if(value == 0)
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			else
			{
				this->t_float = this->getFloat() / value;
				this->type = Php::Type::Float;
			}

			return *this;
		}
		Value &operator/=(std::nullptr_t val)
		{
			this->t_bool = false;
			this->type = Php::Type::Bool;

			return *this;
		}

// ===[ Operator %= ]===================================================================================================

			/**
			 *  Divide the object with a certain value, and get the rest
			 *  @param  value
			 *  @return Value
			 */
			Value &operator%=(const Php::Value &value)
			{
				double dval = 0;
				long lval = 0;
				char itype = -1;

				if(value.type() == Php::Type::String)
				{ // String? Detect type from string value
					const char* input = value.stringValue().c_str();
					itype = this->isNumeric(input, strlen(input), &lval, &dval, 1, NULL);
				}

				if(itype == -1)
				{ // Not string
					if(value.numericValue() == 0)
					{
						type = Php::Type::Bool;
						this->t_bool = false;
					}
					else
					{
						type = Php::Type::Numeric;
						this->t_int = this->getInt() % value.numericValue();
					}
				}
				else
				{
					// string
					if(itype == 0 || (dval == 0 && lval == 0))
					{
						this->t_bool = false;
						this->type = Php::Type::Bool;
					}
					else
					{
						Php::out << "\n - int: " << (uint32_t)this->getInt() << " / " << (uint32_t)itype << "\n";

						this->t_int = this->getInt() % (itype == IS_LONG ? lval : (long)dval);
						this->type = Php::Type::Numeric;
					}
				}
				this->cacheValid = false;

				return *this;
			}
		Value &operator%=(php2cpp::Value &value)
		{
			if(value.getInt() == 0)
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			else
			{
				this->t_int = this->getInt() % value.getInt();
				this->type = Php::Type::Numeric;
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator%=(int16_t value)
		{
			if(this->getInt() == 0)
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			else
			{
				this->t_int = this->getInt() % value;
				this->type = Php::Type::Numeric;
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator%=(int32_t value)
		{
			if(this->getInt() == 0)
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			else
			{
				this->t_int = this->getInt() % value;
				this->type = Php::Type::Numeric;
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator%=(int64_t value)
		{
			if(this->getInt() == 0)
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			else
			{
				this->t_int = this->getInt() % value;
				this->type = Php::Type::Numeric;
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator%=(bool value)
		{
			if(value)
			{
				this->t_int = this->getInt();
				this->type = Php::Type::Numeric;
			}
			else
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			this->cacheValid = false;

			return *this;
		}
		//Value &operator+=(char value);
		Value &operator%=(const std::string &value)
		{
			double dval = 0;
			long lval = 0;
			char itype = -1;

			const char* input = value.c_str();
			itype = this->isNumeric(input, strlen(input), &lval, &dval, 1, NULL);

			// string
			if(itype == 0 || (lval == 0 && dval == 0))
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			else if(itype == IS_DOUBLE)
			{
				this->t_int = this->getInt() % (long)dval;
				this->type = Php::Type::Numeric;
			}
			else
			{
				this->t_int = this->getInt() % lval;
				this->type = Php::Type::Numeric;
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator%=(const char *value)
		{
			Php::out << "\n - const char " << (uint32_t)type << "\n";

			double dval = 0;
			long lval = 0;
			char itype = -1;

			itype = this->isNumeric(value, strlen(value), &lval, &dval, 1, NULL);

			Php::out << "\n - const char " << (uint32_t)type << " / " << (uint32_t)itype << "\n";

			// string
			if(itype == 0 || (lval == 0 && dval == 0))
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			else if(itype == IS_DOUBLE)
			{
				this->t_int = this->getInt() % (long)dval;
				this->type = Php::Type::Numeric;
			}
			else
			{
				this->t_int = this->getInt() % lval;
				this->type = Php::Type::Numeric;
			}

			this->cacheValid = false;

			return *this;
		}
		Value &operator%=(double value)
		{
			if((long)value == 0)
			{
				this->t_bool = false;
				this->type = Php::Type::Bool;
			}
			else
			{
				this->t_int = this->getInt() / (long)value;
				this->type = Php::Type::Numeric;
			}

			return *this;
		}
		Value &operator%=(std::nullptr_t val)
		{
			this->t_bool = false;
			this->type = Php::Type::Bool;

			return *this;
		}

// ===[ Operator + ]====================================================================================================

			/**
			 *  Addition operator
			 *  @param  value
			 *  @return Value
			 */
			Value operator+(const Php::Value &value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 += op2;

				return op1;
			}
			Value operator+(const Value &value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 += op2;

				return op1;
			}
			Value operator+(int16_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 += op2;

				return op1;
			}
			Value operator+(int32_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 += op2;

				return op1;
			}
			Value operator+(int64_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 += op2;

				return op1;
			}
			Value operator+(bool value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 += op2;

				return op1;
			}
			Value operator+(char value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 += op2;

				return op1;
			}
			Value operator+(const std::string &value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 += op2;

				return op1;
			}
			Value operator+(const char *value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 += op2;

				return op1;
			}
			Value operator+(double value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 += op2;

				return op1;
			}
			Value operator+(std::nullptr_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 += op2;

				return op1;
			}

// ===[ Operator - ]====================================================================================================

			/**
			 *  Subtraction operator
			 *  @param  value
			 *  @return Value
			 */
			Value operator-(const Value &value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 -= op2;

				return op1;
			}
			Value operator-(const Php::Value &value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 -= op2;

				return op1;
			}
			Value operator-(int16_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 -= op2;

				return op1;
			}
			Value operator-(int32_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 -= op2;

				return op1;
			}
			Value operator-(int64_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 -= op2;

				return op1;
			}
			Value operator-(bool value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 -= op2;

				return op1;
			}
			Value operator-(char value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 -= op2;

				return op1;
			}
			Value operator-(const std::string &value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 -= op2;

				return op1;
			}
			Value operator-(const char *value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 -= op2;

				return op1;
			}
			Value operator-(double value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 -= op2;

				return op1;
			}
		Value operator-(std::nullptr_t value)
		{
			php2cpp::Value op1(this), op2(value);

			op1 -= op2;

			return op1;
		}

// ===[ Operator * ]====================================================================================================

			/**
			 *  Multiplication operator
			 *  @param  value
			 *  @return Value
			 */
			Value operator*(const Value &value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 *= op2;

				return op1;
			}
			Value operator*(const Php::Value &value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 *= op2;

				return op1;
			}
			Value operator*(int16_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 *= op2;

				return op1;
			}
			Value operator*(int32_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 *= op2;

				return op1;
			}
			Value operator*(int64_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 *= op2;

				return op1;
			}
			Value operator*(bool value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 *= op2;

				return op1;
			}
			Value operator*(char value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 *= op2;

				return op1;
			}
			Value operator*(const std::string &value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 *= op2;

				return op1;
			}
			Value operator*(const char *value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 *= op2;

				return op1;
			}
			Value operator*(double value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 *= op2;

				return op1;
			}
			Value operator*(std::nullptr_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 *= op2;

				return op1;
			}

// ===[ Operator / ]====================================================================================================

			/**
			 *  Division operator
			 *  @param  value
			 *  @return Value
			 */
			Value operator/(const Value &value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 /= op2;

				return op1;
			}
			Value operator/(const Php::Value &value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 /= op2;

				return op1;
			}
			Value operator/(int16_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 /= op2;

				return op1;
			}
			Value operator/(int32_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 /= op2;

				return op1;
			}
			Value operator/(int64_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 /= op2;

				return op1;
			}
			Value operator/(bool value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 /= op2;

				return op1;
			}
			Value operator/(char value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 /= op2;

				return op1;
			}
			Value operator/(const std::string &value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 /= op2;

				return op1;
			}
			Value operator/(const char *value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 /= op2;

				return op1;
			}
			Value operator/(double value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 /= op2;

				return op1;
			}
			Value operator/(std::nullptr_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 /= op2;

				return op1;
			}

// ===[ Operator % ]====================================================================================================

			/**
			 *  Modulus operator
			 *  @param  value
			 *  @return Value
			 */
			Value operator%(const Value &value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 %= op2;

				return op1;
			}
			Value operator%(const Php::Value &value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 %= op2;

				return op1;
			}
			Value operator%(int16_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 %= op2;

				return op1;
			}
			Value operator%(int32_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 %= op2;

				return op1;
			}
			Value operator%(int64_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 %= op2;

				return op1;
			}
			Value operator%(bool value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 %= op2;

				return op1;
			}
			Value operator%(char value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 %= op2;

				return op1;
			}
			Value operator%(const std::string &value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 %= op2;

				return op1;
			}
			Value operator%(const char *value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 %= op2;

				return op1;
			}
			Value operator%(double value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 %= op2;

				return op1;
			}
			Value operator%(std::nullptr_t value)
			{
				php2cpp::Value op1(this), op2(value);

				op1 %= op2;

				return op1;
			}

// ===[ Lofical operator || ]===========================================================================================
		bool operator||(const Value &value)
		{
			if(this->getBool() || value.getBool())
			{
				return true;
			}

			return false;
		}
		bool operator||(const Php::Value &value)
		{
			if(this->getBool() || value.boolValue())
			{
				return true;
			}

			return false;
		}
		bool operator||(int16_t value)
		{
			if(this->getBool() || value)
			{
				return true;
			}

			return false;
		}
		bool operator||(int32_t value)
		{
			if(this->getBool() || value)
			{
				return true;
			}

			return false;
		}
		bool operator||(int64_t value)
		{
			if(this->getBool() || value)
			{
				return true;
			}

			return false;
		}
		bool operator||(bool value)
		{
			if(this->getBool() || value)
			{
				return true;
			}

			return false;
		}
		bool operator||(char value)
		{
			if(this->getBool() || value)
			{
				return true;
			}

			return false;
		}
		bool operator||(const std::string &value)
		{
			if(this->getBool() || value.length() > 0)
			{
				return true;
			}

			return false;
		}
		bool operator||(const char *value)
		{
			if(this->getBool() || strlen(value) > 0)
			{
				return true;
			}

			return false;
		}
		bool operator||(double value)
		{
			if(this->getBool() || value != 0)
			{
				return true;
			}

			return false;
		}
		bool operator||(std::nullptr_t value)
		{
			return false;
		}
// ===[ Getters ]=======================================================================================================

			Php::Type getType() const
			{
				return type;
			}


			Php::Type detectType()
			{
				if(cacheValid)
				{
					return this->cachedType;
				}

				if(this->getType() == Php::Type::String)
				{
					const char *tmp = this->t_string->c_str();
					long lval;
					double dval;
					char type = isNumeric(tmp, strlen(tmp), &lval, &dval, 1, NULL);

					this->cacheValid = true;

					if(type == IS_LONG)
					{
						return this->cachedType = Php::Type::Numeric;
					}
					else if(type == IS_DOUBLE)
					{
						return this->cachedType = Php::Type::Float;
					}

					return this->cachedType = Php::Type::String;
				}

				this->cacheValid = true;
				return this->cachedType = this->getType();
			}

			/*
			 * This function is from PHP 7 source code by Zend and modified for purpose of this project
			 *
			 	   +----------------------------------------------------------------------+
				   | Zend Engine                                                          |
				   +----------------------------------------------------------------------+
				   | Copyright (c) 1998-2016 Zend Technologies Ltd. (http://www.zend.com) |
				   +----------------------------------------------------------------------+
				   | This function source is subject to version 2.00 of the Zend license, |
				   | that is bundled with this package in the file LICENSE, and is        |
				   | available through the world-wide-web at the following url:           |
				   | http://www.zend.com/license/2_00.txt.                                |
				   | If you did not receive a copy of the Zend license and are unable to  |
				   | obtain it through the world-wide-web, please send a note to          |
				   | license@zend.com so we can mail you a copy immediately.              |
				   +----------------------------------------------------------------------+
				   | Authors: Andi Gutmans <andi@zend.com>                                |
				   |          Zeev Suraski <zeev@zend.com>                                |
				   |          Dmitry Stogov <dmitry@zend.com>                             |
				   +----------------------------------------------------------------------+
			 */
#define ZEND_IS_DIGIT(c) ((c) >= '0' && (c) <= '9')
#define MAX_LENGTH_OF_LONG 20
#define LONG_MIN_DIGITS "9223372036854775808"
			char isNumeric(const char *str, size_t length, long *lval, double *dval, int allow_errors, int *oflow_info) const
			{
				const char *ptr;
				int digits = 0, dp_or_e = 0;
				double local_dval = 0.0;
				unsigned char type;
				long tmp_lval = 0;
				int neg = 0;

				if (!length) {
					return 0;
				}

				if (oflow_info != NULL) {
					*oflow_info = 0;
				}

				/* Skip any whitespace
				 * This is much faster than the isspace() function */
				while (*str == ' ' || *str == '\t' || *str == '\n' || *str == '\r' || *str == '\v' || *str == '\f') {
					str++;
					length--;
				}
				ptr = str;

				if (*ptr == '-') {
					neg = 1;
					ptr++;
				} else if (*ptr == '+') {
					ptr++;
				}

				if (ZEND_IS_DIGIT(*ptr)) {
					/* Skip any leading 0s */
					while (*ptr == '0') {
						ptr++;
					}

					/* Count the number of digits. If a decimal point/exponent is found,
					 * it's a double. Otherwise, if there's a dval or no need to check for
					 * a full match, stop when there are too many digits for a long */
					for (type = IS_LONG; !(digits >= MAX_LENGTH_OF_LONG && (dval || allow_errors == 1)); digits++, ptr++) {
						check_digits:
						if (ZEND_IS_DIGIT(*ptr)) {
							tmp_lval = tmp_lval * 10 + (*ptr) - '0';
							continue;
						} else if (*ptr == '.' && dp_or_e < 1) {
							goto process_double;
						} else if ((*ptr == 'e' || *ptr == 'E') && dp_or_e < 2) {
							const char *e = ptr + 1;

							if (*e == '-' || *e == '+') {
								ptr = e++;
							}
							if (ZEND_IS_DIGIT(*e)) {
								goto process_double;
							}
						}

						break;
					}

					if (digits >= MAX_LENGTH_OF_LONG) {
						if (oflow_info != NULL) {
							*oflow_info = *str == '-' ? -1 : 1;
						}
						dp_or_e = -1;
						goto process_double;
					}
				} else if (*ptr == '.' && ZEND_IS_DIGIT(ptr[1])) {
					process_double:
					type = IS_DOUBLE;

					/* If there's a dval, do the conversion; else continue checking
					 * the digits if we need to check for a full match */
					if (dval) {
						local_dval = strtod(str, (char**)&ptr);
					} else if (allow_errors != 1 && dp_or_e != -1) {
						dp_or_e = (*ptr++ == '.') ? 1 : 2;
						goto check_digits;
					}
				} else {
					return 0;
				}

				if (ptr != str + length) {
					if (!allow_errors) {
						return 0;
					}
					if (allow_errors == -1) {
						//zend_error(E_NOTICE, "A non well formed numeric value encountered");
					}
				}

				if (type == IS_LONG) {
					if (digits == MAX_LENGTH_OF_LONG - 1) {
						int cmp = strcmp(&ptr[-digits], LONG_MIN_DIGITS);

						if (!(cmp < 0 || (cmp == 0 && *str == '-'))) {
							if (dval) {
								*dval = strtod(str, NULL);
							}
							if (oflow_info != NULL) {
								*oflow_info = *str == '-' ? -1 : 1;
							}

							return IS_DOUBLE;
						}
					}

					if (lval) {
						if (neg) {
							tmp_lval = -tmp_lval;
						}
						*lval = tmp_lval;
					}

					return IS_LONG;
				} else {
					if (dval) {
						*dval = local_dval;
					}

					return IS_DOUBLE;
				}
			}

			long getInt() const
			{
				switch(type)
				{
					case Php::Type::Null:
						return 0;
					case Php::Type::Bool:
						return this->t_bool ? 1 : 0;
					case Php::Type::Numeric:
						return this->t_int;
					case Php::Type::Float:
						return (long)this->t_float;
					case Php::Type::String:
						return (long)php2cpp::to_float(this->t_string->c_str());
					case Php::Type::Array:
						return this->count() > 0 ? 1 : 0;
					case Php::Type::Callable:
						return 1;
						// TODO
					case Php::Type::Object:
						return 1;
					case Php::Type::Resource:
						return 1;
					case Php::Type::Constant:
						return 1;
					case Php::Type::ConstantArray:
						return 1;
				}

				return 0;
			}

			std::string getString() const
			{
				switch(type)
				{
					case Php::Type::Null:
						return "";
					case Php::Type::Bool:
						return this->t_bool ? "1" : "";
					case Php::Type::Numeric:
						return php2cpp::to_string(this->t_int);
					case Php::Type::Float:
						return php2cpp::to_string(this->t_float);
					case Php::Type::String:
						return this->t_string->c_str();
					case Php::Type::Array:
						return "Array";
					case Php::Type::Callable:
						return "";
						// TODO
					case Php::Type::Object:
						return "";
					case Php::Type::Resource:
						return "";
					case Php::Type::Constant:
						return "";
					case Php::Type::ConstantArray:
						return "";
				}

				return "";
			}

			double getFloat() const
			{
				switch(type)
				{
					case Php::Type::Null:
						return 0.0;
					case Php::Type::Bool:
						return this->t_bool ? 1.0 : 0.0;
					case Php::Type::Numeric:
						return (double)this->t_int;
					case Php::Type::Float:
						return this->t_float;
					case Php::Type::String:
						return php2cpp::to_float(this->t_string->c_str());
					case Php::Type::Array:
						return this->count() > 0 ? 1.0 : 0.0;
					case Php::Type::Callable:
						return 1.0;
						// TODO
					case Php::Type::Object:
						return 1;
					case Php::Type::Resource:
						return 1;
					case Php::Type::Constant:
						return 1;
					case Php::Type::ConstantArray:
						return 1;
				}

				return 0.0;
			}

			bool getBool() const
			{
				switch(type)
				{
					case Php::Type::Null:
						return false;
					case Php::Type::Bool:
						return this->t_bool;
					case Php::Type::Numeric:
						return this->t_int == 0 ? false : true;
					case Php::Type::Float:
						return this->t_float == 0.0 ? false : true;
					case Php::Type::String:
						return strlen(this->t_string->c_str()) == 0 ? false : true;
					case Php::Type::Array:
						return this->count() > 0 ? true : false;
					case Php::Type::Callable:
						return true;
						// TODO
					case Php::Type::Object:
						return true;
					case Php::Type::Resource:
						return true;
					case Php::Type::Constant:
						return true;
					case Php::Type::ConstantArray:
						return true;
				}

				return false;
			}



		protected:
			Php::Type type;

			/*
			 * Cache variable type
			 */
			Php::Type cachedType;
			bool cacheValid;

			union {
				std::string*	t_string;
				bool			t_bool;
				long			t_int;
				double			t_float;

			};



			char count() const
			{
				// TODO
				return 0;
			}
	};

// ===[ Output ]========================================================================================================

	std::ostream &operator<<(std::ostream &stream, const Value &value)
	{
		return stream << (value.getString());
	}


	/*template <typename X, typename std::enable_if<std::is_integral<X>::value>::type* = nullptr>
	X &operator+=(X &x, const php2cpp::Value &value) { return x += static_cast<X>(value); }
	template <typename X, typename std::enable_if<std::is_integral<X>::value>::type* = nullptr>
	X &operator-=(X &x, const php2cpp::Value &value) { return x -= static_cast<X>(value); }
	template <typename X, typename std::enable_if<std::is_integral<X>::value>::type* = nullptr>
	X &operator*=(X &x, const php2cpp::Value &value) { return x *= static_cast<X>(value); }
	template <typename X, typename std::enable_if<std::is_integral<X>::value>::type* = nullptr>
	X &operator/=(X &x, const php2cpp::Value &value) { return x /= static_cast<X>(value); }
	template <typename X, typename std::enable_if<std::is_integral<X>::value>::type* = nullptr>
	X &operator%=(X &x, const php2cpp::Value &value) { return x %= static_cast<X>(value); }*/
}
