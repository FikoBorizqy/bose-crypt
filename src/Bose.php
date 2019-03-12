<?php

namespace Borizqy\Bose;

use Borizqy\Bose\Basic\Request;

class Bose extends Controller {

	protected $plain, $private;

	public function __construct() {
		$this->plain = $this->defaultPlainPrivate();
		$this->private = $this->defaultPlainPrivate();
	}

	public function encrypt($plain, $private) {

		$this->plain->length = strlen($plain);
		$this->private->length = strlen($private);
		
		// converting plain-text to ascii
		for($i=1; $i<=$this->plain->length; $i++) {
			$this->plain->ascii = $this->plain->ascii . dechex(ord($plain[$i-1]));
		}

		// converting private-key to ascii
		for($i=1; $i<=$this->private->length; $i++) {
			$temp = dechex(ord($private[$i-1]));
			$temp = (strlen($temp) == 1)? "0{$temp}": $temp;
			$this->private->ascii = $this->private->ascii . $temp;
			$this->private->calculation = $this->privateKeyCalculation($temp, $this->private->calculation);
		}

		// $this->private->calculation = $calculation;

		// private text calculation
		// for($i=0; $i<$this->private->length; $i++) {
		// 	$this->private->calculation = $this->private->calculation
		// }

		return $this;
	}

}