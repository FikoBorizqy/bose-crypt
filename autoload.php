<?php

/**
 * Autoload File
 * 
 * include this file, and this file will automatically includes 
 * all required files. But, if you installed this via composer,
 * you don't have to include this file, because composer will
 * automatically includes all files that are required.
 * 
 * @package Bose Cryptography
 * @author Fiko Borizqy <fiko@dr.com>
 * @license MIT
 * @license https://choosealicense.com/licenses/mit/
 * @see https://github.com/fikoborizqy/bose-crypt
 */



/**
 * All required files that need to be included.
 */
require_once(__DIR__ . '/inc/fikoborizqy/troop/src/Troop.php');
require_once(__DIR__ . '/src/basic/EncryptStepMethods.php');
require_once(__DIR__ . '/src/basic/DecryptStepMethods.php');
require_once(__DIR__ . '/src/basic/Request.php');
require_once(__DIR__ . '/src/Controller.php');
require_once(__DIR__ . '/src/Bose.php');



/**
 * Encryption Example
 * 
 * This is the default codes to make an encryption.
 */
// $class = new Borizqy\Bose\Bose();
// $plain = "lorem ipsum dolor sit amet";
// $private = "private-key";
// $encryption = $class->encrypt($plain, $private);
// print_r($encryption);



/**
 * Decryption Example
 * 
 * This is the default codes to make an decryption of
 * plain-text: "lorem ipsum dolor sit amet" above
 */
// $class = new Borizqy\Bose\Bose();
// $plain = "100100100111100011011011001110101011011011011000000010110011011111000101011011011110011110011110100001111010111111001000001111110111101011111101101101100000110110000010011110001101000001110010011110011111001111010000111110101111000011000011111101111010110001001101100000101001101111110011000";
// $private = "private-key";
// $public = "q207510vTacoNkh10PfOqKQaF10sYmK1hYA10tE4ZMiQt10PfOs6L5j10sGLcfcEx10tE4ZMIRn10PfZhs4fj10sp9Et74v10tE4ZN8Sh10PfZk9U4A10Uw6Ag1V410tE4ZJmK910PgaggNNO10Uev2tWB910000000i9";
// $decryption = $class->decrypt($plain, $private, $public);
// print_r($decryption);