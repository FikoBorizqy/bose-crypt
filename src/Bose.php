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
* @author Fiko Borizqy <fikokuper@gmail.com>
* @license MIT
* @license https://choosealicense.com/licenses/mit/
* @see https://github.com/fikoborizqy/bose-crypt
*/

namespace Borizqy\Bose;

use Borizqy\Bose\Basic\Request;
use Borizqy\Troop\Troop;

/**
* Bose Basic Class
* 
* To execute an encryption or decryption, make an instance by this
* class. You dont need to use another class or include another
* class. Once you create an instance by this class, whole class that
* are required by this class will be included automatically.
* 
* @access public
*/

class Bose extends Controller {

	/**
	* Construction
	* 
	* This method will be called when user make a new
	* instance of Bose.
	*/
	public function __construct() {
		/**
		* Controller Construction
		* @see src/Controller.php
		* @see Controller::__cConstruct()	Controller construction
		*/
		$this->__cConstruct();
	}



	/**
	* Encryption Process
	* 
	* Method to converts data or plain-text to be cipher-text,
	* cipher-text will be on binary number.
	* 
	* @param String $plain		Text that will be converted to cipher-text
	* @param String $private	String to encrypt and also to decrypt to be plain-text
	* @return Array				This array will return the cipher-text and public-key, 
	* 							whereas public-key used to decrypt the cipher-text as 
	* 							well as the private-key.
	*/
	public function encrypt($plain, $private) {

		/**
		* Checking Parameters Existence
		* 
		* if value of plain-text or private-key is null,
		* then method returns (boolean)false directly.
		*/
		if(strlen($plain) == 0 || strlen($private) == 0) return false;

		/**
		* Preparing Data
		* 
		* store all parameters to the objects.
		*/
		$this->plain->value = $plain;
		$this->private->value = $private;
		$this->plain->length = strlen($plain);
		$this->private->length = strlen($private);

		/**
		* Converting string or text to ASCII.
		* 
		* @see src/Controller.php
		* @see Controlller::stringToAscii()
		*/
		$this->plain->ascii = $this->stringToAscii($this->plain->value);

		/**
		* Converting private-key to ASCII
		* @see src/Controller.php
		* @see Controlller::privateToAscii()
		*/
		$this->privateToAscii();		

		/**
		* Getting Even & Odd Integer that will be added for each plain-text
		* @see src/Controller.php
		* @see Controlller::evenOddMapping()
		*/
		$this->evenOddMapping();

		/**
		* Plain-text key mapping 
		* @see src/Controller.php
		* @see Controlller::keysMapping()
		*/
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
		$this->encrypt->public_key = Troop::fromDec(intval($this->public->randomKey)) . $this->process->minAscii . $this->process->maxAscii . implode('', $this->public->jsonAscii);

		return new Request([
			'cipher_text' => $this->encrypt->cipher,
			'public_key' => $this->encrypt->public_key,
		]);
	}



	/**
	* Decryption Process
	* 
	* Method to converts back the data or plain-text from the cipher-text,
	* cipher-text will be converted back to plain-text.
	* 
	* @param String $cipher		Text that will be converted beck to plain-text
	* @param String $private	String to encrypt and also to decrypt to be plain-text
	* @param String $public		String that needed to decrypt data from cipher-text,
	* 							that are generated when encrypting data.
	* @return String			Plain-text of the cipher text,
	*/
	public function decrypt($cipher, $private, $public) {

		// storing paramaters to object
		$this->encrypt->cipher = $cipher;
		$this->encrypt->length = strlen($cipher);
		$this->private->value = $private;
		$this->private->length = strlen($private);
		$this->public->randomKey = Troop::toDec($public[0]);
		$this->process->minAscii = hexdec(substr($public, 1, 2));
		$this->process->maxAscii = hexdec(substr($public, 3, 2));
		$this->encrypt->public_key = substr($public, 5);

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