<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SsoJwtService
{
    private function baseUrl(): string
    {
        return rtrim(config('iae.cloud_base_url'), '/');
    }

    public function verifyToken(string $token): array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new \Exception('Format JWT tidak valid.');
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;

        $header = json_decode($this->base64UrlDecode($encodedHeader), true);
        $payload = json_decode($this->base64UrlDecode($encodedPayload), true);
        $signature = $this->base64UrlDecode($encodedSignature);

        if (!$header || !$payload) {
            throw new \Exception('Header atau payload JWT tidak valid.');
        }

        if (($header['alg'] ?? null) !== 'RS256') {
            throw new \Exception('Algoritma JWT tidak didukung.');
        }

        if (isset($payload['exp']) && time() > $payload['exp']) {
            throw new \Exception('JWT sudah expired.');
        }

        $jwksResponse = Http::get($this->baseUrl() . '/api/v1/auth/jwks');

        if (!$jwksResponse->successful()) {
            throw new \Exception('Gagal mengambil JWKS dari SSO dosen.');
        }

        $jwks = $jwksResponse->json();
        $publicKeyPem = $this->findPublicKeyPem($jwks, $header['kid'] ?? null);

        $verified = openssl_verify(
            $encodedHeader . '.' . $encodedPayload,
            $signature,
            $publicKeyPem,
            OPENSSL_ALGO_SHA256
        );

        if ($verified !== 1) {
            throw new \Exception('Signature JWT tidak valid.');
        }

        return [
            'valid' => true,
            'subject' => $payload['sub'] ?? null,
            'token_type' => $payload['token_type'] ?? null,
            'grant_type' => $payload['grant_type'] ?? null,
            'profile' => $payload['profile'] ?? null,
            'local_role' => $this->mapLocalRole($payload),
            'payload' => $payload,
        ];
    }

    private function mapLocalRole(array $payload): string
    {
        if (($payload['token_type'] ?? null) === 'user') {
            return 'customer';
        }

        if (($payload['token_type'] ?? null) === 'm2m') {
            return 'service';
        }

        return 'guest';
    }

    private function findPublicKeyPem(array $jwks, ?string $kid): string
    {
        foreach ($jwks['keys'] ?? [] as $key) {
            if ($kid !== null && ($key['kid'] ?? null) !== $kid) {
                continue;
            }

            if (isset($key['x5c'][0])) {
                return "-----BEGIN CERTIFICATE-----\n" .
                    chunk_split($key['x5c'][0], 64, "\n") .
                    "-----END CERTIFICATE-----\n";
            }

            if (isset($key['n'], $key['e'])) {
                return $this->rsaPublicKeyToPem($key['n'], $key['e']);
            }
        }

        throw new \Exception('Public key JWT tidak ditemukan di JWKS.');
    }

    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;

        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }

    private function rsaPublicKeyToPem(string $modulus, string $exponent): string
    {
        $modulus = $this->base64UrlDecode($modulus);
        $exponent = $this->base64UrlDecode($exponent);

        $modulus = "\x00" . $modulus;

        $rsaPublicKey = $this->asn1Sequence(
            $this->asn1Integer($modulus) .
            $this->asn1Integer($exponent)
        );

        $rsaOID = "\x30\x0D\x06\x09\x2A\x86\x48\x86\xF7\x0D\x01\x01\x01\x05\x00";

        $publicKey = $this->asn1Sequence(
            $rsaOID .
            $this->asn1BitString($rsaPublicKey)
        );

        return "-----BEGIN PUBLIC KEY-----\n" .
            chunk_split(base64_encode($publicKey), 64, "\n") .
            "-----END PUBLIC KEY-----\n";
    }

    private function asn1Length(int $length): string
    {
        if ($length < 128) {
            return chr($length);
        }

        $temp = ltrim(pack('N', $length), "\x00");

        return chr(0x80 | strlen($temp)) . $temp;
    }

    private function asn1Integer(string $value): string
    {
        return "\x02" . $this->asn1Length(strlen($value)) . $value;
    }

    private function asn1Sequence(string $value): string
    {
        return "\x30" . $this->asn1Length(strlen($value)) . $value;
    }

    private function asn1BitString(string $value): string
    {
        return "\x03" . $this->asn1Length(strlen($value) + 1) . "\x00" . $value;
    }
}