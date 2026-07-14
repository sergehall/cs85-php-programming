<?php

namespace App\Services;

use Illuminate\Support\Str;
use InvalidArgumentException;

class TotpAuthenticator
{
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function generateSecret(int $length = 20): string
    {
        return $this->base32Encode(random_bytes($length));
    }

    public function verify(string $secret, string $code, int $window = 1): bool
    {
        return $this->matchingTimeSlice($secret, $code, $window) !== null;
    }

    public function matchingTimeSlice(string $secret, string $code, int $window = 1): ?int
    {
        $normalizedCode = preg_replace('/\s+/', '', $code) ?? '';

        if (! preg_match('/^\d{6}$/', $normalizedCode)) {
            return null;
        }

        $timeSlice = (int) floor(time() / 30);

        for ($offset = -$window; $offset <= $window; $offset++) {
            if (hash_equals($this->code($secret, $timeSlice + $offset), $normalizedCode)) {
                return $timeSlice + $offset;
            }
        }

        return null;
    }

    public function provisioningUri(string $issuer, string $accountName, string $secret): string
    {
        $label = rawurlencode($issuer.':'.$accountName);
        $query = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => 6,
            'period' => 30,
        ]);

        return "otpauth://totp/{$label}?{$query}";
    }

    public function code(string $secret, ?int $timeSlice = null): string
    {
        $timeSlice ??= (int) floor(time() / 30);
        $secretKey = $this->base32Decode($secret);
        $time = pack('N*', 0).pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $time, $secretKey, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $truncatedHash = unpack('N', substr($hash, $offset, 4));

        if (! is_array($truncatedHash) || ! isset($truncatedHash[1])) {
            throw new InvalidArgumentException('Invalid TOTP hash.');
        }

        $value = $truncatedHash[1] & 0x7FFFFFFF;

        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    private function base32Encode(string $bytes): string
    {
        $bits = '';
        foreach (str_split($bytes) as $byte) {
            $bits .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT);
        }

        $encoded = '';
        foreach (str_split($bits, 5) as $chunk) {
            $encoded .= self::BASE32_ALPHABET[bindec(str_pad($chunk, 5, '0', STR_PAD_RIGHT))];
        }

        return $encoded;
    }

    private function base32Decode(string $secret): string
    {
        $secret = Str::upper(preg_replace('/[^A-Z2-7]/i', '', $secret) ?? '');
        $bits = '';

        foreach (str_split($secret) as $character) {
            $position = strpos(self::BASE32_ALPHABET, $character);

            if ($position === false) {
                throw new InvalidArgumentException('Invalid base32 secret.');
            }

            $bits .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }

        $bytes = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $bytes .= chr(bindec($byte));
            }
        }

        return $bytes;
    }
}
