recompilr
====
PHP 5.5+ factory runtime class compiler

[![Build Status](https://travis-ci.org/jgswift/recompilr.png?branch=master)](https://travis-ci.org/jgswift/recompilr)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jgswift/recompilr/badges/quality-score.png?s=87a44242339b2b007df16d5847b06c0246500931)](https://scrutinizer-ci.com/g/jgswift/recompilr/)
[![Latest Stable Version](https://poser.pugx.org/jgswift/recompilr/v/stable.svg)](https://packagist.org/packages/jgswift/recompilr)
[![License](https://poser.pugx.org/jgswift/recompilr/license.svg)](https://packagist.org/packages/jgswift/recompilr)
[![Coverage Status](https://coveralls.io/repos/jgswift/recompilr/badge.png?branch=master)](https://coveralls.io/r/jgswift/recompilr?branch=master)

## Description

recompilr can take a class definition and recompile it in runtime using a unique hash. 
This essentially allows an application to redeclare classes at runtime.
An interface is provided to keep track of aliases and handle instantiation of aliased classes.

## Installation

Install via cli using [composer](https://getcomposer.org/):
```sh
php composer.phar require jgswift/recompilr:0.1.*
```

Install via composer.json using [composer](https://getcomposer.org/):
```json
{
    "require": {
        "jgswift/recompilr": "0.1.*"
    }
}
```

## Dependency

* php 5.5+
* [adlawson\veval.php](http://github.com/adlawson/veval.php)

## Usage

### Basic compiling and instantiation

```php
// # path/to/FooClass.php
class FooClass {
    /* */
}

// usage
recompilr\execute('FooClass','path/to/FooClass.php');

$foo = recompilr\make('FooClass');

var_dump($foo); // (object) FooClass_*hash
```

### Recompiling

When it is determined that a class definition was updated, all updated classes may be recompiled
by using ```recompilr\all```.

```php
// change path/to/FooClass.php while application is running

recompilr\all();
```

**Note: will not compile files where bracketed namespaces are used**
**Note: all compiled classes are final and may not be inherited from**

### Binary handling

#### Saving to file
```php
recompilr\execute('FooClass','path/to/FooClass.php');

recompilr\binary('path/to/binary.rcx');
```

#### Loading from file

```php
recompilr\load('path/to/binary.rcx');

$foo = recompilr\make('FooClass');

var_dump($foo); // (object) FooClass_*hash
```