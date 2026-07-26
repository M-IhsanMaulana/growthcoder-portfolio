<?php

namespace App\Services;

class HashidsHelper
{
    private const ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    private const BASE = 62;

    private const XOR_MASK = 987654321; // Simple mask to obfuscate sequential IDs

    /**
     * Encode an integer ID to a base62 hash.
     */
    public static function encode(int $id): string
    {
        $obfuscated = $id ^ self::XOR_MASK;
        $encoded = '';
        $alphabet = self::ALPHABET;

        while ($obfuscated > 0) {
            $remainder = $obfuscated % self::BASE;
            $encoded = $alphabet[$remainder].$encoded;
            $obfuscated = intdiv($obfuscated, self::BASE);
        }

        return $encoded ?: $alphabet[0];
    }

    /**
     * Decode a base62 hash back to an integer ID.
     */
    public static function decode(string $hash): int
    {
        $alphabet = self::ALPHABET;
        $decoded = 0;
        $len = strlen($hash);

        for ($i = 0; $i < $len; $i++) {
            $pos = strpos($alphabet, $hash[$i]);
            if ($pos === false) {
                return 0;
            }
            $decoded = $decoded * self::BASE + $pos;
        }

        return $decoded ^ self::XOR_MASK;
    }
}
