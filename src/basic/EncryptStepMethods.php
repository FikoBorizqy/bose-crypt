<?php

namespace Borizqy\Bose\Basic;

use Borizqy\Bose\Basic\Request;

trait EncryptStepMethods {

	protected $process;

	protected function __esmConstruct() {
		$this->process = new Request();
		$this->encrypt = new Request();
		$this->process = new Request([
			'order' => [],
			'orderCompress' => [],
			'orderBinary' => [],
			'split' => 5,
			'pad' => 4,
		]);
		if(strlen(intval('999999999999999999999')) > 11) {
			$this->process->split = 14;
			$this->process->pad = 9;
		}
	}

	protected function defaultPlainPrivate() {
		return new Request();
	}

	protected function privateKeyCalculation($char, $temp) {
		$char_temp = hexdec($char[0]) + hexdec($char[1]);
		if($temp == $char_temp) {
			$this->private->calculation = $temp + $char_temp;
		} elseif($temp > $char_temp) {
			$this->private->calculation = $temp - $char_temp;
		} else {
			$this->private->calculation = $char_temp - $temp;
		}
	}

	protected function evenOddMapping() {
		$sourceLength = $this->plain->length;
		if($this->private->length >= $sourceLength) {
			$this->process->even = $this->private->length % $sourceLength;
			$this->process->odd = ($this->process->even == 0)? $sourceLength: floor($sourceLength / $this->process->even);
		} else {
			$this->process->even = $sourceLength % $this->private->length;
			$this->process->odd = ($this->process->even == 0)? $this->private->length: floor($this->private->length / $this->process->even);
		}
		// $this->process->even = 
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

	public function evenCheck($int) {
		return (((int) $int)%2 == 0)? true: false;
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
	* Converting string to ascii
	* 
	* @param	String $string This text will be converted to ascii
	* @return	String $string ascii character of string
	*/
	public function stringToAscii($string) {
		$return = null;
		for($i=1; $i<=strlen($string); $i++) {
			$return .= dechex(ord($string[$i-1]));
		}
		return $return;
	}

	/**
	* Converting ascii to string
	* 
	* @param	String $string This ascii will be converted to text
	* @return	String $string String format of ascii
	*/
	public function asciiToString($ascii) {
		$return = null;
		for($i=1; $i<=strlen($ascii)/2; $i++) {
			$a = $ascii[$i*2-2];
			$b = $ascii[$i*2-1];
			$return .= chr(hexdec($a . $b));
		}
		return $return;
	}

	/**
	* converting private become ascii, and also getting private-key calculation of all
	*/
	public function privateToAscii() {
		for($i=1; $i<=$this->private->length; $i++) {
			$temp = dechex(ord($this->private->value[$i-1]));
			$this->private->ascii .= $temp = (strlen($temp) == 1)? "0{$temp}": $temp;
			$this->privateKeyCalculation($temp, $this->private->calculation);
		}
	}

	protected function keysMapping($decrypt = false) {
		for($i=1; $i<=$this->plain->length; $i++) {
			$j = $i-1;
			$k = floor(($i + ($this->evenCheck($i)? $this->process->even: $this->process->odd)) % $this->private->length);
			$this->plain->keysMapping .= ($k == 0)? $this->private->length: $k;

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