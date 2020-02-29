<?php namespace System;

class Crypt
{

    private $iv = 'dsf876dfg564sadf';
    private $key = '345jkh45kjhb4k5j6b';
    const SESS_CIPHER = 'aes-128-cbc';
    const SALT = '55gkk98dfgt56';


    function encrypt($str)
    {
        // Get the MD5 hash salt as a key.
        $key = self::SALT;
        // For an easy iv, MD5 the salt again.
        $iv = $this->_getIv();
        // Encrypt the session ID.
        $ciphertext = openssl_encrypt($str, self::SESS_CIPHER, $key, $options=OPENSSL_RAW_DATA, $iv);
        // Base 64 encode the encrypted session ID.
        $encryptedSessionId = base64_encode($ciphertext);
        // Return it.
        return $encryptedSessionId;
    }

    function decrypt($code)
    {

        // Get the Drupal hash salt as a key.
        $key = self::SALT;
        // Get the iv.
        $iv = $this->_getIv();
        // Decode the encrypted session ID from base 64.
        $decoded = base64_decode($code, TRUE);
        // Decrypt the string.
        $decryptedSessionId = openssl_decrypt($decoded, self::SESS_CIPHER, $key, $options=OPENSSL_RAW_DATA, $iv);
        // Trim the whitespace from the end.
        $session_id = rtrim($decryptedSessionId, '\0');
        // Return it.
        return $session_id;

    }

    public function _getIv() {
        $ivlen = openssl_cipher_iv_length(self::SESS_CIPHER);
        return substr(md5(self::SALT), 0, $ivlen);
    }




}