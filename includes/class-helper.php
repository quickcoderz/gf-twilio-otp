<?php
namespace GF_Twilio_OTP;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Helper {

    public static function encrypt($data, $key)
    {
        $cipher_method = 'AES-256-CBC';
        $iv_length = openssl_cipher_iv_length($cipher_method);
        $iv = openssl_random_pseudo_bytes($iv_length);
        $encrypted = openssl_encrypt($data, $cipher_method, $key, 0, $iv);
        
        // Check if encryption was successful
        if ($encrypted === false) {
            error_log('Encryption failed: ' . openssl_error_string());
            return false;
        }
        
        return base64_encode($iv . $encrypted);
    }

    public static function decrypt($data, $key)
    {
        $cipher_method = 'AES-256-CBC';
        $data = base64_decode($data);
        
        if ($data === false) {
            error_log('Base64 decode failed.');
            return false;
        }

        $iv_length = openssl_cipher_iv_length($cipher_method);
        $iv = substr($data, 0, $iv_length);
        $encrypted_data = substr($data, $iv_length);

        $decrypted = openssl_decrypt($encrypted_data, $cipher_method, $key, 0, $iv);
        
        // Check if decryption was successful
        if ($decrypted === false) {
            error_log('Decryption failed: ' . openssl_error_string());
            return false;
        }

        return $decrypted;
    }

    public static function dd(...$params)
    {
        foreach ($params as $param) {
            echo '<pre>';
            print_r($param);
            echo '</pre>';
        }
        die();
    }

    public static function dump(...$data)
    {
        foreach ($data as $item) {
            echo '<pre>';
            print_r($item);
            echo '</pre>';
        }
    }
}