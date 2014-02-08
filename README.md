# PHP Annotations

This library provides parsing for annotations defined in class, method and
member doc comments. Annotations are made available as an object with array
access capabilities.

## Install

Install via [Composer](http://getcomposer.org)

    {
        "require": {
            "zeptech/annotations": "1.1.0"
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

### Factory

Projects that create multiple Annotations instances for the same doc comment, or
find it necessary to do a lot of argument passing to avoid this, may benefit
from the use of an AnnotationFactory.  AnnotationFactories will cache Annotation
instances based on the doc comment's md5 hash value. Computing this value is
generally faster than parsing the comment multiple times.

```php
<?php
use \zpt\anno\AnnotationFactory;

$factory = new AnnotationFactory;
$annos = $factory->get('stdclass');
```

## Annotation syntax

All annotations are case **insensitive**. The default value for all annotations
is `true`.

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

### Annotation values

A single annotation value can be defined as:

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

Some values will be cast into their expected types; strings `'true'` and
`'false'` will be cast to their boolean equivalents and numeric values will be
cast to either int or float types.

#### Lists
List values can be specified by providing a comma separated
list surrounded with brackets:

```php
<?php
/**
 * @LikesToEat [ cheese, kraft dinner, hot dogs ]
 */
// ...

$annotations['LikesToEat'] == array('cheese', 'kraft dinner', 'hot dogs');
```

A parsed list value will be represented as a PHP array:

```php
<?php
is_array($annotations['LikesToEat']) === true
```

#### Maps

A map value can be specified using named parameters:

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
**NOTE:** It is possible at this time to nest list values inside of another list or
to nest maps inside of another map or a list to use only json object start { and end with }.

```php
@MenuData {"root":{"child":{"arraychild":[0,1,2,3]}},"arraysroot":[1,2,3]}
```

* * *
**NOTE:** All annotation values can be surrounded by optional parentheses.

```php
<?php
/**
 * @LikesToEat([ cheese, kraft dinner, hot dogs ])
 * @DoesNotLikeToEat( morning = salad, night = [ toast, fruit ])
 */
```

