<?php

namespace Borizqy\Bose;

use Borizqy\Bose\Basic\Request;
use Borizqy\Bose\Basic\EncryptStepMethods;
use Borizqy\Bose\Basic\DecryptStepMethods;

class Controller extends Request {
	use EncryptStepMethods, DecryptStepMethods;

	protected function __construct() {
		$this->__esmConstruct();
		$this->__dsmConstruct();
	}

	protected function copy() {
		return new Bose();
	}
}