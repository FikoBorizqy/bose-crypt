# BOSE Cryptography
Encrypt and Decrypt string by giving private-key before doing encryption, and insert public-key &amp; private-key for decrypting.

## Installation
You can install this directly from github, or you can install it via composer. But, we recommend you to install this library via composer.

1. Install composer  
If you don't know how to install composer, you can read this: [Install Composer](https://getcomposer.org/download/)
2. Make your project directory / go to your project directory.
3. Install by composer  
Open your terminal, go to your project directory, and paste this code:
```
composer require fikoborizqy/bose-crypt
```
4. Include the composer `autoload.php`  
Open your php project file, and put this on top of your code.
```
require_once('vendor/autoload.php');
```

## How to use?
When you encrypting data, you need to decide private-key for the plain-text. Let's crack on:

1. use Bose Class by the namespace  
copy this before you encrypting data! You can change this `$class` variable as you want.
```
$class = new \Borizqy\Bose\Bose();
```
2. Encrypting data  
To encrypt data, you need to decide the private-key. This private key will be used to encrypt and decrypt data:
```
$example = $class->encrypt("text example", "this my key");
```
The output will be of encryption above will be like this:
```
Object (
    [plain_text] => text example
    [cipher_text] => 01101000101100011000101101110011111001111000010010010101111010111111101111101001111101011000011110000111010010111010101
    [public_key] => 71f7b10vTacnxhz10PgRNQLca10C4amZveG10vlIg7ygo10DQMy72cc10IAQAOJya10u2KMZZ1F10E2K0VHbY10G2oGPVPQ10u2KMZZ1F10E1Rr8N7410I1DvgxBE100iCUmdg1
    [private_key] => this my key
)
```

3. Decrypting data  
To decrypt data, you need to prepare cipher-text, public-key, and private-key.
```
$example = $class->decrypt("01101000101100011000101101110011111001111000010010010101111010111111101111101001111101011000011110000111010010111010101", "this my key", '71f7b10vTacnxhz10PgRNQLca10C4amZveG10vlIg7ygo10DQMy72cc10IAQAOJya10u2KMZZ1F10E2K0VHbY10G2oGPVPQ10u2KMZZ1F10E1Rr8N7410I1DvgxBE100iCUmdg1');
```
The output will be of decryption above will be like this:
```
Object (
    [plain_text] => text example
    [cipher_text] => 01101000101100011000101101110011111001111000010010010101111010111111101111101001111101011000011110000111010010111010101
    [public_key] => 71f7b10vTacnxhz10PgRNQLca10C4amZveG10vlIg7ygo10DQMy72cc10IAQAOJya10u2KMZZ1F10E2K0VHbY10G2oGPVPQ10u2KMZZ1F10E1Rr8N7410I1DvgxBE100iCUmdg1
    [private_key] => this my key
)
```

## License
[MIT](https://choosealicense.com/licenses/mit/)
