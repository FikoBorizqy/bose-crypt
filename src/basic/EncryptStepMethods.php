<?php

namespace Borizqy\Bose\Basic;

use Borizqy\Bose\Basic\Request;

trait EncryptStepMethods {
	protected function defaultPlainPrivate() {
		return new Request(['ascii' => null]);
	}

	public function privateKeyCalculation($char, $temp) {
		$char_temp = hexdec($char[0]) + hexdec($char[1]);
		if($temp == $char_temp) {
			return $temp + $char_temp;
		} elseif($temp > $char_temp) {
			return $temp - $char_temp;
		} else {
			return $char_temp - $temp;
		}
	}
}