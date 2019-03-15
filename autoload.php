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

$text = 'aku';
$private = 'aku';
// $bose = $class->encrypt($text, $private);

$text = '100010001101101110111101';
$public = '4108ilKvlk3NP10andwXvO3qc10bnwrBxa1vA107NFuK4p0IJ10000001dLdP';
$bose = $class->decrypt($text, $private, $public);

// $bose = base64_encode($a);
print_r($bose);

// echo sha1('welcome');
echo "\n\n" . (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"] . "\n");
exit;