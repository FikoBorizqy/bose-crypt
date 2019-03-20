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

/**
 * Encryption Trait
 * 
 * This class contains methods that are used for encrypting data or
 * plain-text. This class used by Controller class.
 * 
 * @access protected
 * @see src/Controller.php
 */
trait EncryptStepMethods {

	/**
	 * Exchange to Cipher
	 * 
	 * Converting string to Huffman's Binary, this huffman's binary will 
	 * be return as the cipher-text.
	 * 
	 * Example: "adbcabad" becomes "0001101100100001"
	 * 
	 * @property String $this->process->exchange	(Requried) String before converted to Huffman's Binary
	 * @property String $this->encrypt->cipher 		(Return) Huffman's Binary of exchange
	 */
	protected function exToCipher() {
		$this->encrypt->cipher = $this->process->exchange;
		foreach($this->process->orderBinary as $key => $value) {
			$this->encrypt->cipher = str_replace($key, $value, $this->encrypt->cipher);
		}
	}

	/**
	* Converting String to ASCII
	* 
	* Once converting string to ASCII completed, then this method will
	* collect lowest number and the highest number base on ASCII.
	* 
	* Example: "me" becomes "6b65"
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
			$temp_diff = $this->process->maxAscii - $this->process->minAscii;
			$this->process->minAscii = $this->process->minAscii - (3-$temp_diff);
			$this->process->maxAscii = $this->process->maxAscii + (3-$temp_diff);
		}
		$this->process->minAscii = str_pad(dechex($this->process->minAscii), 2, '0', STR_PAD_LEFT);
		$this->process->maxAscii = str_pad(dechex($this->process->maxAscii), 2, '0', STR_PAD_LEFT);
		return $return;
	}
}