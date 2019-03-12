<?php

namespace Borizqy\Bose;

class Bose extends Controller {

	public $asciiPlain = 'aku';
	public $asciiPrivate = 'kau';

	public function encrypt($plain, $private) {
		
		// converting plain-text to ascii
		$ascii = NULL;
		for($i=0; $i<strlen($plain); $i++) {
			$ascii = $ascii . dechex(ord($plain[$i]));
		}

		// converting private-key to ascii
		$asciiPrivate = NULL;
		for($i=0; $i<strlen($private); $i++) {
			$asciiPrivate = $asciiPrivate . dechex(ord($private[$i]));
		}

		return $ascii . ' - ' . $asciiPrivate;
	}

}