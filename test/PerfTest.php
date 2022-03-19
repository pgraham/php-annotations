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

use PHPUnit\Framework\TestCase as TestCase;

require_once __DIR__ . '/test-common.php';

/**
 * This class tests that performance related assumptions hold true.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class PerfTest extends TestCase {

	/**
	 * Test that calculating the md5 hash of a string is faster than parsing it 
	 * for annotations. The entire premise of caching annotation instances is 
	 * based on this assumption. If this test fails then there is no performance 
	 * benefit to using an AnnotationFactory although there will still probably be 
	 * a memory gain.
	 */
	public function testHashVsParseSpeed() {
		global $shortComment, $mediumComment, $longComment;

		$shortHashTime = $this->timeHash($shortComment);
		$shortParseTime = $this->timeParse($shortComment);
		$this->assertTrue($shortHashTime < $shortParseTime);

		$mediumHashTime = $this->timeHash($mediumComment);
		$mediumParseTime = $this->timeParse($mediumComment);
		$this->assertTrue($mediumHashTime < $mediumParseTime);

		$longHashTime = $this->timeHash($longComment);
		$longParseTime = $this->timeParse($longComment);
		$this->assertTrue($longHashTime < $longParseTime);

//		echo "Short comment:\n";
//		echo "  Hash:  $shortHashTime\n";
//		echo "  Parse: $shortParseTime\n";
//
//		echo "Medium comment:\n";
//		echo "  Hash:  $mediumHashTime\n";
//		echo "  Parse: $mediumParseTime\n";
//
//		echo "Long comment:\n";
//		echo "  Hash:  $longHashTime\n";
//		echo "  Parse: $longParseTime\n";
	}

	private function timeHash($comment) {
		return $this->timer(function () use ($comment) {
			md5($comment);
		});
	}

	private function timeParse($comment) {
		return $this->timer(function () use ($comment) {
			AnnotationParser::getAnnotations($comment);
		});
	}

	private function timer($fn) {
		$times = array();

		for ($i = 0; $i < 10000; $i++) {
			$st = microtime(true);
			$fn();
			$et = microtime(true);

			$times[] = $et - $st;
		}

		return (array_reduce($times, function ($r, $i) {
			$r += $i;
			return $r;
		}) / count($times));

	}
}

$shortComment = <<<ANNO
/**
 * @Column
 */
ANNO;

$mediumComment = <<<ANNO
/**
 * @Id
 * @Column(name = id)
 */
ANNO;

$longComment = <<<ANNO
/**
 * Model class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 * @package anno\test
 *
 * @Entity(table = models)
 * @NoCrud
 */
ANNO;
