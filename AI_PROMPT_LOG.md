# Log Prompt AI - Tugas 3 IAE

## Identitas

Nama: M Zacky Dhaffary
NIM: 102022430021
Service: Keranjang Service
Tema: E-Commerce
API Key: KEY-MHS-54
Team ID: TEAM-01
Akun SSO: [warga16@ktp.iae.id](mailto:warga16@ktp.iae.id)

---

## Prompt 1 - Memahami Instruksi Tugas 3

**Prompt:**
Saya meminta bantuan AI untuk memahami instruksi Tugas 3 IAE, terutama terkait Federated SSO, SOAP/XML client, RabbitMQ publisher, dan dokumen analisis.

**Hasil:**
AI menjelaskan bahwa Tugas 3 merupakan lanjutan dari Tugas 2. Service yang sudah dibuat, yaitu Keranjang Service, harus diintegrasikan dengan sistem cloud dosen melalui SSO/JWT, SOAP Audit, dan RabbitMQ/message publisher.

---

## Prompt 2 - Menentukan Transaksi Kritis

**Prompt:**
Saya bertanya transaksi mana yang paling tepat dijadikan transaksi kritis pada Keranjang Service.

**Hasil:**
AI menyarankan endpoint `POST /api/v1/carts/items` sebagai transaksi kritis karena endpoint tersebut mengubah data sistem dengan menambahkan produk ke keranjang dan data tersebut akan digunakan pada proses checkout.

---

## Prompt 3 - Menguji Endpoint Health Cloud Dosen

**Prompt:**
Saya meminta arahan untuk menguji apakah server cloud dosen dapat diakses.

**Hasil:**
AI mengarahkan untuk melakukan request `GET https://iae-sso.virtualfri.id/health`. Hasil pengujian menunjukkan server cloud dosen dapat diakses dan RabbitMQ dalam keadaan connected.

---

## Prompt 4 - Menguji Token M2M Menggunakan API Key

**Prompt:**
Saya meminta arahan untuk mengambil token menggunakan API Key yang diberikan dosen.

**Hasil:**
AI mengarahkan untuk melakukan request ke endpoint `/api/v1/auth/token` menggunakan body API key `KEY-MHS-54`. Hasilnya token M2M berhasil diperoleh dengan `token_type: m2m`, `client_id: KEY-MHS-54`, dan `team: TEAM-01`.

---

## Prompt 5 - Menguji Token User SSO

**Prompt:**
Saya meminta arahan untuk menguji akun SSO warga.

**Hasil:**
AI mengarahkan untuk login menggunakan akun `warga16@ktp.iae.id` dan password yang disediakan dosen. Hasilnya token user berhasil diperoleh dengan `token_type: user`.

---

## Prompt 6 - Menguji SOAP Audit

**Prompt:**
Saya meminta bantuan untuk membuat request SOAP/XML sesuai format dosen.

**Hasil:**
AI membantu menyusun SOAP Envelope dengan elemen `TeamID`, `ActivityName`, dan `LogContent`. Pengujian SOAP Audit berhasil dengan response `SUCCESS` dan menghasilkan ReceiptNumber `IAE-LOG-2026-A3F14723`.

---

## Prompt 7 - Mengatasi Error Bearer Token pada Postman

**Prompt:**
Saya mengalami error `Unauthorized: Invalid or expired Bearer token` saat menguji SOAP di Postman.

**Hasil:**
AI membantu menganalisis bahwa error terjadi karena token tidak terbaca dengan benar di header Authorization. Untuk menghindari kesalahan copy-paste token, pengujian SOAP kemudian dilakukan melalui PowerShell dengan token otomatis dari endpoint auth.

---

## Prompt 8 - Menguji RabbitMQ Publish

**Prompt:**
Saya meminta bantuan untuk menguji publish event ke RabbitMQ/message broker dosen.

**Hasil:**
AI membantu menyusun payload event `cart.item.added` dan melakukan request ke `/api/v1/messages/publish`. Hasilnya publish berhasil dengan response `status: success`, exchange `iae.central.exchange`, dan routing key `cart.item.added`.

