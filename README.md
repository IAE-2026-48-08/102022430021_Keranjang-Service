# Keranjang Service - E-Commerce

## Identitas

Nama: M Zacky Dhaffary  
NIM: 102022430021  
Mata Kuliah: Integrasi Aplikasi Enterprise  
Service: Keranjang Service  

## Deskripsi Service

Keranjang Service adalah mini-service yang digunakan untuk mengelola data keranjang belanja customer pada proses pembelian produk di sistem E-Commerce.

Service ini digunakan untuk:
1. Menampilkan data keranjang belanja.
2. Menampilkan detail keranjang belanja.
3. Menambahkan produk ke dalam keranjang belanja.

## Teknologi yang Digunakan

- Laravel 12 (PHP 8.3)
- SQLite
- REST API
- Swagger/OpenAPI (L5-Swagger)
- GraphQL (Lighthouse)
- GraphQL Playground (GraphiQL)
- Docker & Docker Compose
- API Key Authentication (X-IAE-KEY)

## Cara Menjalankan dengan Docker Compose (Direkomendasikan)

Cukup jalankan satu perintah:

```bash
docker-compose up -d --build
```

Tunggu sekitar 2-3 menit hingga proses selesai, lalu cek status:

```bash
docker-compose ps
```

Pastikan status container adalah `healthy`.

Untuk menghentikan:

```bash
docker-compose down
```

## URL yang Dapat Diakses

| URL | Fungsi |
|-----|--------|
| http://localhost:8000/api/v1/carts | REST API - Semua keranjang |
| http://localhost:8000/api/v1/carts/{id} | REST API - Detail keranjang |
| http://localhost:8000/api/v1/carts/items | REST API - Tambah item (POST) |
| http://localhost:8000/api/documentation | Swagger UI (Dokumentasi API) |
| http://localhost:8000/graphiql | GraphQL Playground |
| http://localhost:8000/graphql | GraphQL Endpoint |

## API Key

Semua endpoint REST API dan GraphQL diproteksi menggunakan API Key melalui header:

```
X-IAE-KEY: 102022430021
```

Jika API Key tidak dikirim atau salah, sistem akan mengembalikan response 401 Unauthorized.

## Endpoint REST API

### 1. GET Semua Data Keranjang

```
GET /api/v1/carts
Header: X-IAE-KEY: 102022430021
```

### 2. GET Detail Keranjang

```
GET /api/v1/carts/{id}
Header: X-IAE-KEY: 102022430021
```

### 3. POST Tambah Produk ke Keranjang

```
POST /api/v1/carts/items
Header: X-IAE-KEY: 102022430021
Content-Type: application/json
```

Body JSON:

```json
{
  "cart_id": 1,
  "product_id": 4,
  "product_name": "Webcam HD",
  "quantity": 1,
  "price": 180000
}
```

## Format Response (Standard Integration Contract)

### Response Success (2xx)

```json
{
  "status": "success",
  "message": "Cart data retrieved successfully",
  "data": [],
  "meta": {
    "service_name": "Cart-Service",
    "api_version": "v1"
  }
}
```

### Response Error (4xx/5xx)

```json
{
  "status": "error",
  "message": "Invalid or missing API Key",
  "errors": null
}
```

### Response Validation Error (422)

```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "cart_id": ["The cart id field is required."]
  }
}
```

## Swagger Documentation

Swagger UI dapat diakses melalui:

http://localhost:8000/api/documentation

Swagger menampilkan endpoint:
- GET /api/v1/carts
- GET /api/v1/carts/{id}
- POST /api/v1/carts/items

## GraphQL

### GraphQL Playground

GraphQL Playground (GraphiQL) dapat diakses melalui browser:

http://localhost:8000/graphiql

### GraphQL Endpoint

```
POST /graphql
Header: X-IAE-KEY: 102022430021
Content-Type: application/json
```

### Contoh Query - Semua Keranjang

```graphql
{
  carts {
    id
    customer_name
    status
    items {
      id
      product_name
      quantity
      price
    }
  }
}
```

### Contoh Query - Detail Keranjang

```graphql
{
  cart(id: 1) {
    id
    customer_name
    status
    items {
      id
      product_name
      quantity
      price
    }
  }
}
```

### Contoh Body JSON untuk GraphQL

```json
{
  "query": "{ carts { id customer_name status items { id product_name quantity price } } }"
}
```

## Cara Menjalankan Project Lokal (Tanpa Docker)

Install dependency:

```bash
composer install
```

Copy file environment:

```bash
cp .env.example .env
```

Generate app key:

```bash
php artisan key:generate
```

Jalankan migration dan seeder:

```bash
php artisan migrate:fresh --seed
```

Generate Swagger docs:

```bash
php artisan l5-swagger:generate
```

Jalankan server:

```bash
php artisan serve
```

Akses project:

http://127.0.0.1:8000