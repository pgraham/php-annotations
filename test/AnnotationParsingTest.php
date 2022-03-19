<?php
use zpt\anno\Annotations;
use PHPUnit\Framework\TestCase as TestCase;

require_once __DIR__ . '/test-common.php';

/**
 * This class tests the \anno\Annotations class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class AnnotationParsingTest extends TestCase {

  /**
   * Tests that a single line doc comment containing a single annotation is
   * parsed properly.
   */
  public function testInlineAnnotation() {
    $comment = "/** @Inline */";
    $annotations = new Annotations($comment);

    $this->assertTrue(isset($annotations['inline']));
  }

  public function testInlineAnnotationValue() {
    $comment = "/** @Inline inline value */";
    $annotations = new Annotations($comment);

    $this->assertEquals('inline value', $annotations['inline']);
  }

  public function testInlineBracketedAnnotationValue() {
    $comment = "/** @Inline (inline value) */";
    $annotations = new Annotations($comment);

    $this->assertEquals('inline value', $annotations['inline']);
  }

  public function testInlineCompactAnnotationValue() {
    $comment = "/**@Inline inline value*/";
    $annotations = new Annotations($comment);

    $this->assertEquals('inline value', $annotations['inline']);
  }

  public function testInlineCompactBracketedAnnotationValue() {
    $comment = "/**@Inline(inline value)*/";
    $annotations = new Annotations($comment);

    $this->assertEquals('inline value', $annotations['inline']);
  }

  public function testInlineParameterizedAnnotationValue() {
    $comment = "/** @Inline param = value */";
    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['inline']), $msg);
    $this->assertTrue(isset($annotations['inline']['param']), $msg);
    $this->assertEquals('value', $annotations['inline']['param'], $msg);
  }

  public function testCompactInlineParameterizedAnnotationValue() {
    $comment = "/**@Inline param = value*/";
    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['inline']), $msg);
    $this->assertTrue(isset($annotations['inline']['param']), $msg);
    $this->assertEquals('value', $annotations['inline']['param'], $msg);
  }

  public function testBracketedInlineParameterizedAnnotationValue() {
    $comment = "/** @Inline(param = value) */";
    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['inline']), $msg);
    $this->assertTrue(isset($annotations['inline']['param']), $msg);
    $this->assertEquals('value', $annotations['inline']['param'], $msg);
  }

  public function testCompactBracketedInlineParameterizedAnnotationValue() {
    $comment = "/**@Inline(param=value)*/";
    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['inline']), $msg);
    $this->assertTrue(isset($annotations['inline']['param']), $msg);
    $this->assertEquals('value', $annotations['inline']['param'], $msg);
  }

  public function testSingleUnvaluedAnnotation() {
    $comment = <<<'EOT'
/**
 * This is a comment that contains a single annotation without a value.
 *
 * @Entity
 */
EOT;
    
    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['entity']), $msg);
    $this->assertIsBool($annotations['entity'], $msg);
    $this->assertEquals(true, $annotations['entity'], $msg);
  }

  public function testSingleValuedAnnotation() {
    $comment = <<<'EOT'
/**
 * This is a comment that contains a single annotation with a single value
 *
 * @Entity is awesome!
 */
EOT;
    
    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['entity']), $msg);
    $this->assertIsString($annotations['entity'], $msg);
    $this->assertEquals('is awesome!', $annotations['entity'], $msg);
  }

  public function testSingleValueAsArray() {
    $comment = <<<'EOT'
/**
 * This is a comment that contains a single annotation with a single value
 *
 * @LikesToEat [ cheese, kraft dinner, hot dogs ]
 */
EOT;
    
    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['LikesToEat']), $msg);
    $this->assertIsArray($annotations['LikesToEat'], $msg);
    $this->assertEquals(
      array('cheese', 'kraft dinner', 'hot dogs'),
      $annotations['LikesToEat'],
      $msg);
  }

  public function testSingleAnnotationOneParam() {
    $comment = <<<'EOT'
/**
 * This is a comment that contains a single annotation with one parameter.
 *
 * @Entity(name = table)
 */
EOT;

    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['entity']), $msg);

    $annoVal = $annotations['entity'];
    $this->assertIsArray($annoVal, $msg);
    $this->assertArrayHasKey('name', $annoVal, $msg);
    $this->assertEquals('table', $annoVal['name'], $msg);
  }

  public function testSingleAnnotationTwoParams() {
    $comment = <<<'EOT'
/**
 * This is a comment that contains a single annotation with two parameters.
 *
 * @Entity(name = table, desc = Entity that represents a table)
 */
EOT;

    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['entity']), $msg);

    $annoVal = $annotations['entity'];
    $this->assertIsArray($annoVal, $msg);
    $this->assertArrayHasKey('name', $annoVal, $msg);
    $this->assertEquals('table', $annoVal['name'], $msg);
    $this->assertArrayHasKey('desc', $annoVal, $msg);
    $this->assertEquals('Entity that represents a table', $annoVal['desc'],
      $msg);
  }

  public function testSingleAnnotationMultipleParams() {
    $comment = <<<'EOT'
/**
 * This is a comment that contains a single annotations with multiple parameters
 *
 * @Entity(name = table, desc = Entity that represents a table, parent = database)
 */
EOT;

    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['entity']), $msg);

    $annoVal = $annotations['entity'];
    $this->assertIsArray($annoVal, $msg);
    $this->assertArrayHasKey('name', $annoVal, $msg);
    $this->assertEquals('table', $annoVal['name'], $msg);
    $this->assertArrayHasKey('desc', $annoVal, $msg);
    $this->assertEquals('Entity that represents a table', $annoVal['desc'],
      $msg);
    $this->assertArrayHasKey('parent', $annoVal, $msg);
    $this->assertEquals('database', $annoVal['parent'], $msg);
  }

  public function testSingleAnnotationMultipleParamsUnbracketed() {
    $comment = <<<'EOT'
/**
 * This is a comment that contains a single annotations with multiple parameters
 *
 * @Entity name = table, desc = Entity that represents a table, parent = database
 */
EOT;

    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['entity']), $msg);

    $annoVal = $annotations['entity'];
    $this->assertIsArray($annoVal, $msg);
    $this->assertArrayHasKey('name', $annoVal, $msg);
    $this->assertEquals('table', $annoVal['name'], $msg);
    $this->assertArrayHasKey('desc', $annoVal, $msg);
    $this->assertEquals('Entity that represents a table', $annoVal['desc'],
      $msg);
    $this->assertArrayHasKey('parent', $annoVal, $msg);
    $this->assertEquals('database', $annoVal['parent'], $msg);
  }

  public function testMultipleUnvaluedAnnotations() {
    $comment = <<<'EOT'
/**
 * This is a comment that contains multiple annotations with no parameters.
 *
 * @Hotdog
 * @Hamburger
 * @KraftDinner
 */
EOT;

    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['hotdog']), $msg);
    $this->assertTrue(isset($annotations['hamburger']), $msg);
    $this->assertTrue(isset($annotations['kraftdinner']), $msg);
  }

  public function testMultipleAnnotationsMultipleParams() {
    $comment = <<<'EOT'
/**
 * This is a comment that contains multiple annotations with multiple parameters
 *
 * @Hotdog(brand = Maple Leaf, bun = true, cooking_method = BBQ)
 * @Hamburger ( brand = Home Made, bun = true, cooking_method = BBQ )
 * @KraftDinner brand = Kraft, bun = false, cooking_method = stove
 */
EOT;

    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['hotdog']), $msg);
    $this->assertTrue(isset($annotations['hamburger']), $msg);
    $this->assertTrue(isset($annotations['kraftdinner']), $msg);

    $annoVal = $annotations['hotdog'];
    $this->assertIsArray($annoVal, $msg);
    $this->assertArrayHasKey('brand', $annoVal, $msg);
    $this->assertArrayHasKey('bun', $annoVal, $msg);
    $this->assertArrayHasKey('cooking_method', $annoVal, $msg);
    $this->assertEquals('Maple Leaf', $annoVal['brand'], $msg);
    $this->assertTrue($annoVal['bun'], $msg);
    $this->assertEquals('BBQ', $annoVal['cooking_method'], $msg);

    $annoVal = $annotations['hamburger'];
    $this->assertIsArray($annoVal, $msg);
    $this->assertArrayHasKey('brand', $annoVal, $msg);
    $this->assertArrayHasKey('bun', $annoVal, $msg);
    $this->assertArrayHasKey('cooking_method', $annoVal, $msg);
    $this->assertEquals('Home Made', $annoVal['brand'], $msg);
    $this->assertTrue($annoVal['bun'], $msg);
    $this->assertEquals('BBQ', $annoVal['cooking_method'], $msg);

    $annoVal = $annotations['kraftdinner'];
    $this->assertIsArray($annoVal, $msg);
    $this->assertArrayHasKey('brand', $annoVal, $msg);
    $this->assertArrayHasKey('bun', $annoVal, $msg);
    $this->assertArrayHasKey('cooking_method', $annoVal, $msg);
    $this->assertEquals('Kraft', $annoVal['brand'], $msg);
    $this->assertFalse($annoVal['bun'], $msg);
    $this->assertEquals('stove', $annoVal['cooking_method'], $msg);
  }

  public function testArrayValue() {
    $comment = <<<'EOT'
/**
 * This is a comment to test that array value are parsed properly.
 *
 * @HasArray(array = [ one, two, three ])
 * @HasTwoArrays(array1 = [ four, five, six ], array2 = [ seven, eight, nine ])
 * @HasThreeArrays(array1 = [ ten, eleven, twelve ], array2 = [ thirteen, fourteen, fifteen ], array3 = [ sixteen, seventeen, eighteen ])
 */
EOT;

    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['hasarray']), $msg);
    $this->assertTrue(isset($annotations['hastwoarrays']), $msg);
    $this->assertTrue(isset($annotations['hasthreearrays']), $msg);

    $hasArray = $annotations['hasarray'];
    $this->assertIsArray($hasArray, $msg);
    $this->assertArrayHasKey('array', $hasArray, $msg);
    $this->assertIsArray($hasArray['array'], $msg);
    $this->assertContains('one', $hasArray['array'], $msg);
    $this->assertContains('two', $hasArray['array'], $msg);
    $this->assertContains('three', $hasArray['array'], $msg);

    $hasTwoArrays = $annotations['hastwoarrays'];
    $this->assertIsArray($hasTwoArrays, $msg);
    $this->assertArrayHasKey('array1', $hasTwoArrays, $msg);
    $this->assertArrayHasKey('array2', $hasTwoArrays, $msg);
    $this->assertIsArray($hasTwoArrays['array1'], $msg);
    $this->assertIsArray($hasTwoArrays['array2'], $msg);
    $this->assertContains('four', $hasTwoArrays['array1'], $msg);
    $this->assertContains('five', $hasTwoArrays['array1'], $msg);
    $this->assertContains('six', $hasTwoArrays['array1'], $msg);
    $this->assertContains('seven', $hasTwoArrays['array2'], $msg);
    $this->assertContains('eight', $hasTwoArrays['array2'], $msg);
    $this->assertContains('nine', $hasTwoArrays['array2'], $msg);

    $hasThreeArrays = $annotations['hasthreearrays'];
    $this->assertIsArray($hasThreeArrays, $msg);
    $this->assertArrayHasKey('array1', $hasThreeArrays, $msg);
    $this->assertArrayHasKey('array2', $hasThreeArrays, $msg);
    $this->assertArrayHasKey('array3', $hasThreeArrays, $msg);
    $this->assertIsArray($hasThreeArrays['array1'], $msg);
    $this->assertIsArray($hasThreeArrays['array2'], $msg);
    $this->assertIsArray($hasThreeArrays['array3'], $msg);
    $this->assertContains('ten', $hasThreeArrays['array1'], $msg);
    $this->assertContains('eleven', $hasThreeArrays['array1'], $msg);
    $this->assertContains('twelve', $hasThreeArrays['array1'], $msg);
    $this->assertContains('thirteen', $hasThreeArrays['array2'], $msg);
    $this->assertContains('fourteen', $hasThreeArrays['array2'], $msg);
    $this->assertContains('fifteen', $hasThreeArrays['array2'], $msg);
    $this->assertContains('sixteen', $hasThreeArrays['array3'], $msg);
    $this->assertContains('seventeen', $hasThreeArrays['array3'], $msg);
    $this->assertContains('eighteen', $hasThreeArrays['array3'], $msg);

  }

  public function testQuotedValue() {
    $expected = "This is a description, contained in quotes, that has commas";
    $comment = <<<EOT
/**
 * This is a comment that contains a quoted value.
 *
 * @Description(value = "$expected")
 */
EOT;

    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['description']), $msg);

    $description = $annotations['description'];
    $this->assertIsArray($description, $msg);
    $this->assertArrayHasKey('value', $description, $msg);

    $this->assertEquals($expected, $description['value'], $msg);
  }

  public function testBooleanAnnotationValueTrue() {
    $comment = <<<EOT
/**
 * This is a comment which contains an explicit true value.
 *
 * @AreCool true
 */
EOT;

    $annotations = new Annotations($comment);

    $this->assertTrue($annotations['arecool']);
  }

  public function testBooleanAnnotationValueFalse() {
    $comment = <<<EOT
/**
 * This is a comment which contains an explicit false value.
 *
 * @WillFail false
 */
EOT;

    $annotations = new Annotations($comment);

    $this->assertFalse($annotations['willfail']);
  }

  public function testIntegerAnnotationValue() {
    $comment = <<<EOT
/**
 * This is a comment which contains an integer valued annotation.
 *
 * @Count 4
 */
EOT;

    $annotations = new Annotations($comment);

    $this->assertEquals(4, $annotations['count']);
  }

  public function testFloatAnnotationValue() {
    $comment = <<<EOT
/**
 * This is a comment which contains an integer valued annotation.
 *
 * @Pi 3.14
 */
EOT;

    $annotations = new Annotations($comment);

    $this->assertEquals(3.14, $annotations['pi']);
  }

  public function testFloatDotZeroAnnotationValue() {
    $comment = <<<EOT
/**
 * This is a comment which contains an integer valued annotation.
 *
 * @Pi 3.0
 */
EOT;

    $annotations = new Annotations($comment);

    $this->assertEquals(3.0, $annotations['pi']);
  }

  public function testMultipleValuedAnnotationsWithSameName() {
    $comment = <<<EOT
/**
 * This is a comment that contains multiple annotations with the same
 * name
 *
 * @param Value1
 * @param Value2
 */
EOT;

    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['param']), $msg);
    $this->assertIsArray($annotations['param'], $msg);

    $params = $annotations['param'];
    $this->assertCount(2, $params, $msg);
    $this->assertEquals(array('Value1', 'Value2'), $params, $msg);

    $comment = <<<EOT
