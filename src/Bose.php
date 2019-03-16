<?php

namespace Borizqy\Bose;

use Borizqy\Bose\Basic\Request;
use Borizqy\Troop\Troop;

class Bose extends Controller {

	protected $plain, $private, $public, $return;

	public function __construct() {
		$this->plain = $this->defaultPlainPrivate();
		$this->private = $this->defaultPlainPrivate();
		$this->public = $this->defaultPlainPrivate();
		$this->return = $this->defaultPlainPrivate();
		$this->__cConstruct();
	}

	public function encrypt($plain, $private) {

		// if value of plain-text or private-key is null, then return false
		if(strlen($plain) == 0 || strlen($private) == 0) return false;

		/**
		* preparing data
		* 
		* store all parameters to object,
		*/
		$this->plain->value = $plain;
		$this->private->value = $private;
		$this->plain->length = strlen($plain);
		$this->private->length = strlen($private);
		
		// converting plain-text to ascii
		$this->plain->ascii = $this->stringToAscii($this->plain->value);

		// converting private-key to ascii
		$this->privateToAscii();		

		// decide even and odd key value
		$this->evenOddMapping();

		// key's mapping & calculate the value of exchange
		$this->keysMapping();

		// Ordering by lowest key and then the value
		$this->process->exchange = $this->numberToAlpha($this->process->exchange);

		// make an order of exchange, categorize how many times character shows up
		$this->CharCategories();

		// huffman process
		$this->huffmanBinary();
		
		// converting data becoming encrypted
		$this->exToChiper();

		// generating public key
		$this->public->randomKey = rand(1,61);

		foreach($this->process->order as $key => $value) {
			$this->process->order[$key] = $value + $this->public->randomKey;
		}

		$this->public->json = json_encode($this->process->order);
		for($i=0; $i<strlen($this->public->json); $i++) { 
			$x = str_pad(ord($this->public->json[$i]), 3, '0', STR_PAD_LEFT);
			$this->public->jsonAscii .= $x;
		}

		$this->public->jsonAscii = str_split($this->public->jsonAscii, $this->process->split);
		foreach($this->public->jsonAscii as $key => $value) {
			$this->public->jsonAscii[$key] = '1' . str_pad(Troop::fromDec(intval("1{$value}")), $this->process->pad, '0', STR_PAD_LEFT);
		}

		// adding public-key to an object that will be returned
		$this->encrypt->public_key = Troop::fromDec(intval($this->public->randomKey)) . implode('', $this->public->jsonAscii);

		return new Request([
			'cipher_text' => $this->encrypt->cipher,
			'public_key' => $this->encrypt->public_key,
		]);
	}

	public function decrypt($cipher, $private, $public) {

		// storing paramaters to object
		$this->encrypt->cipher = $cipher;
		$this->encrypt->length = strlen($cipher);
		$this->private->value = $private;
		$this->private->length = strlen($private);
		$this->public->randomKey = Troop::toDec($public[0]);
		$this->encrypt->public_key = substr($public, 1);

		// split-up public key
		$this->process->order = str_split($this->encrypt->public_key, $this->process->pad+1);

		// convert jsonAscii to decimal from Troop
		foreach($this->process->order as $key => $value) {
			$this->process->order[$key] = substr(Troop::toDec(substr($value, 1)), 1);
		}

		// imploding public-key's
		$this->process->order = implode('', $this->process->order);

		$this->process->order = str_split($this->process->order, 3);
		foreach($this->process->order as $key => $value) {
			$this->process->order[$key] = chr($value);
		}
		$this->process->order = implode('', $this->process->order);
		// for($i=0; $i<count($this->process->order); $i++) { 
		// 	$x = str_pad(ord($this->public->json[$i]), 3, '0', STR_PAD_LEFT);
		// 	$this->process->order .= $x;
		// }
		$this->process->order = json_decode($this->process->order, true);
		
		// if public key incorrect, then return false
		if(!is_array($this->process->order)) return false;

		foreach($this->process->order as $key => $value) {
			$this->process->order[$key] = $value - $this->public->randomKey;
		}

		$this->huffmanBinary();

		$temp = array_flip($this->process->orderBinary);
		for($i=0; $i<strlen($this->encrypt->cipher); $i++) { 
			$this->process->temp_key .= $this->encrypt->cipher[$i];
			if(isset($temp[$this->process->temp_key])) {
				$this->process->exchange .= $temp[$this->process->temp_key];
				unset($this->process->temp_key);
			}
		}

		// convert from alphabet to numberical
		$this->process->exchange = $this->numberToAlpha($this->process->exchange, true);

		// getting plain text length
		$this->plain->length = floor(strlen($this->process->exchange)/4);

		// converting private-key to ascii
		$this->privateToAscii();

		// decide even and odd key value
		$this->evenOddMapping();

		// key's mapping & calculate the value of exchange
		$this->keysMapping(true);

		// converting ascii to plain-text
		$this->plain->value = $this->asciiToString($this->plain->ascii);

		return $this->plain->value;
	}

}