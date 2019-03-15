<?php

namespace Borizqy\Bose;

use Borizqy\Bose\Basic\Request;
use Borizqy\Troop\Troop;

class Bose extends Controller {

	protected $plain, $private;

	public function __construct() {
		$this->plain = $this->defaultPlainPrivate();
		$this->private = $this->defaultPlainPrivate();
		$this->__esmConstruct();
	}

	public function encrypt($plain, $private) {

		// if value of plain-text or private-key is null, then return false
		if(strlen($plain) == 0 || strlen($private) == 0) return false;;

		$this->plain->value = $plain;
		$this->private->value = $private;
		$this->plain->length = strlen($plain);
		$this->private->length = strlen($private);
		
		// converting plain-text to ascii
		for($i=1; $i<=$this->plain->length; $i++) {
			$this->plain->ascii .= dechex(ord($plain[$i-1]));
		}

		// converting private-key to ascii
		for($i=1; $i<=$this->private->length; $i++) {
			$temp = dechex(ord($private[$i-1]));
			$this->private->ascii .= $temp = (strlen($temp) == 1)? "0{$temp}": $temp;
			$this->privateKeyCalculation($temp, $this->private->calculation);
		}

		// decide even and odd key value #2
		$this->evenOddMapping();

		// key's mapping #3
		for($i=1; $i<=$this->plain->length; $i++) {
			$j = $i-1;
			$k = floor(($i + ($this->evenCheck($i)? $this->process->even: $this->process->odd)) % $this->private->length);
			$this->plain->keysMapping .= ($k == 0)? $this->private->length: $k;

			$m = 1;
			for($k=$j*2; $k<$i*2; $k++) {
				$temp = hexdec($this->plain->ascii[$k]) + hexdec($this->private->ascii[$this->plain->keysMapping[$j]*2-$m]) + $this->private->calculation;
				$this->process->exchange .= (strlen($temp) == 1?'0':'') . $temp;
				$m++;
			}
		}

		// Ordering by lowest key and then the value
		// $this->process->exchangeBefore = $this->process->exchange;
		$temp_exchange = $this->process->exchange = $this->numberToAlpha($this->process->exchange);
		do {
			$this->process->order[$temp_exchange[0]] = substr_count($temp_exchange, $temp_exchange[0]);
			$temp_exchange = str_replace($temp_exchange[0], '', $temp_exchange);
		} while(strlen($temp_exchange) > 0);
		ksort($this->process->order);
		asort($this->process->order);

		// huffman process
		$this->huffmanBinary();
		
		// converting data becoming encrypted
		$this->exToChiper();

		// generating public key
		$this->process->publicSum = rand(1,61);

		foreach($this->process->order as $key => $value) {
			$this->process->order[$key] = $value + $this->process->publicSum;
		}

		$pub = json_encode($this->process->order);
		for($i=0; $i<strlen($pub); $i++) { 
			$set = ord($pub[$i]);
			$this->process->publicKey .= str_pad($set, 3, '0', STR_PAD_LEFT);
		}

		$this->process->publicKey = str_split($this->process->publicKey, $this->process->split);
		foreach($this->process->publicKey as $key => $value) {
			$this->process->publicKey[$key] = '1' . str_pad(Troop::fromDec(intval("1{$value}")), $this->process->pad, '0', STR_PAD_LEFT);
		}

		// adding public-key to an object that will be returned
		$this->encrypt->publicKey = Troop::fromDec(intval($this->process->publicSum)) . implode('', $this->process->publicKey);

		return $this;
	}

	public function decrypt($cipher, $private, $public) {
		$this->process->publicSum = Troop::toDec($public[0]);
		$this->process->order = str_split(substr($public, 1), $this->process->pad+1);
		foreach($this->process->order as $key => $value) {
			$this->process->order[$key] = substr(Troop::toDec(substr($value, 1)), 1);
		}

		$this->process->order = implode('', $this->process->order);
		
		/**
		* if period found, then return (boolean)false.
		* It means that user gave wrong public-key.
		*/
		if(strpos('0'.$this->process->order, '.') > 0) return false;

		$this->process->order = str_split($this->process->order, 3);
		foreach($this->process->order as $key => $value) {
			$this->process->order[$key] = chr($value);
		}
		$this->process->order = json_decode(implode('', $this->process->order), true);
		if(!is_array($this->process->order)) {
			return false;
		}
		foreach($this->process->order as $key => $value) {
			$this->process->order[$key] = $value - $this->process->publicSum;
		}

		// huffman process
		$this->huffmanBinary();
		
						print_r($this->process->order);
		return $this;
	}

}