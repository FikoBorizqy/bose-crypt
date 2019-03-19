<?php

/**
 * This Abstract Controller class will be required by Bose class.
 */

namespace Borizqy\Bose;

use Borizqy\Bose\Basic\Request;
use Borizqy\Bose\Basic\EncryptStepMethods;
use Borizqy\Bose\Basic\DecryptStepMethods;

/**
 * Bose Controller
 * 
 * You can't make an instance by this due to an abstract class,
 * you can make an instance by Bose.
 */
abstract class Controller extends Request {


	
	use EncryptStepMethods, DecryptStepMethods;



	/**
	 * @var $plain		Whole plain-text data will be stored in this property.
	 * @var $private	Whole private-key data will be stored in this property.
	 * @var $public		Whole public-key data will be stored in this property.
	 * @var $process	Object to placed whole data while processing.
	 */
	protected $plain, $private, $public, $process;



	/**
	* Controller Construction
	* 
	* This will be executed when developer|user creates
	* a new Bose instance.
	*/
	protected function __cConstruct() {
		/**
		* Storing new Request Instance for each properties
		* @see src/basic/Request.php
		* @see class Request()
		*/
		$this->plain = new Request();
		$this->private = new Request();
		$this->public = new Request();
		$this->process = new Request();
		$this->process = new Request([
			'order' => [],
			'orderCompress' => [],
			'orderBinary' => [],
			'split' => 5,
			'pad' => 4,
		]);

		/**
		 * Checking is the maximum number can be count by the system
		 */
		if(strlen(intval('999999999999999999999')) > 11) {
			$this->process->split = 14;
			$this->process->pad = 9;
		}

		/**
		* Encryption Trait Construction
		* @see src/basic/EncryptStepMethods.php
		* @see EncryptStepMethods::__esmConstruct()
		*/
		$this->__esmConstruct();
	}



	/**
	* Create New Instance of Bose
	* 
	* Clear all recent objects that are stored, and creates
	* new Bose instance with default null object.
	* 
	* @return Object new Bose instance
	*/
	protected function copy() {
		return new Bose();
	}



	/**
	* Even Checking
	* 
	* Check is the number an even number or odd number
	* 
	* @param Integer	$int	Number that will be checked
	* @return Boolean			return that it is true even or not
	*/
	public function evenCheck($int) {
		return (((int) $int)%2 == 0)? true: false;
	}



	/**
	* Converting private-key to ASCII
	* 
	* @var Integer	$this->private->length		(Required) Total number of characters of private-key
	* @var String	$this->private->value		(Required) This will be converted to ASCII
	* @var String	$this->private->ascii		(Return) Private-key ASCII value will be stored in this property
	* @var Integer	$this->private->calculation	(Return) Private-key Substraction
	*/
	public function privateToAscii() {
		for($i=1; $i<=$this->private->length; $i++) {
			$temp = dechex(ord($this->private->value[$i-1]));
			$this->private->ascii .= $temp = (strlen($temp) == 1)? "0{$temp}": $temp;
			/**
			* Private-key Substraction
			* @see self::privateKeyCalculation()
			*/
			$this->privateKeyCalculation($temp, $this->private->calculation);
		}
		$this->private->addition = $this->private->addition % $this->public->randomKey;
		if($this->private->addition > $this->private->calculation) {
			$this->private->calculation = $this->private->addition - $this->private->calculation;
		} else {
			$this->private->calculation = $this->private->addition;
		}
	}



	/**
	* Private-key Substraction
	* 
	* Calculation means, each character of private-key will be
	* substracted by character behind it if the character is
	* larger than the character after it. If the character after
	* it larger than that character, then character after it will
	* be substracted will this character.
	* 
	* @param String	 $char 							2 digits Hexadecimal that will be added.
	* @param Integer $temp 							[$this->private->calculation] this is
	* 												the sum before current action.
	* @var   Integer $this->private->calculation	(Return) Result of the current sum
	* 												will be store here.
	*/
	protected function privateKeyCalculation($char, $temp) {
		$char_temp = hexdec($char[0]) + hexdec($char[1]);
		if($temp == $char_temp) {
			$this->private->calculation = $temp + $char_temp;
		} elseif($temp > $char_temp) {
			$this->private->calculation = $temp - $char_temp;
		} else {
			$this->private->calculation = $char_temp - $temp;
		}
		$this->private->addition += $char_temp;
	}



	/**
	* Even & Odd Integer
	* 
	* are integer that will be calculated for each character of plain-text.
	* @example (String) plain-text "abcd", [a,c] will be be calculated by even and [b,d] will be calculated bu odd
	* 
	* @var Integer $this->plain->length 	(Required) plain-text length
	* @var Integer $this->private->length 	(Required) private-key length
	* @var Integer $this->process->even 	(Return) This will be added for each even character in plain-text
	* @var Integer $this->process->odd		(Return) This will be added for each odd character in plain-text
	*/
	protected function evenOddMapping() {
		$sourceLength = $this->plain->length;
		if($this->private->length >= $sourceLength) {
			$this->process->even = $this->private->length % $sourceLength;
			$this->process->odd = ($this->process->even == 0)? $sourceLength: floor($sourceLength / $this->process->even);
		} else {
			$this->process->even = $sourceLength % $this->private->length;
			$this->process->odd = ($this->process->even == 0)? $this->private->length: floor($this->private->length / $this->process->even);
		}
	}



	/**
	* Plain-text key mapping
	* 
	* are integer that will be calculated for each character of plain-text.
	* @param Boolean $decrypt parameter that give signal is it encryption process or decryption process
	*/
	protected function keysMapping($decrypt = false) {
		for($i=1; $i<=$this->plain->length; $i++) {
			$j = $i-1;
			$k = floor(($i + ($this->evenCheck($i)? $this->process->even: $this->process->odd)) % $this->private->length);
			$this->plain->keysMapping .= ($k == 0)? $this->private->length: $k-1;

			if($decrypt === true) {
				$temp = $this->process->exchange;
				$x = $i*4;
				$a = $this->process->exchange[$x-4] . $this->process->exchange[$x-3];
				$b = $this->process->exchange[$x-2] . $this->process->exchange[$x-1];
				$this->plain->ascii .= dechex($a - $this->private->calculation - hexdec($this->private->ascii[$this->plain->keysMapping[$j]*2-1]));
				$this->plain->ascii .= dechex($b - $this->private->calculation - hexdec($this->private->ascii[$this->plain->keysMapping[$j]*2-2]));
			} else {
				$m = 1;
				for($k=$j*2; $k<$i*2; $k++) {
					$temp = hexdec($this->plain->ascii[$k]) + hexdec($this->private->ascii[$this->plain->keysMapping[$j]*2-$m]) + $this->private->calculation;
					$this->process->exchange .= (strlen($temp) == 1?'0':'') . $temp;
					$m++;
				}
			}
		}
	}

}