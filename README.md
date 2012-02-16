# PHP Annotations

This library provides parsing for annotations defined in class, method and
member doc comments. Annotations are made available as an object with array
access capabilities.

## Usage

```php
<?php
require 'SplClassLoader.php';
$loader = new SplClassLoader('anno', '/path/to/php-annotations/zeptech');
$loader->register();

$refClass = new ReflectionClass('myns\MyClass');
$classAnnotations = new Annotations($refClass);

$methodAnnotations = array();
foreach ($refClass->getMethods() AS $method) {
  $methodAnnotations[$method->getName()] = new Annotations($method);
}
```
*See <https://gist.github.com/221634> for an implementation of SplClassLoader*

## Supported annotation syntax

**All annotations are case insensitive.**

### Boolean

Boolean annotations values are defined as the existance or non-existance of an
annotation.

```php
<?php
/**
 * @Characteristic
 */
class MyClass {
}

$annotations = new Annotations(new ReflectionClass('MyClass'));
$annotations['Characteristic'] === true;

// Careful:
$annotations['AnotherCharacteristic'] === null;
isset($annotations['AnotherCharacteristic']) === false;
```

Instead, use the hasAnnotation($annotation) method:

```php
<?php
$annotations->hasAnnotation('Characteristic') === true;
$annotations->hasAnnotation('AnotherCharacteristic') === false;
```

### Single Value

Single value annotations can be defined as:

```php
<?php
/**
 * @LikesToEat cheese
 */
class MyClass {
    // ...
}

$annotations['LikesToEat'] === 'cheese';
```

Single values can be specified as arrays:

```php
<?php
/**
 * @LikesToEat [ cheese, kraft dinner, hot dogs ]
 */
// ...

$annotations['LikesToEat'] == array('cheese', 'kraft dinner', 'hot dogs');
```
Some values will be cast into their appropriate types; `true` and `false` will
be cast to their boolean equivalents and numeric values will be cast to either
int or float types.

### Parameters

Annotations can be defined with named parameter values as follows:

```php
<?php
/**
 * @LikesToEat weekend = [ chips, dip ], anytime = [ cheese, kraft dinner, hot dogs ]
 */
// ...

$annotations['likesToEat']['weekend'] == array('chips', 'dip');
$annotations['likesToEat']['anytime'] == array('cheese', 'kraft dinner', 'hot dogs');
```

* * *
**NOTE:** All parameter values can be surrounded by optional braces.
```php
<?php
/**
 * @LikesToEat([ cheese, kraft dinner, hot dogs ])
 * @DoesNotLikeToEat( morning = salad, night = [ toast, fruit ])
 */
```
* * *

