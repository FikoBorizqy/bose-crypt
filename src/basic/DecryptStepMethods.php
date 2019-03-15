<?php

namespace Borizqy\Bose\Basic;

trait DecryptStepMethods {

	protected $decrypt;

	protected function __dsmConstruct() {
		$this->decrypt = new Request();
	}

}