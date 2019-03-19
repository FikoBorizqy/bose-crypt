<?php

/**
 * It will be used by Controller
 */

namespace Borizqy\Bose\Basic;

use Borizqy\Bose\Basic\Request;

/**
 * Encryption Class
 * 
 * This class contains methods that are used for encrypting data or
 * plain-text. This class used by Controller class.
 * 
 * @see Borizqy\Bose\Controller()
 */
trait EncryptStepMethods {

	/**
	 * @var $encrypt	Whole temporary data will be stored here before being
	 * 					returned.
	 */
	protected $encrypt;



	/**
	 * @var $encrypt	Whole temporary data will be stored here before being
	 * 					returned.
	 */
	protected function __esmConstruct() {
		$this->encrypt = new Request();
	}

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

	protected function exToChiper() {
		$this->encrypt->cipher = $this->process->exchange;
		foreach($this->process->orderBinary as $key => $value) {
			$this->encrypt->cipher = str_replace($key, $value, $this->encrypt->cipher);
		}
	}

	protected function huffmanBinary() {
		$huffman = new Request(['order' => $this->process->order]);
		while(count($huffman->order) > 1) {
			// print_r($huffman->order);
			$huffman->key = null;
			$huffman->value = null;
			$huffman->binary = [];
			foreach(array_slice($huffman->order, 0, 2, TRUE) as $key => $value) {
				$huffman->key .= $key;
				$huffman->value += $value;
				unset($huffman->order[$key]);
				array_push($huffman->binary, $key);
				$huffman->i++;
			}
			
			$huffman->binaryTemp = [];
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

		// print_r($huffman->order);
		$this->process->orderBinary = current($this->process->orderBinary);
		$this->process->orderCompress = $huffman->order;
	}

	/**
	* Converting String to ASCII
	* @example "me" becomes "6b65"
	* 
	* @param String $string This text will be converted to ascii
	* @return String $string ascii character of string
	*/
	public function stringToAscii($string) {
		$return = null;
		for($i=1; $i<=strlen($string); $i++) {
			$temp = ord($string[$i-1]);
			$this->process->minAscii = ($temp<$this->process->minAscii || is_null($this->process->minAscii))? $temp: $this->process->minAscii;
			$this->process->maxAscii = ($temp>$this->process->maxAscii)? $temp: $this->process->maxAscii;
			$return .= dechex($temp);
		}
		if($this->process->maxAscii - $this->process->minAscii <= 3) {
			$this->process->minAscii = 3 - ($this->process->maxAscii - $this->process->minAscii);
			$this->process->minAscii = $this->process->minAscii - $this->process->minAscii;
			$this->process->maxAscii = 3 - ($this->process->maxAscii - $this->process->minAscii);
			$this->process->maxAscii = $this->process->maxAscii + $this->process->maxAscii;
		}
		$this->process->minAscii = str_pad(dechex($this->process->minAscii), 2, '0', STR_PAD_LEFT);
		$this->process->maxAscii = str_pad(dechex($this->process->maxAscii), 2, '0', STR_PAD_LEFT);
		return $return;
	}

	protected function charCategories() {
		$temp_exchange = $this->process->exchange;
		do {
			$this->process->order[$temp_exchange[0]] = substr_count($temp_exchange, $temp_exchange[0]);
			$temp_exchange = str_replace($temp_exchange[0], '', $temp_exchange);
		} while(strlen($temp_exchange) > 0);
		ksort($this->process->order);
		asort($this->process->order);
	}
}