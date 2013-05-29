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

use \ArrayAccess;
use \InvalidArgumentException;
use \ReflectionClass;
use \Reflector;

/**
 * This class parses a given Reflector for annotations and provides array style
 * or method style access to them.	Only Reflectors that implement the
 * getDocComment method are supported.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class Annotations implements ArrayAccess {

	/**
	 * Parse a doc comment from a parameter or throw an exception.
	 */
	public static function parseDocComment($arg) {
		if (is_array($arg)) {
			return $arg;
		}

		if (is_object($arg)) {
			if (!($arg instanceof Reflector)) {
				throw new InvalidArgumentException();
			}

			if (!method_exists($arg, 'getDocComment')) {
				throw new ReflectorNotCommentedException();
			}

			return $arg->getDocComment();
		}

		if (class_exists($arg)) {
			$class = new ReflectionClass($arg);
			return $class->getDocComment();
		}

		return $arg;
	}

	/*
	 * The reflection element's annotations as returned by
	 * ReflectionHelper::getAnnotations().
	 */
	private $_annotations;

	/**
	 * Create a new Annotations instance.
	 *
	 * @param mixed $reflector Either a Reflector instance, the name of a class, 
	 * a doc comment or an array to use to directly populate the instance. An 
	 * value that evaluates to `false` will result in an empty Annotations 
	 * instance.
	 * @throws ReflectorNotCommentedException If the given object is a Reflector 
	 * instance but does not contain a getDocComment() method.
	 * @throws InvalidArgumentException If the given parameter is an object but 
	 * not a Reflector instance.
	 */
	public function __construct($reflector = null) {
		if (!$reflector) {
			$reflector = array();
		}

		if (is_array($reflector)) {
			$this->_annotations = $reflector;
		} else {
			$docComment = self::parseDocComment($reflector);
			$this->_annotations = AnnotationParser::getAnnotations($docComment);
		}
	}

	/**
	 * Return an annotation value as a list, even if the specified annotation
	 * contains only a scalar value or is an associative array representing
	 * a single parameterized annotation declaration.
	 *
	 * Nested annotations can be accessed by passing in multiple parameters.
	 *
	 * If the specified annotation is not set then an empty array is returned.
	 */
	public function asArray() {
		if (func_num_args() === 0) {
			return $this->_annotations;
		}

		$args = func_get_args();
		$annos =& $this->_annotations;
		while (count($args) > 0) {
			$anno = strtolower(array_shift($args));
			if (!isset($annos[$anno])) {
				$val = array();
				break;
			}

			$val = $annos[$anno];
			$annos =& $annos[$anno];
		}

		if (!is_array($val)) {
			$val = array($val);
		} else {
			// Check if array is associative
			$isAssoc = (bool) count(array_filter(array_keys($val), 'is_string'));
			if ($isAssoc) {
				$val = array($val);
			}
		}
		return $val;
	}

	/**
	 * Return a boolean indicating whether or not the specified annotation is
	 * set.  Nested values can be specified by providing multiple parameters.
	 *
	 * E.g. $annotations->hasAnnotation('base', 'sub'); is equivalent to checking
	 * if $annotations['base']['sub'] is set.
	 *
	 * Passing in no parameters will return whether or not the collection is
	 * empty.
	 *
	 * @param string...
	 */
	public function hasAnnotation() {
		if (func_num_args() == 0) {
			return count($this->_annotations) > 0;
		}

		$args = func_get_args();
		$annos =& $this->_annotations;
		while (count($args) > 0) {
			$anno = strtolower(array_shift($args));
			if (isset($annos[$anno])) {
				$annos =& $annos[$anno];
			} else {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if the collection contains an annotation with the given name.
	 */
	public function isAnnotatedWith($annotation) {
		return array_key_exists(strtolower($annotation), $this->_annotations);
	}

	/*
	 * ===========================================================================
	 * ArrayAccess implementation
	 * ===========================================================================
	 */

	/**
	 * Whether or not the requested annotation exists.
	 *
	 * @param mixed $offset
	 * @return boolean
	 */
	public function offsetExists($offset) {
		return isset($this->_annotations[strtolower($offset)]);
	}

	/**
	 * Get the information for the requested annotation.
	 *
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset) {
		return $this->_annotations[strtolower($offset)];
	}

	/**
	 * Set the given annotation to be the given value.
	 *
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value) {
		// TODO Should this be allowed?  Does allowing this cause an expectation
		// that were the same doc commenct parsed again the value would still be
		// different?
		if ($offset === null) {
			$this->_annotations[] = $value;
		} else {
			$this->_annotations[strtolower($offset)] = $value;
		}
	}

	/**
	 * Unset the annotation at the given offset.
	 *
	 * @param mixed $offset
	 */
	public function offsetUnset($offset) {
		// TODO Should this be allowed?  Does allowing this cause an expectation
		// that were the same doc commenct parsed again the value would still be
		// different?
		unset($this->_annotations[strtolower($offset)]);
	}
}
