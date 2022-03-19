<?php
/**
 * =============================================================================
 * Copyright (c) 2022, Philip Graham
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

use zpt\anno\ValueLexer;

/**
 * This class provides extensions to PHP's built in reflection capabilities.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class AnnotationParser {

	const ANNOTATION_START_REGEX = '/@(\w+)\s*(.*)/';
	const ANNOTATION_END_REGEX = '/\)\s*$/';
	const ANNOTATION_LINE_REGEX = '/@(\w+)(\s*\(?\s*.*\s*\)?\s*)/';
	const ANNOTATION_MULTILINE_REGEX = '/\(\s*$/';
	const ANNOTATION_REGEX = '/@(\w+)(?:\s*(?:\(\s*)?(.*?)(?:\s*\))?)??\s*(?:\n|\*\/)/';
	const PARAMETER_REGEX = '/(\w+)\s*=\s*(\[[^\]]*\]|"[^"]*"|[^,)]*)\s*(?:,|$)/';
	const PARAMETER_NEW_REGEX = '/(\w+)\s*=\s*(\[[^\]]*\]|"[^"]*"|[^,)]*)\s*(?:,|$)/';

	/**
	 * Parse any annotations from the given doc comment and return them in an
	 * array structure.  The annotations in the returned array are indexed by
	 * their lowercased name.  Parameters with a value defined as a comma
	 * separated list contained in braces will be return as arrays.  Parameter
	 * values defined in quotes will have the quotes stripped and the inner value
	 * parsed for boolean and numeric values.  If not a boolean or numeric value,
	 * will be return as a string.	All other parameter values will be returned as
	 * either a boolean, number or string as appropriate.
	 *
	 * @param string $docComment The comment to parse.
	 * @return array Array containing the defined annotations.
	 */
	public static function getAnnotations($docComment) {
		return self::getAnnotationsTokenizer($docComment);
	}

	private static function getAnnotationsTokenizer($docComment) {
		$annotations = array();

		$tok = strtok($docComment, "\n");
		$parseState = null;

		while ($tok !== false) {

			if (preg_match(self::ANNOTATION_START_REGEX, $tok, $anno)) {
				if ($parseState !== null) {
					// Parse current state parameters
					$parseState['complete'] = true;
					$parseState['next'] = $tok;
				} else {
					$parseState = array();
					$parseState['name'] = strtolower($anno[1]);
					$parseState['params'] = $anno[2];

					// Check if annotation is defined entirely on one line
					if (preg_match(self::ANNOTATION_MULTILINE_REGEX, $tok)) {
						$parseState['complete'] = false;
					} else {
						$parseState['complete'] = true;
					}
				}
			} else if ($parseState !== null) {
				$parseState['params'] .= preg_replace('/^\s*\*\s*/', '', $tok);
			}

			if (
				$parseState !== null &&
				$parseState['complete'] === false &&
				preg_match(self::ANNOTATION_END_REGEX, $tok, $anno)
			) {
				$parseState['complete'] = true;
			}

			if ($parseState !== null && $parseState['complete'] === true) {
				$paramSrc = preg_replace('/^\s*\(\s*/', '', $parseState['params']);
				$paramSrc = preg_replace('/\s*(?:\)\s*)?(:?\*\/)?$/', '', $paramSrc);

				/* fwrite(STDOUT, "Checking " . $paramSrc . " for params\n"); */
				/* $val = self::_parseAnnotationValue($paramSrc); */
				/* fwrite(STDOUT, print_r($val, true)); */

				$hasParams = preg_match_all(
					self::PARAMETER_NEW_REGEX,
					$paramSrc,
					$params,
					PREG_SET_ORDER
				);

				if ($hasParams) {
					$val = array();
					foreach ($params AS $param) {
						$val[$param[1]] = self::_parseValue($param[2]);
					}
				} else {
					$val = trim($paramSrc);
					if ($val == '') {
						$val = true;
					} else {
						$val = self::_parseValue($val);
					}
				}

				$annoName = $parseState['name'];
				if (isset($annotations[$annoName])) {
					if (!is_array($annotations[$annoName])) {
						$annotations[$annoName] = array($annotations[$annoName]);
					}
					$annotations[$annoName][] = $val;
				} else {
					$annotations[$annoName] = $val;
				}

				$parseState = null;
			}

			if ($parseState !== null && isset($parseState['next'])) {
				$tok = $parseState['next'];
				$parseState = null;
			} else {
				$tok = strtok("\n");
			}
		}

		return $annotations;
	}

	private static function _parseAnnotationValue($value) {
		$lexer = new ValueLexer($value);

		$t = $lexer->next();
		while ($t !== false) {
			fwrite(STDOUT, print_r($t, true));
		}
		return $value;
	}

	private static function _parseValue($value) {
		$val = trim($value);

		if (substr($val, 0, 1) == '[' && substr($val, -1) == ']') {
			// Array values
			$vals = explode(',', substr($val, 1, -1));
			$val = array();
			foreach ($vals as $v) {
				$val[] = self::_parseValue($v);
			}
			return $val;

		} else if (substr($val, 0, 1) == '{' && substr($val, -1) == '}') {
			// If is json object that start with { } decode them
			return json_decode($val);
		} else if (substr($val, 0, 1) == '"' && substr($val, -1) == '"') {
			// Quoted value, remove the quotes then recursively parse and return
			$val = substr($val, 1, -1);
			return self::_parseValue($val);

		} else if (strtolower($val) == 'true') {
			// Boolean value = true
			return true;

		} else if (strtolower($val) == 'false') {
			// Boolean value = false
			return false;

		} else if (is_numeric($val)) {
			// Numeric value, determine if int or float and then cast
			if ((float) $val == (int) $val) {
				return (int) $val;
			} else {
				return (float) $val;
			}

		} else {
			// Nothing special, just return as a string
			return $val;
		}
	}
}
