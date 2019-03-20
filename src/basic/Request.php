<?php

/**
 * Bose-Cryptography
 * 
 * Cryptography that will cencrypt data to be binary codes
 * with the decided private-key and decrypt binary codes
 * to be data as developer/user that encrypted before
 * by private-key and public-key(that will be generated once
 * doing encryption).
 * 
 * @package Bose Cryptography
 * @author Fiko Borizqy <fiko@dr.com>
 * @license MIT
 * @license https://choosealicense.com/licenses/mit/
 * @see https://github.com/fikoborizqy/bose-crypt
 */

namespace Borizqy\Bose\Basic;

/**
 * Request Instance
 * 
 * Base object, so when getting undefined property or undefined method,
 * is will automatically return null value.
 * 
 * @access public
 */
class Request {

	/**
	 * Request constructor - Decide variables that will be stored on this 
	 * object.
	 * 
	 * @param Array $array 	Variables in an array that will be stored on object
	 */
	public function __construct(Array $array = []) {
		foreach($array as $key => $val) {
			$this->$key = $val;
		}
	}

	/**
	 * Controls if calling undefined method. If undefined mehod called, then
	 * it will return null.
	 * 
	 * @param String 	$method 	Method name that will be check and called
	 * @param Arrays 	$params 	All parameters on a method
	 */
	public function __call($method, $params = []) {
		if(method_exists($this, $method)) {
			return call_user_func_array([$this, $method], $params);
		} else {
			return NULL;
		}
	}

	/**
	 * Controls if calling undefined property. If undefined
	 * mehod called, then it will return null.
	 * 
	 * @param String 	$var 	variable / property name
	 */
	public function __get($var) {
		if(isset($this->$var)) {
			return $this->$var;
		} else {
			return NULL;
		}
	}

}