---

## Prompt 9 - Membuat Konfigurasi Laravel untuk Cloud Dosen

**Prompt:**
Saya meminta bantuan untuk menghubungkan konfigurasi cloud dosen ke Laravel.

**Hasil:**
AI membantu menambahkan konfigurasi pada `.env` dan membuat file `config/iae.php` yang berisi base URL cloud dosen, API Key, Team ID, kode kelompok, nama, NIM, dan routing key event.

---

## Prompt 10 - Membuat Service Laravel untuk Integrasi Cloud

**Prompt:**
Saya meminta bantuan membuat service Laravel untuk mengambil token, mengirim SOAP Audit, dan publish event RabbitMQ.

**Hasil:**
AI membantu membuat file `app/Services/IaeCloudService.php`. Service ini digunakan untuk mengambil token M2M, mengirim SOAP Audit, mengambil ReceiptNumber, dan publish event `cart.item.added`.

---

## Prompt 11 - Menghubungkan Integrasi ke CartController

**Prompt:**
Saya meminta bantuan menghubungkan integrasi cloud dosen ke endpoint `POST /api/v1/carts/items`.

**Hasil:**
AI membantu mengubah function `addItem` pada `CartController`. Setelah item berhasil dibuat, Laravel otomatis mengirim SOAP Audit dan publish event RabbitMQ. Response endpoint juga menampilkan status integrasi.

---

## Prompt 12 - Mengatasi Error Config Laravel

**Prompt:**
Saya mengalami error karena API Key tidak terbaca oleh Laravel.

**Hasil:**
AI membantu melakukan pengecekan menggunakan `php artisan tinker`. Ditemukan bahwa `config('iae.cloud_api_key')` bernilai null. Setelah memperbaiki `.env`, menjalankan `config:clear`, dan `cache:clear`, Laravel berhasil membaca `KEY-MHS-54`.

---

## Prompt 13 - Menguji Endpoint Laravel Terintegrasi

**Prompt:**
Saya meminta bantuan menguji apakah endpoint Laravel sudah benar-benar menjalankan SOAP dan RabbitMQ otomatis.

**Hasil:**
Endpoint `POST /api/v1/carts/items` berhasil dijalankan. Response menunjukkan `m2m_auth` success, `soap_audit` success dengan ReceiptNumber `IAE-LOG-2026-D0C3C8E7`, dan `rabbitmq_publish` success ke exchange `iae.central.exchange`.

---

## Prompt 14 - Membuat Federated SSO/JWT Verification

**Prompt:**
Saya meminta bantuan melengkapi Modul 1 Federated SSO/JWT.

**Hasil:**
AI membantu membuat service `SsoJwtService.php` untuk memverifikasi JWT menggunakan JWKS dari cloud dosen. Karena instalasi package JWT mengalami kendala, validasi dibuat menggunakan native PHP dan OpenSSL.

---

## Prompt 15 - Membuat Endpoint SSO Verify

**Prompt:**
Saya meminta bantuan membuat endpoint untuk menguji validasi JWT SSO di Laravel.

**Hasil:**
AI membantu membuat `SsoController` dan route `GET /api/v1/sso/verify`. Hasil pengujian menunjukkan JWT dari `warga16@ktp.iae.id` berhasil diverifikasi dan dipetakan ke role lokal `customer`.

---

## Kesimpulan Penggunaan AI

AI digunakan sebagai pendamping dalam memahami instruksi Tugas 3, menyusun alur integrasi, menyelesaikan error teknis, membuat service Laravel, menguji koneksi ke cloud dosen, dan menyusun dokumentasi. Seluruh hasil akhir tetap diuji secara langsung melalui endpoint cloud dosen dan endpoint lokal Laravel.

Hasil akhir menunjukkan bahwa Keranjang Service berhasil terhubung dengan Federated SSO/JWT, SOAP/XML Audit, dan RabbitMQ/message publisher.
