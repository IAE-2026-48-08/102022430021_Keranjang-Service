<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class IaeCloudService
{
    private function baseUrl(): string
    {
        return rtrim(config('iae.cloud_base_url'), '/');
    }

    public function getM2mToken(): string
    {
        $response = Http::asJson()->post($this->baseUrl() . '/api/v1/auth/token', [
            'api_key' => config('iae.cloud_api_key'),
        ]);

        if (!$response->successful()) {
            throw new \Exception('Gagal mengambil token M2M dari cloud dosen: ' . $response->body());
        }

        return $response->json('token');
    }

    public function sendCartItemAudit(array $cartItem): array
    {
        $token = $this->getM2mToken();

        $logContent = json_encode([
            'nama' => config('iae.student_name'),
            'nim' => config('iae.student_nim'),
            'api_key' => config('iae.cloud_api_key'),
            'team' => config('iae.team_id'),
            'kode_kelompok' => config('iae.group_code'),
            'service' => 'Keranjang Service',
            'endpoint' => 'POST /api/v1/carts/items',
            'activity' => 'Menambahkan produk ke keranjang',
            'cart_id' => $cartItem['cart_id'] ?? null,
            'product_id' => $cartItem['product_id'] ?? null,
            'product_name' => $cartItem['product_name'] ?? null,
            'quantity' => $cartItem['quantity'] ?? null,
            'price' => $cartItem['price'] ?? null,
        ], JSON_PRETTY_PRINT);

        $soapBody = '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:iae="http://iae.central/audit">
    <soap:Body>
        <iae:AuditRequest>
            <iae:TeamID>' . config('iae.team_id') . '</iae:TeamID>
            <iae:ActivityName>CartItemAdded</iae:ActivityName>
            <iae:LogContent><![CDATA[' . $logContent . ']]></iae:LogContent>
        </iae:AuditRequest>
    </soap:Body>
</soap:Envelope>';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'text/xml',
            'Content-Type' => 'text/xml',
        ])
            ->withBody($soapBody, 'text/xml')
            ->post($this->baseUrl() . '/soap/v1/audit');

        $body = $response->body();

        preg_match('/<iae:ReceiptNumber>(.*?)<\/iae:ReceiptNumber>/', $body, $receiptMatch);
        preg_match('/<iae:Status>(.*?)<\/iae:Status>/', $body, $statusMatch);

        return [
            'success' => $response->successful() && str_contains($body, 'SUCCESS'),
            'status' => $statusMatch[1] ?? null,
            'receipt_number' => $receiptMatch[1] ?? null,
            'raw_response' => $body,
        ];
    }

    public function publishCartItemAdded(array $cartItem, ?string $receiptNumber = null): array
    {
        $token = $this->getM2mToken();

        $payload = [
            'routing_key' => config('iae.event_routing_key'),
            'payload' => [
                'event_name' => config('iae.event_routing_key'),
                'service_name' => 'Keranjang Service',
                'api_version' => 'v1',
                'occurred_at' => now()->utc()->toIso8601String(),
                'team_id' => config('iae.team_id'),
                'kode_kelompok' => config('iae.group_code'),
                'nama' => config('iae.student_name'),
                'nim' => config('iae.student_nim'),
                'api_key' => config('iae.cloud_api_key'),
                'endpoint' => 'POST /api/v1/carts/items',
                'activity' => 'Menambahkan produk ke keranjang',
                'cart_id' => $cartItem['cart_id'] ?? null,
                'product_id' => $cartItem['product_id'] ?? null,
                'product_name' => $cartItem['product_name'] ?? null,
                'quantity' => $cartItem['quantity'] ?? null,
                'price' => $cartItem['price'] ?? null,
                'legacy_receipt_number' => $receiptNumber,
            ],
        ];

        $response = Http::withToken($token)
            ->acceptJson()
            ->asJson()
            ->post($this->baseUrl() . '/api/v1/messages/publish', $payload);

        return [
            'success' => $response->successful(),
            'response' => $response->json(),
        ];
    }
}