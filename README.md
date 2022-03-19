# PHP Annotations

This library provides parsing for annotations defined in class, method and
member doc comments. Annotations are made available as an object with array
access capabilities.

## Install

Install via [Composer](http://getcomposer.org)

    {
        "require": {
            "zeptech/annotations": "1.2.0"
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

An annotation can be explicitely set to have the value false:
```php
<?php
/**
 * @Characteristic false
 */

$annotations['Characteristic'] === false;
isset($annotations['Characteristic']) === true;
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
list surrounded with square brackets:

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

Lists can also be created by defining multiple annotations with the same name:
```php
/**
 * @LikesToDrink water
 * @LikesToDrink beer
 */
// ...

$annotations['LikesToDrink'] == array('water', 'beer');
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

#### Nesting

Lists can nested inside of lists or maps.
```php
/**
 * This is a comment which contains an annotation value with a nested list.
 *
 * @ListNest [ [0], [1], [2] ]
 * @MapNest oneZero = [0], oneOne = [1], oneTwo = [2]
 */
// ...

$annotation['listnest'] = array( array(0), array(1), array(2) );
$annotation['mapnest'] = array( 'oneZero' => array(0), 'oneOne' => array(1), 'oneTwo' => array(2) );
```

At this time, this is only supported for one level of nesting. Deep nesting is
not supported. Also, maps cannot be nested. If deep nesting or nested maps are
required, values specified as JSON objects will be parsed using `json_decode`.

```php
/**
 * @MenuData {"root":{"child":{"arraychild":[0,1,2,3]}},"arraysroot":[1,2,3]}
 */
```

Because of the use of `json_decode`, the parsed value will be an instance of
`stdClass` rather than an array:

```
$annotations['menudata']->root->child->arraychild = array( 0, 1, 2, 3 )
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

### Oddities

Mixing types when using multiple declarations will likely not cause an error but
isn't officially supported and may have some weird behaviour. The final
result will depend on the order of the types used.

```php
/**
 * @Yolo [ sounds, like, a, good, time ]
 * @Yolo [ but, is, it? ]
 * @Yolo yolo!
 */
```

Values can be enclosed in double quotes, which will be stripped, but this
feature it isn't well supported and probably doesn't behave the way one might
expect. This is something that might get proper handling in the future.

Also note that while annotation names are case insensitve, parameter names are
case sensitive. This is a result of the top level annotations being stored in an
object which implements custom accessors, but map values being stored
as native array instances.

## Contributing

Dev setup:

 1. [Install composer locally](https://getcomposer.org/download/)

        php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
        php composer-setup.php
        php -r "unlink('composer-setup.php');"

 2. Install dependencies:

        php composer.phar update

 3. Run Tests:

        ./vendor/bin/phpunit test

