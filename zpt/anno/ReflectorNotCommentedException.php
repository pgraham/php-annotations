<?php
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
