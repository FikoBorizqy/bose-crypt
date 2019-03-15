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
			'split' => 8,
			'pad' => 6,
		]);
		if(strlen(intval('999999999999999999999')) > 11) {
			$this->process->split = 17;
			$this->process->pad = 11;
		}
	}

	protected function defaultPlainPrivate() {
		return new Request(['ascii' => null]);
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
		if($this->private->length >= $this->plain->length) {
			$this->process->even = $this->private->length % $this->plain->length;
			$this->process->odd = ($this->process->even == 0)? $this->plain->length: floor($this->plain->length / $this->process->even);
			$this->process->private = true; // this variable indicates that private-key is bigger or equals to plain-text
		} else {
			$this->process->even = $this->plain->length % $this->private->length;
			$this->process->odd = ($this->process->even == 0)? $this->private->length: floor($this->private->length / $this->process->even);
			$this->process->private = false;
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
}