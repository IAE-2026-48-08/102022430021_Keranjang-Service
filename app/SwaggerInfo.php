<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Keranjang Service API',
    version: '1.0.0',
    description: 'Dokumentasi API untuk Keranjang Service pada sistem E-Commerce'
)]
#[OA\Server(
    url: 'http://127.0.0.1:8000',
    description: 'Local Development Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'ApiKeyAuth',
    type: 'apiKey',
    in: 'header',
    name: 'X-IAE-KEY'
)]
class SwaggerInfo
{
}