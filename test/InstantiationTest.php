<?php
namespace zpt\anno;

use PHPUnit\Framework\TestCase as TestCase;
use \ReflectionClass;
use \ReflectionFunction;

require_once __DIR__ . '/test-common.php';

/**
 * This class exercises instantiate Annotations instances using different 
 * arguments.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class InstantiationTest extends TestCase {

	public function testInstantiationFromArrayEmpty() {
		$annos = new Annotations(array());

		$this->assertInstanceOf('zpt\anno\Annotations', $annos);
	}

	public function testInstantiationFromArray() {
		$annos = new Annotations(array('characteristic' => 'value'));

		$this->assertInstanceOf('zpt\anno\Annotations', $annos);
		$this->assertTrue(isset($annos['characteristic']));
		$this->assertEquals('value', $annos['characteristic']);
	}

	public function testInstantiateWithReflector() {
		$annos = new Annotations(new ReflectionClass('stdclass'));

		$this->assertInstanceOf('zpt\anno\Annotations', $annos);
	}

	public function testInstantiateWithUncommentedReflector() {
		$this->expectException(ReflectorNotCommentedException::class);
		eval('function testFunction($arg1) {}');
		$fnRef = new ReflectionFunction('testFunction');
		$paramsRef = $fnRef->getParameters();
		$annos = new Annotations($paramsRef[0]);
	}

	public function testInstantiateWithObject() {
		$this->expectException(\InvalidArgumentException::class);
		$annos = new Annotations((object) array());
	}

	public function testIntantiateWithClassName() {
		$annos = new Annotations('stdclass');

		$this->assertInstanceOf('zpt\anno\Annotations', $annos);
	}

	public function testInstantiateWithString() {
		$annos = new Annotations("/** @Entity */");

		$this->assertInstanceOf('zpt\anno\Annotations', $annos);
	}
}
