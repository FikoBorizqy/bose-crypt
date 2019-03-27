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

use Borizqy\Bose\Basic\Request;
use Borizqy\Bose\Basic\Troop;

/**
 * Decryption Trait
 * 
 * All methods that are required for decrypting data will be avaiable 
 * here, this document will be used by Controller.
 * 
 * @access protected
 * @see src/Controller.php
 */
trait DecryptStepMethods {

	/**
	 * Processing Public-Key
	 * 
	 * Processing public-key, this function is the opposite of public-key 
	 * process of encryption. converting Troop to decimal.
	 * 
	 * Example:
	 * from: [
	 * 		"a" => "j9S902Hk"
	 * ]
	 * to: [
	 * 		"a" => 3
	 * ]
	 * 
	 * @property Array $this->process->order 	(Required|Return) public-key after splited
	 * 											up. Once completed, then order will return 
	 * 											array that return each plain-text character 
	 * 											value.
	 */
	protected function processPublicKey() {
		foreach($this->process->order as $key => $value) {
			$this->process->order[$key] = substr(Troop::toDec(substr($value, 1)), 1);
		}

		$this->process->order = implode('', $this->process->order);
		$this->process->order = str_split($this->process->order, 3);
		foreach($this->process->order as $key => $value) {
			$this->process->order[$key] = chr($value);
		}
		$this->process->order = implode('', $this->process->order);
		$this->process->order = json_decode($this->process->order, true);
	}



	/**
	* Converting ascii to string
	* 
	* @param	String $ascii	This ascii will be converted to text
	* @return	String 			String format of ascii
	*/
	protected function asciiToString($ascii) {
		$return = null;
		for($i=1; $i<=strlen($ascii)/2; $i++) {
			$temp = hexdec($ascii[$i*2-2] . $ascii[$i*2-1]);
			while($temp < $this->process->minAscii || $temp > $this->process->maxAscii) {
				$temp = ($temp + $this->process->minAscii) % $this->process->maxAscii;
			}
			$return .= chr($temp);
		}
		// print_r($return);exit;
		return $return;
	}



	/**
	 * Convert Huffman's Binary
	 * 
	 * Convert back from huffman's binary to character before huffman 
	 * when encrypting data or plain-text.
	 * 
	 * @property Array	$this->process->orderBinary	(Required) Array of complete huffman 
	 * 												binary chacacter's mapping.
	 * @property String	$this->encrypt->cipher		(Required) Cipher-text will be calculated
	 * 												for the length.
	 * @property String	$this->process->exchange	(Return) Result of convertion will be store 
	 * 												in this variable.
	 */
	protected function cipherToEx() {
		$temp = array_flip($this->process->orderBinary);
		for($i=0; $i<strlen($this->encrypt->cipher); $i++) { 
			$this->process->temp_key .= $this->encrypt->cipher[$i];
			if(isset($temp[$this->process->temp_key])) {
				$this->process->exchange .= $temp[$this->process->temp_key];
				unset($this->process->temp_key);
			}
		}
	}

}