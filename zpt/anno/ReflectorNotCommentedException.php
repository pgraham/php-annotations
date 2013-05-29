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

use \Exception;

/**
 * Exception that is thrown when a \Reflector instance that does not provide a
 * getDocComment() method is used to try and instantiate an Annotations 
 * instance.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class ReflectorNotCommentedException extends Exception {

	public function __construct() {
		parent::__construct("Only Reflector implementations that provide a" .
			"getDocComment() method can be parsed for annotations");
	}
}
