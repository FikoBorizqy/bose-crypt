<?php

namespace Borizqy\Bose;

class Controller {

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