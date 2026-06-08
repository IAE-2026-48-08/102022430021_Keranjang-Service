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

- Laravel
- SQLite
- REST API
- Swagger/OpenAPI
- GraphQL Lighthouse
- Docker
- API Key Authentication

## Endpoint REST API

### 1. GET Semua Data Keranjang

Endpoint:

GET /api/v1/carts

Fungsi:

Mengambil seluruh data keranjang belanja.

Header:

X-IAE-KEY: 102022430021

### 2. GET Detail Keranjang

Endpoint:

GET /api/v1/carts/{id}

Contoh:

GET /api/v1/carts/1

Fungsi:

Mengambil detail keranjang berdasarkan ID.

Header:

X-IAE-KEY: 102022430021

### 3. POST Tambah Produk ke Keranjang

Endpoint:

POST /api/v1/carts/items

Fungsi:

Menambahkan produk ke dalam keranjang belanja.

Header:

X-IAE-KEY: 102022430021

Body JSON:

{
  "cart_id": 1,
  "product_id": 4,
  "product_name": "Webcam HD",
  "quantity": 1,
  "price": 180000
}

## Format Response

### Response Success

{
  "status": "success",
  "message": "Cart data retrieved successfully",
  "data": [],
  "meta": {
    "service_name": "Cart-Service",
    "api_version": "v1"
  }
}

### Response Error API Key

{
  "status": "error",
  "message": "Invalid or missing API Key",
  "errors": null
}

## Swagger Documentation

Swagger UI dapat diakses melalui:

http://127.0.0.1:8000/api/documentation

Swagger menampilkan endpoint:
- GET /api/v1/carts
- GET /api/v1/carts/{id}
- POST /api/v1/carts/items

## GraphQL

GraphQL endpoint:

POST /graphql

Contoh query:

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

Contoh body JSON:

{
  "query": "{ carts { id customer_name status items { id product_name quantity price } } }"
}

## Cara Menjalankan Project Lokal

Install dependency:

composer install

Generate app key:

php artisan key:generate

Jalankan migration dan seeder:

php artisan migrate:fresh --seed

Jalankan server:

php artisan serve

Akses project:

http://127.0.0.1:8000

## Cara Menjalankan dengan Docker

Build image:

docker build -t keranjang-service .

Jalankan container:

docker run -p 8000:8000 --name keranjang-service-container keranjang-service

Jika container sudah pernah dibuat, jalankan:

docker stop keranjang-service-container
docker rm keranjang-service-container

Lalu jalankan ulang:

docker run -p 8000:8000 --name keranjang-service-container keranjang-service

## API Key

Semua endpoint REST diproteksi menggunakan API Key melalui header:

X-IAE-KEY: 102022430021

Jika API Key tidak dikirim atau salah, sistem akan mengembalikan response 401 Unauthorized.