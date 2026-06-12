<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SsoJwtService;
use Illuminate\Http\Request;

class SsoController extends Controller
{
    public function verify(Request $request, SsoJwtService $ssoJwtService)
    {
        $authorizationHeader = $request->header('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bearer token is required',
            ], 401);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);

        try {
            $jwtData = $ssoJwtService->verifyToken($token);

            return response()->json([
                'status' => 'success',
                'message' => 'SSO JWT verified successfully',
                'data' => [
                    'subject' => $jwtData['subject'],
                    'token_type' => $jwtData['token_type'],
                    'grant_type' => $jwtData['grant_type'],
                    'profile' => $jwtData['profile'],
                    'local_role' => $jwtData['local_role'],
                ],
                'meta' => [
                    'service_name' => 'Cart-Service',
                    'module' => 'Federated SSO',
                    'api_version' => 'v1',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid SSO JWT',
                'error' => $e->getMessage(),
            ], 401);
        }
    }
}