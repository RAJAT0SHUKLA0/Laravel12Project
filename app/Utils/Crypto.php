<?php

namespace App\Utils;

use App\Services\ApiLogService;
use App\Helper\Message;

class Crypto
{
    protected static $cipher = 'AES-256-CBC';
    protected static $hashAlgo = 'sha256';

    public static function encryptId(int $id): ?string
{
    try {
        $secret = config('constants.CUSTOM_SECRET_KEY');
        if (!$secret) throw new \Exception('Encryption key not configured.');
        $iv = random_bytes(openssl_cipher_iv_length(self::$cipher));
        $ciphertext = openssl_encrypt((string)$id, self::$cipher, $secret, OPENSSL_RAW_DATA, $iv);
        $data = base64_encode($iv . $ciphertext);
        return rtrim(strtr($data, '+/', '-_'), '=');
    } catch (\Exception $e) {
        ApiLogService::error('Encryption failed', $e);
        return null;
    }
}


    
public static function decryptId(string $encrypted)
{
    $secret = config('constants.CUSTOM_SECRET_KEY');
    if (!$secret) throw new \Exception('Encryption key not configured.');
    // Reconstruct standard Base64 by reversing URL-safe substitutions
    $b64 = strtr($encrypted, '-_', '+/');
    $padding = strlen($b64) % 4;
    if ($padding) $b64 .= str_repeat('=', 4 - $padding);
    $decoded = base64_decode($b64, true);
    if ($decoded === false) return null;
    $ivLen = openssl_cipher_iv_length(self::$cipher);
    $iv = substr($decoded, 0, $ivLen);
    $ciphertext = substr($decoded, $ivLen);
    $plaintext = openssl_decrypt($ciphertext, self::$cipher, $secret, OPENSSL_RAW_DATA, $iv);
    return is_numeric($plaintext) ? (int)$plaintext : null;
}
}
