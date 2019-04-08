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
use Borizqy\Bose\Basic\Troop;

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
	* Encryption Process
	* 
	* Method to converts data or plain-text to be cipher-text,
	* cipher-text will be on binary number.
	* 
	* @param String $plain		Text that will be converted to cipher-text
	* @param String $private	String to encrypt and also to decrypt to be plain-text
	* @param String $minChar	(Default: null) Decide what is the lowest character, if
	* 							you don't decide it, system will randomly decide it.
	* @param String $maxChar	(Default: null) Decide what is the highest character, if
	* 							you don't decide it, system will randomly decide it.
	* @param String $private	String to encrypt and also to decrypt to be plain-text
	* @return Request			This object will return the plain-text, cipher-text, 
	* 							private-key, public-key. whereas public-key used to 
	* 							decrypt the cipher-text as well as the private-key.
	*/
	public function encrypt($plain, $private, $minChar = null, $maxChar = null) {

		/**
		* Checking Parameters Existence - if value of plain-text or private-key is null,
		* then method returns (boolean)false directly.
		*/
		if(strlen($plain) == 0 || strlen($private) == 0) return false;

		/**
		* Reset all object
		*/
		$this->copy();

		/**
		* Preparing Data - store all parameters to the objects.
		*/
		$this->plain->value = $plain;
		$this->private->value = $private;
		$this->plain->length = strlen($plain);
		$this->private->length = strlen($private);
		$this->process->minAscii = (is_null($minChar) || is_bool($minChar))? null:ord($minChar);
		$this->process->maxAscii = (is_null($maxChar) || is_bool($maxChar))? null:ord($maxChar);

		/**
		* Generate Random Key - this random key in integer will be used to encrypt data.
		* Random key format is in integer between 1 to 61.
		*/
		$this->public->randomKey = rand(1,61);

		/**
		* Converting plain-text to ASCII.
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
		* Map each plain-text's character key from private-key
		* @see src/Controller.php
		* @see Controlller::keysMapping()
		*/
		$this->keysMapping();

		/**
		 * Converting Numberal to Alphabet
		 * @see src/Controller.php
		 * @see Controller::numberToAlpha()
		 */
		$this->process->exchange = $this->numberToAlpha($this->process->exchange);

		/**
		 * Calculate each character
		 * @see src/Controller.php
		 * @see Controller::CharCategories()
		 */
		$this->CharCategories();

		/**
		 * Processing Huffman algorithm
		 * @see src/Controller.php
		 * @see Controller::huffmanBinary()
		 */
		$this->huffmanBinary();
		
		/**
		 * Converting Data to Huffman Binary
		 * @see src/basic/EncryptStepMethods.php
		 * @see EncryptStepMethods::exToCipher()
		 */
		$this->exToCipher();

		/**
		 * Processing Public-key
		 * 
		 * This is how public-key generated base on randomKey, plain-text, and
		 * and private-key as well.
		 */		
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

		$this->encrypt->public_key = Troop::fromDec(intval($this->public->randomKey)) . $this->process->minAscii . $this->process->maxAscii . implode('', $this->public->jsonAscii);

		/**
		 * Return of encryption
		 */
		return new Request([
			'plain_text' => $plain,
			'cipher_text' => $this->encrypt->cipher,
			'public_key' => $this->encrypt->public_key,
			'private_key' => $private,
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
	* @return Request			Object of decryption
	*/
	public function decrypt($cipher, $private, $public) {

		/**
		* Reset all object
		*/
		$this->copy();

		/**
		* Preparing Data - store all parameters to the objects.
		*/
		$this->encrypt->cipher = $cipher;
		$this->encrypt->length = strlen($cipher);
		$this->private->value = $private;
		$this->private->length = strlen($private);
		$this->public->randomKey = Troop::toDec($public[0]);
		$this->process->minAscii = hexdec(substr($public, 1, 2));
		$this->process->maxAscii = hexdec(substr($public, 3, 2));
		$this->encrypt->public_key = substr($public, 5);

		/**
		* Split up public-key by $this->process->pad total character
		*/
		$this->process->order = str_split($this->encrypt->public_key, $this->process->pad+1);

		/**
		 * Processing Public-Key
		 * @see src/basic/DecryptStepMethod.php
		 * @see DecryptStepMethod::processPublicKey()
		 */
		$this->processPublicKey();

		/**
		 * If public-key is incorrect, then return false.
		 */
		if(!is_array($this->process->order)) return false;

		/**
		 * Minus all order array item's value by random key
		 * that are generated when encrypting before.
		 */
		foreach($this->process->order as $key => $value) {
			$this->process->order[$key] = $value - $this->public->randomKey;
		}

		/**
		 * Processing Huffman algorithm
		 * @see src/Controller.php
		 * @see Controller::huffmanBinary()
		 */
		$this->huffmanBinary();
		
		/**
		 * Processing Huffman algorithm
		 * @see src/Controller.php
		 * @see Controller::huffmanBinary()
		 */
		$this->cipherToEx();

		/**
		 * Converting Alphabet to Numeral
		 * @see src/Controller.php
		 * @see Controller::numberToAlpha()
		 */
		$this->process->exchange = $this->numberToAlpha($this->process->exchange, true);

		/**
		 * Getting plain-text's length
		 */
		$this->plain->length = floor(strlen($this->process->exchange)/4);

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
		* Map each plain-text's character key from private-key
		* @see src/Controller.php
		* @see Controlller::keysMapping()
		*/
		$this->keysMapping(true);

		/**
		* Converting ASCII to plain-text.
		* @see src/basic/DecryptStepMethods.php
		* @see DecryptStepMethods::asciiToString()
		*/
		$this->plain->value = $this->asciiToString($this->plain->ascii);

		$return = new Request([
			'plain_text' => $this->plain->value,
			'cipher_text' => $cipher,
			'public_key' => $public,
			'private_key' => $private,
		]);

		/**
		 * Returning decryption's method
		 */
		/**
		 * Return of encryption
		 */
		return $return;
	}

}