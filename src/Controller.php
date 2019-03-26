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

namespace Borizqy\Bose;

use Borizqy\Bose\Basic\Request;
use Borizqy\Bose\Basic\EncryptStepMethods;
use Borizqy\Bose\Basic\DecryptStepMethods;

/**
 * Bose Controller
 * 
 * You can't make an instance by this due to an abstract class,
 * you can make an instance by Bose. This Abstract Controller 
 * class will be required by Bose class.
 * 
 * @uses src/Bose.php
 */
abstract class Controller extends Request {

	/**
	 * @see src/basic/EncryptStepMethods.php
	 * @see src/basic/DecryptStepMethods.php
	 */
	use EncryptStepMethods, DecryptStepMethods;



	/**
	 * @var $plain		Whole plain-text data will be stored in this property.
	 */
	protected $plain;
	
	/**
	 * @var $private	Whole private-key data will be stored in this property.
	 */
	protected $private;
	
	/**
	 * @var $public		Whole public-key data will be stored in this property.
	 */
	protected $public;

	/**
	 * @var $process	Object to placed whole data while processing.
	 */
	protected $process;

	/**
	 * @var $encrypt	Whole temporary data will be stored here before being returned.
	 */
	protected $encrypt;



	/**
	* Create New Instance of Bose
	* 
	* Clear all recent objects that are stored, and creates
	* new Bose instance with default null object.
	* 
	* @return Object new Bose instance
	*/
	protected function copy() {
		/**
		* Storing new Request Instance for each properties
		* @see src/basic/Request.php
		* @see class Request()
		*/
		$this->plain = new Request();
		$this->private = new Request();
		$this->public = new Request();
		$this->process = new Request();
		$this->encrypt = new Request();
		$this->process = new Request([
			'order' => [],
			'orderCompress' => [],
			'orderBinary' => [],
			'split' => 5,
			'pad' => 4,
		]);

		/**
		 * Checking 64-bit system structure
		 */
		if(strlen(PHP_INT_MAX) > 11) {
			$this->process->split = 14;
			$this->process->pad = 9;
		}
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
	* @property Integer	$this->private->length		(Required) Total number of characters of private-key
	* @property String	$this->private->value		(Required) This will be converted to ASCII
	* @property String	$this->private->ascii		(Return) Private-key ASCII value will be stored in this property
	* @property Integer	$this->private->calculation	(Return) Private-key Substraction
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



	/**
	 * Converting Numberal to Alphabet
	 * 
	 * this method will converting numeral to alphabet or alphabet to 
	 * numeral.
	 * 
	 * @param String|Integer	$text			text or integer that will be converted
	 * @param Boolean			$fromAlpha		(default: false) "false" means this method will convert
	 * 											from numeral to alphabet.
	 * @return String|Integer					Return numeral or alphabet, the result of convertion.
	 */
	public function numberToAlpha($text, $fromAlpha = false) {
		$number = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
		$alpha  = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];
		if($fromAlpha === true) {
			$temp = $number;
			$number = $alpha;
			$alpha = $temp;
		}
		return str_replace($number, $alpha, strval($text));
	}



	/**
	 * Calculate Each Character
	 * 
	 * Calculate how many times each alphabet shows up on the plain-text.
	 * Then, it will be order by key on ascending and then value on 
	 * ascending as well.
	 * 
	 * Example:
	 * exchange: "adbcabad"
	 * order: step #1 = [
	 * 		"a" => 3,
	 * 		"d" => 2,
	 * 		"b" => 2,
	 * 		"c" => 1,
	 * ]
	 * step #2 = [
	 * 		"a" => 3,
	 * 		"b" => 2,
	 * 		"c" => 1,
	 * 		"d" => 2,
	 * ]
	 * step #3 = [
	 * 		"c" => 1,
	 * 		"b" => 2,
	 * 		"d" => 2,
	 * 		"a" => 3,
	 * ]
	 * 
	 * @property String	$this->process->exchange	(Required) alphabet that are in string
	 * @property Array 	$this->process->order		(Return) array of the result by exchange
	 */
	protected function charCategories() {
		$temp_exchange = $this->process->exchange;
		do {
			$this->process->order[$temp_exchange[0]] = substr_count($temp_exchange, $temp_exchange[0]);
			$temp_exchange = str_replace($temp_exchange[0], '', $temp_exchange);
		} while(strlen($temp_exchange) > 0);
		ksort($this->process->order);
		asort($this->process->order);
	}



	/**
	 * Processing Huffman Algorithm
	 * 
	 * Example: 
	 * order: #1 [
	 * 		"c" => 1,
	 * 		"b" => 2,
	 * 		"d" => 2,
	 * 		"a" => 3,
	 * ]
	 * order_binary: #1 [
	 * 		"c" => 11,
	 * 		"b" => 10,
	 * 		"d" => 01,
	 * 		"a" => 00,
	 * ]
	 * 
	 * @property Array 	$this->process->order 			(Requried) An array that will be calculate
	 * @property Array 	$this->process->orderBinary		(Return) Array of result
	 * @property String	$this->process->orderCompress	(Return) Order of array from the least to the most.
	 */
	protected function huffmanBinary() {
		/**
		 * Converting array to object of Request instance.
		 * 
		 * Example:
		 * from: $variable['item_1']
		 * to: $variable->item_1
		 */
		$huffman = new Request(['order' => $this->process->order]);
		
		/**
		 * Looping each item of array until it's item just left one.
		 * 
		 * Example:
		 * from: [
		 * 		"c" => 1,
		 * 		"b" => 2,
		 * 		"d" => 2,
		 * 		"a" => 3
		 * ]
		 * to: [
		 * 		"cbda" => 8
		 * ]
		 */
		while(count($huffman->order) > 1) {
			
			/**
			 * Preparing default var's value for each looping
			 */
			$huffman->key = null;
			$huffman->value = null;
			$huffman->binary = [];
			$huffman->binaryTemp = [];

			/**
			 * Slicing 2 items at beginning of $huffman->order, loop for each item.
			 */
			foreach(array_slice($huffman->order, 0, 2, TRUE) as $key => $value) {
				$huffman->key .= $key;
				$huffman->value += $value;
				unset($huffman->order[$key]);
				array_push($huffman->binary, $key);
				$huffman->i++;
			}
			
			/**
			 * Loop $huffman->binary above, and make an array with value of 
			 * huffman's binary.
			 */
			foreach($huffman->binary as $key => $val) {
				if(isset($this->process->orderBinary[$val])) {
					foreach($this->process->orderBinary[$val] as $key_old => $val_old) {
						$huffman->binaryTemp[$key_old] = $key . $val_old;
					}
					unset($this->process->orderBinary[$val]);
				} else {
					$huffman->binaryTemp[$val] = $key;
				}
			}
			$this->process->orderBinary[$huffman->key] = $huffman->binaryTemp;
			$huffman->order[$huffman->key] = $huffman->value;
			ksort($huffman->order);
			asort($huffman->order);
		}

		/**
		 * Storing to object
		 */
		$this->process->orderBinary = current($this->process->orderBinary);
		$this->process->orderCompress = $huffman->order;
	}

}