<?php
/**
 * =============================================================================
 * Copyright (c) 2013, Philip Graham
 * All rights reserved.
 *
 * This file is part of Reed and is licensed by the Copyright holder under the
 * 3-clause BSD License.	The full text of the license can be found in the
 * LICENSE.txt file included in the root directory of this distribution or at
 * the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
namespace zpt\anno;

use \PHPUnit_Framework_TestCase as TestCase;
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

	/**
	 * @expectedException zpt\anno\ReflectorNotCommentedException
	 */
	public function testInstantiateWithUncommentedReflector() {
		eval('function testFunction($arg1) {}');
		$fnRef = new ReflectionFunction('testFunction');
		$paramsRef = $fnRef->getParameters();
		$annos = new Annotations($paramsRef[0]);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInstantiateWithObject() {
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
