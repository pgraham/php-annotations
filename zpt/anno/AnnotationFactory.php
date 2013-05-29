<?php
/**
 * =============================================================================
 * Copyright (c) 2013, Philip Graham
 * All rights reserved.
 *
 * This file is part of Reed and is licensed by the Copyright holder under
 * the 3-clause BSD License.	The full text of the license can be found in the
 * LICENSE.txt file included in the root directory of this distribution or at
 * the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
namespace zpt\anno;

use \ReflectionClass;
use \Reflector;

/**
 * Factory class for Annotation instances that provides caching.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class AnnotationFactory {

	private $_cache = array();

	/**
	 * Retrieve annotation set for given reflector.  Reflector can either be the
	 * name of a class to reflect or an instance of Reflector that implements
	 * the getDocComment() method. To create an empty Annotations object simply
	 * call this method with no arguments.
	 *
	 * @param mixed $reflector
	 * @return Annotations
	 */
	public function get($reflector = null) {
		$docComment = Annotations::parseDocComment($reflector);
		$cacheKey = md5((string) $docComment);
		if (array_key_exists($cacheKey, $this->_cache)) {
			return $this->_cache[$cacheKey];
		}

		$annotations = new Annotations($docComment);
		$this->_cache[$cacheKey] = $annotations;
		return $annotations;
	}
}
