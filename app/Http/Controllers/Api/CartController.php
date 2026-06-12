<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
class CartController extends Controller
{
   #[OA\Get(
    path: '/api/v1/carts',
    summary: 'Mengambil seluruh data keranjang',
    security: [['ApiKeyAuth' => []]],
    tags: ['Carts'],
    responses: [
        new OA\Response(response: 200, description: 'Success'),
        new OA\Response(response: 401, description: 'Invalid or missing API Key')
    ]
)]
public function index()
    {
        $carts = Cart::with('items')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Cart data retrieved successfully',
            'data' => $carts,
            'meta' => [
                'service_name' => 'Cart-Service',
                'api_version' => 'v1'
            ]
        ], 200);
    }
#[OA\Get(
    path: '/api/v1/carts/{id}',
    summary: 'Mengambil detail keranjang',
    security: [['ApiKeyAuth' => []]],
    tags: ['Carts'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        )
    ],
    responses: [
        new OA\Response(response: 200, description: 'Success'),
        new OA\Response(response: 404, description: 'Cart not found'),
        new OA\Response(response: 401, description: 'Invalid or missing API Key')
    ]
)]
public function show($id)
    {
        $cart = Cart::with('items')->find($id);

        if (!$cart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart not found',
                'errors' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cart detail retrieved successfully',
            'data' => $cart,
            'meta' => [
                'service_name' => 'Cart-Service',
                'api_version' => 'v1'
            ]
        ], 200);
    }
#[OA\Post(
    path: '/api/v1/carts/items',
    summary: 'Menambahkan produk ke keranjang',
    security: [['ApiKeyAuth' => []]],
    tags: ['Carts'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['cart_id', 'product_id', 'product_name', 'quantity', 'price'],
            properties: [
                new OA\Property(property: 'cart_id', type: 'integer', example: 1),
                new OA\Property(property: 'product_id', type: 'integer', example: 4),
                new OA\Property(property: 'product_name', type: 'string', example: 'Webcam HD'),
                new OA\Property(property: 'quantity', type: 'integer', example: 1),
                new OA\Property(property: 'price', type: 'number', example: 180000)
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: 'Created'),
        new OA\Response(response: 401, description: 'Invalid or missing API Key')
    ]
)]
public function addItem(Request $request)
{
    $validated = $request->validate([
        'cart_id' => 'required|exists:carts,id',
        'product_id' => 'required|integer',
        'product_name' => 'required|string',
        'quantity' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0',
    ]);

    $cartItem = CartItem::create($validated);

    $iaeCloudService = app(\App\Services\IaeCloudService::class);

    $auditResult = $iaeCloudService->sendCartItemAudit($cartItem->toArray());

    $publishResult = $iaeCloudService->publishCartItemAdded(
        $cartItem->toArray(),
        $auditResult['receipt_number'] ?? null
    );

    return response()->json([
        'status' => 'success',
        'message' => 'Product added to cart successfully',
        'data' => $cartItem,
        'integration' => [
            'm2m_auth' => [
                'status' => 'success',
                'api_key' => config('iae.cloud_api_key'),
                'team' => config('iae.team_id'),
            ],
            'soap_audit' => [
                'success' => $auditResult['success'],
                'status' => $auditResult['status'],
                'receipt_number' => $auditResult['receipt_number'],
            ],
            'rabbitmq_publish' => [
                'success' => $publishResult['success'],
                'response' => $publishResult['response'],
            ],
        ],
        'meta' => [
            'service_name' => 'Cart-Service',
            'api_version' => 'v1',
            'routing_key' => config('iae.event_routing_key'),
        ],
    ], 201);
}
}