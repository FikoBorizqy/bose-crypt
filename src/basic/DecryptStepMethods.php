<?php

namespace Borizqy\Bose\Basic;

use Borizqy\Bose\Basic\Request;

trait DecryptStepMethods {

	/**
	* Converting ascii to string
	* 
	* @param	String $string This ascii will be converted to text
	* @return	String $string String format of ascii
	*/
	public function asciiToString($ascii) {
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

}