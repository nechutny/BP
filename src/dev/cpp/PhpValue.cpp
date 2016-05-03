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
	class Value
	{
		public:
			/*
			 * Strings
			 */
			Value(std::string val)
			{
				type = Php::Type::String;
				this->t_string = val;
				this->cacheValid = false;
			}

			Value(const char* val)
			{
				type = Php::Type::String;
				this->t_string = std::string(val);
				this->cacheValid = false;
			}

			/*
			 * Integers
			 */
			Value(int val)
			{
				type = Php::Type::Numeric;
				this->t_int =val;
				this->cacheValid = false;
			}

			/*
			 * Float
			 */
			Value(double val)
			{
				type = Php::Type::Float;
				this->t_float = val;
				this->cacheValid = false;
			}

			/*
			 * Mixed
			 */
			Value(Php::Value &value)
			{
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
						this->t_bool = value.numericValue();
						break;
					case Php::Type::Float:
						this->t_float = value.floatValue();
						break;
					case Php::Type::String:
						this->t_string = value.stringValue();
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

			Value(php2cpp::Value &value)
			{
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
						this->t_bool = value.getInt();
						break;
					case Php::Type::Float:
						this->t_float = value.getFloat();
						break;
					case Php::Type::String:
						this->t_string = value.getString();
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

			/*
			 * Null
			 */
			Value(std::nullptr_t val)
			{
				type = Php::Type::Null;

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
				type = Php::Type::Null;
				this->cacheValid = false;

				return *this;
			}
			Value &operator=(const Php::Value &value)
			{
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
						this->t_bool = value.numericValue();
						break;
					case Php::Type::Float:
						this->t_float = value.floatValue();
						break;
					case Php::Type::String:
						this->t_string = value.stringValue();
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
			Value &operator=(php2cpp::Value &value)
			{
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
						this->t_bool = value.getInt();
						break;
					case Php::Type::Float:
						this->t_float = value.getFloat();
						break;
					case Php::Type::String:
						this->t_string = value.getString();
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
			Value &operator=(int16_t value)
			{
				type = Php::Type::Numeric;
				this->t_int = value;
				this->cacheValid = false;

				return *this;
			}
			Value &operator=(int32_t value)
			{
				type = Php::Type::Numeric;
				this->t_int = value;
				this->cacheValid = false;

				return *this;
			}
			Value &operator=(int64_t value)
			{
				type = Php::Type::Numeric;
				this->t_int = value;
				this->cacheValid = false;

				return *this;
			}
			Value &operator=(bool value)
			{
				type = Php::Type::Bool;
				this->t_bool = value;
				this->cacheValid = false;

				return *this;
			}
			Value &operator=(char value)
			{
				type = Php::Type::String;
				this->t_string = std::string(&value, &value +1);
				this->cacheValid = false;

				return *this;
			}
			Value &operator=(const std::string &value)
			{
				type = Php::Type::String;
				this->t_string = value;
				this->cacheValid = false;

				return *this;
			}
			Value &operator=(const char *value)
			{
				type = Php::Type::String;
				this->t_string = std::string(value);
				this->cacheValid = false;

				return *this;
			}
			Value &operator=(double value)
			{
				type = Php::Type::Float;
				this->t_float = value;
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
				if(value.type() == Php::Type::Float || this->getType() == Php::Type::Float)
				{
					type = Php::Type::Float;
					this->t_float = this->getFloat() + value.floatValue();
				}

				this->cacheValid = false;
				return *this;
				// TODO
			}
			Value &operator+=(const php2cpp::Value &value);
			Value &operator+=(int16_t value)
			{
				return *this;
			}
			Value &operator+=(int32_t value);
			Value &operator+=(int64_t value);
			Value &operator+=(bool value);
			Value &operator+=(char value);
			Value &operator+=(const std::string &value);
			Value &operator+=(const char *value);
			Value &operator+=(double value);

// ===[ Operator -= ]===================================================================================================

			/**
			 *  Subtract a value from the object
			 *  @param  value
			 *  @return Value
			 */
			Value &operator-=(const Value &value);
			Value &operator-=(int16_t value);
			Value &operator-=(int32_t value);
			Value &operator-=(int64_t value);
			Value &operator-=(bool value);
			Value &operator-=(char value);
			Value &operator-=(const std::string &value);
			Value &operator-=(const char *value);
			Value &operator-=(double value);

// ===[ Operator *= ]===================================================================================================

			/**
			 *  Multiply the object with a certain value
			 *  @param  value
			 *  @return Value
			 */
			Value &operator*=(const Value &value);
			Value &operator*=(int16_t value);
			Value &operator*=(int32_t value);
			Value &operator*=(int64_t value);
			Value &operator*=(bool value);
			Value &operator*=(char value);
			Value &operator*=(const std::string &value);
			Value &operator*=(const char *value);
			Value &operator*=(double value);

// ===[ Operator /= ]===================================================================================================

			/**
			 *  Divide the object with a certain value
			 *  @param  value
			 *  @return Value
			 */
			Value &operator/=(const Value &value);
			Value &operator/=(int16_t value);
			Value &operator/=(int32_t value);
			Value &operator/=(int64_t value);
			Value &operator/=(bool value);
			Value &operator/=(char value);
			Value &operator/=(const std::string &value);
			Value &operator/=(const char *value);
			Value &operator/=(double value);

// ===[ Operator %= ]===================================================================================================

			/**
			 *  Divide the object with a certain value, and get the rest
			 *  @param  value
			 *  @return Value
			 */
			Value &operator%=(const Value &value);
			Value &operator%=(int16_t value);
			Value &operator%=(int32_t value);
			Value &operator%=(int64_t value);
			Value &operator%=(bool value);
			Value &operator%=(char value);
			Value &operator%=(const std::string &value);
			Value &operator%=(const char *value);
			Value &operator%=(double value);

// ===[ Operator + ]====================================================================================================

			/**
			 *  Addition operator
			 *  @param  value
			 *  @return Value
			 */
			Value operator+(const Value &value);
			Value operator+(int16_t value);
			Value operator+(int32_t value);
			Value operator+(int64_t value);
			Value operator+(bool value);
			Value operator+(char value);
			Value operator+(const std::string &value);
			Value operator+(const char *value);
			Value operator+(double value);

// ===[ Operator - ]====================================================================================================

			/**
			 *  Subtraction operator
			 *  @param  value
			 *  @return Value
			 */
			Value operator-(const Value &value);
			Value operator-(int16_t value);
			Value operator-(int32_t value);
			Value operator-(int64_t value);
			Value operator-(bool value);
			Value operator-(char value);
			Value operator-(const std::string &value);
			Value operator-(const char *value);
			Value operator-(double value);

// ===[ Operator * ]====================================================================================================

			/**
			 *  Multiplication operator
			 *  @param  value
			 *  @return Value
			 */
			Value operator*(const Value &value);
			Value operator*(int16_t value);
			Value operator*(int32_t value);
			Value operator*(int64_t value);
			Value operator*(bool value);
			Value operator*(char value);
			Value operator*(const std::string &value);
			Value operator*(const char *value);
			Value operator*(double value);

// ===[ Operator / ]====================================================================================================

			/**
			 *  Division operator
			 *  @param  value
			 *  @return Value
			 */
			Value operator/(const Value &value);
			Value operator/(int16_t value);
			Value operator/(int32_t value);
			Value operator/(int64_t value);
			Value operator/(bool value);
			Value operator/(char value);
			Value operator/(const std::string &value);
			Value operator/(const char *value);
			Value operator/(double value);

// ===[ Operator % ]====================================================================================================

			/**
			 *  Modulus operator
			 *  @param  value
			 *  @return Value
			 */
			Value operator%(const Value &value);
			Value operator%(int16_t value);
			Value operator%(int32_t value);
			Value operator%(int64_t value);
			Value operator%(bool value);
			Value operator%(char value);
			Value operator%(const std::string &value);
			Value operator%(const char *value);
			Value operator%(double value);

// ===[ Getters ]=======================================================================================================

			Php::Type getType()
			{
				return type;
			}

#define IS_LONG 1
#define IS_DOUBLE 2
			Php::Type detectType()
			{
				if(cacheValid)
				{
					return this->cachedType;
				}

				if(this->getType() == Php::Type::String)
				{
					const char *tmp = this->t_string.c_str();
					long lval;
					double dval;
					char type = isNumeric(tmp, strlen(tmp), &lval, &dval, 0, NULL);

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
			char isNumeric(const char *str, size_t length, long *lval, double *dval, int allow_errors, int *oflow_info) /* {{{ */
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

			long getInt()
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
						return (long)php2cpp::to_float(this->t_string);
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

			std::string getString()
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
						return this->t_string;
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
			}

			double getFloat()
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
						return php2cpp::to_float(this->t_string);
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
			}

			bool getBool()
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
						return this->t_string.length() == 0 ? false : true;
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
			}



		protected:
			Php::Type type;

			/*
			 * Cache variable type
			 */
			Php::Type cachedType;
			bool cacheValid;

			union {
				std::string	t_string;
				bool		t_bool;
				long		t_int;
				double		t_float;

			};



			char count()
			{
				// TODO
				return 0;
			}
	};

}
