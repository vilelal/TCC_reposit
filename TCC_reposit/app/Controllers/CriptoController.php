<?php

class CriptoController {
    private static function getKey() {
        $env = $env = parse_ini_file(__DIR__ . "/../../.env");
        return $env["KEY"];
    }
    private static $cipher = "AES-256-CBC";

    public static function encrypt($data) {
        $ivLength = openssl_cipher_iv_length(self::$cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encrypted = openssl_encrypt($data, self::$cipher, self::getKey(), 0, $iv);
        return base64_encode($encrypted . "::" . $iv);
    }

    public static function decrypt($data) {
        $decoded = base64_decode($data);
        if (strpos($decoded, "::") === false) return "";
        list($encryptedData, $iv) = explode("::", $decoded, 2);
        return openssl_decrypt($encryptedData, self::$cipher, self::getKey(), 0, $iv);
    }
}
