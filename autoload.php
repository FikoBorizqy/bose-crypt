<?php

require_once(__DIR__ . '/inc/fikoborizqy/troop/src/Troop.php');
require_once(__DIR__ . '/src/basic/EncryptStepMethods.php');
require_once(__DIR__ . '/src/basic/DecryptStepMethods.php');
require_once(__DIR__ . '/src/basic/Request.php');
require_once(__DIR__ . '/src/Controller.php');
require_once(__DIR__ . '/src/Bose.php');

use Borizqy\Bose\Bose;
use Borizqy\Troop\Troop;

// $a = [
// 	'a' => '00',
// 	'c' => '01',
// 	'p' => '11',
// 	'r' => '10',
// 	'e' => '010',
// 	'd' => '011',
// 	'z' => '0101011',
// 	'j' => '1001',
// 	'b' => '1011',
// 	'y' => '010100',
// ];

// $b = array_flip($a);

// ksort($b);
// array_shift($b);

// print_r($b);
// exit;

// print_r((new Bose())->encrypt('merdeka', 'kuncinya'));
$class = new Bose();

$text = 'bestafiko bo';
$private = 'borizqy';


// print_r($class->copy()->encrypt($text, $private));

$text = '111110011000110101001111010011111110001100100101100011010100000110101011111000111100101111000010101111010011100111100';
print_r($class->copy()->decrypt($text, $private, 'b10vTacnXit10PfDsC44t10sYmK1hIm10tE4ZMIRn10PfDsC44s10TWTuHPYw10tE4ZJML310PfDtXYZ710u6MV7E2E10tE4ZLSPz10PfDvjTTL10sp9Et6Ok10tE4ZKcLX1000jHCmZT'));

echo "\n" . (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"] . "\n");
exit;