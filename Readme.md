# php2cpp: Bachelor thesis

This repository contains Stanislav Nechutný's bachelor thesis written at Faculty of information technology, Brno university of technology.

## What it is

It's tool for automated translation of subselect of PHP language into C++ which can be compiled into PHP extension, loaded in same way as eg. GD library and used from PHP source.

Supported constructions
  - Function definition with arguments and default values
  - Calling built-in and user defined functions
  - Expressions with most of operators
  - Integers, floats, arrays, strings, booleans
  - if, for, while, do-while...

Used technology
  - PHP 5.6
  - PHP-CPP 1.5.3
  - Magic

TODO
  - Support for class, interface, namespace, trait
  - Better data type detection

### Installation

Dependency is PHP-CPP library: http://www.php-cpp.com/download

Then just clone this repo and everything is ready.

### Usage

```sh
$ ./convert.php path/to/input.php
```

If it doesn't show any error, then you have now everything prepared for building, so

```sh
$ make
```

```sh
$ make install
```

License
----
**Apache 2.0**
