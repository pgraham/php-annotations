# PHP Annotations

This library provides parsing for annotations defined in class, method and
member doc comments. Annotations are made available as an object with array
access capabilities.

## Install

Install via [Composer](http://getcomposer.org)

    {
        "require": {
            "zeptech/annotations": "1.0.0"
        }
    }

## Instantiation

To instantiate simply pass a SPL Reflector instance with the `getDocComment()`
method to the Annotations constructor.

```php
<?php
use \zpt\anno\Annotations;

$classReflector = new ReflectionClass('MyClass');
$classAnnotations = new Annotations($classReflector);

$methodAnnotations = array();
foreach ($classReflector->getMethods() as $methodReflector) {
    $methodAnnotations[$methodReflector->getName()] = new Annotations($methodReflector);
}
```

## Supported annotation syntax

All annotations are case **insensitive.**

### Boolean

The default value for all annotations is `true`.

```php
<?php
/**
 * @Characteristic
 */
class MyClass {
}

$annotations = new Annotations(new ReflectionClass('MyClass'));
$annotations['Characteristic'] === true;
```

The absence of an annotation will result in a value of `null`:

```php
$annotations['AnotherCharacteristic'] === null;
isset($annotations['AnotherCharacteristic']) === false;
```

The hasAnnotation($annotation) method can be used to determine the existance of
an annotation, regardless of its value.

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

Some values will be cast into their appropriate types; The strings `'true'` and
`'false'` will be cast to their boolean equivalents and numeric values will be
cast to either int or float types.

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
**NOTE:** All annotation values can be surrounded by optional braces.

```php
<?php
/**
 * @LikesToEat([ cheese, kraft dinner, hot dogs ])
 * @DoesNotLikeToEat( morning = salad, night = [ toast, fruit ])
 */
```

* * *