/**
 * This is a comment that contains multiple annotations with the same name
 *
 * @param Value1
 * @param Value2
 * @param Value3
 */
EOT;

    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['param']), $msg);
    $this->assertIsArray($annotations['param'], $msg);

    $params = $annotations['param'];
    $this->assertCount(3, $params, $msg);
    $this->assertEquals(array('Value1', 'Value2', 'Value3'), $params, $msg);
  }

  public function testGrammarWhiteSpace() {
    $comment = <<<EOT
/**
 * This is a comment that contains parameters with whitespace between the
 * grammar elements.
 *
 * @Entity ( name = Entity1 , value = Value1 )
 */
EOT;

    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['entity']), $msg);

    $this->assertTrue(isset($annotations['entity']['name']), $msg);
    $this->assertEquals('Entity1', $annotations['entity']['name'], $msg);

    $this->assertTrue(isset($annotations['entity']['value']), $msg);
    $this->assertEquals('Value1', $annotations['entity']['value'], $msg);
  }

  public function testMultilineAnnotation() {
    $comment = <<<EOT
/**
 * This is a comment that contains a single annotation spread over multiple
 * lines.
 *
 * @Worker(
 *     name = "Slow Worker",
 *     speed = 5
 * )
 */
EOT;

    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);

    $this->assertTrue(isset($annotations['worker']), $msg);
    $this->assertTrue(isset($annotations['worker']['name']), $msg);
    $this->assertEquals('Slow Worker', $annotations['worker']['name'], $msg);
    $this->assertTrue(isset($annotations['worker']['speed']), $msg);
    $this->assertEquals(5, $annotations['worker']['speed'], $msg);
  }

  public function testJsonAnnotationValue() {
    $comment = <<<EOT
/**
 * This is a comment which contains an annotation defined using JSON syntax.
 *
 * @MenuData {"root":{"child":{"arraychild":[0,1,2,3]}},"arraysroot":[1,2,3]}
 */
EOT;

    $annotations = new Annotations($comment);
    $this->assertIsObject($annotations['menudata']);
    $this->assertIsObject($annotations['menudata']->root);
    $this->assertIsObject($annotations['menudata']->root->child);
    $this->assertIsArray($annotations['menudata']->root->child->arraychild);
    $this->assertEquals(
      $annotations['menudata']->root->child->arraychild,
      array(0,1,2,3)
    );

    $this->assertIsArray($annotations['menudata']->arraysroot);
    $this->assertEquals(
      $annotations['menudata']->arraysroot,
      array(1,2,3)
    );
  }

  public function testBracketedJsonAnnotationValue() {
    $comment = <<<EOT
/**
 * This is a comment which contains an annotation defined using JSON syntax.
 *
 * @MenuData({"root":{"child":{"arraychild":[0,1,2,3]}},"arraysroot":[1,2,3]})
 */
EOT;

    $annotations = new Annotations($comment);
    $this->assertIsObject($annotations['menudata']);
    $this->assertIsObject($annotations['menudata']->root);
    $this->assertIsObject($annotations['menudata']->root->child);
    $this->assertIsArray($annotations['menudata']->root->child->arraychild);
    $this->assertEquals(
      $annotations['menudata']->root->child->arraychild,
      array(0,1,2,3)
    );

    $this->assertIsArray($annotations['menudata']->arraysroot);
    $this->assertEquals(
      $annotations['menudata']->arraysroot,
      array(1,2,3)
    );
  }

  public function testNestedLists() {
    $comment = <<<EOT
/**
 * This is a comment which contains an annotation value with a nested list.
 *
 * @Nesting [ [0], [1], [2] ]
 */
EOT;

    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);
    $this->assertEquals(
      array( array(0), array(1), array(2) ),
      $annotations['nesting']
    );
  }

  public function testListsNestedInParameters() {
    $comment = <<<EOT
/**
 * This is a comment which contains a parameterized annotation value with a
 * nested list.
 *
 * @Nesting oneZero = [0], oneOne = [1], oneTwo = [2]
 */
EOT;

    $annotations = new Annotations($comment);
    $msg = print_r($annotations, true);
    $this->assertEquals(
      array(0),
      $annotations['nesting']['oneZero'],
      $msg
    );
    $this->assertEquals(
      array(1),
      $annotations['nesting']['oneOne'],
      $msg
    );
    $this->assertEquals(
      array(2),
      $annotations['nesting']['oneTwo'],
      $msg
    );
  }
}
