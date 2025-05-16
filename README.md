# Kolektor Database (Mata Elang)

## Latar Belakang

Kolektor Database "Mata Elang" adalah sistem informasi yang dikembangkan khusus untuk debt collector dan tim penagihan untuk melacak kendaraan dengan kredit bermasalah. Dihadapkan dengan tantangan penagihan yang semakin kompleks dan kebutuhan akan akses data yang cepat dan akurat di lapangan, Mata Elang hadir sebagai solusi terintegrasi yang memungkinkan petugas penagihan mengidentifikasi kendaraan target secara efisien berdasarkan nomor polisi dan data konsumen.

Berawal dari kesulitan dalam mencocokkan kendaraan yang ditemui di lapangan dengan database kredit bermasalah yang masih dilakukan secara manual, Mata Elang memberdayakan petugas dengan akses data real-time, memungkinkan identifikasi cepat kendaraan yang sedang dalam proses penagihan atau penarikan.

## Deskripsi Proyek

Kolektor Database "Mata Elang" adalah aplikasi berbasis web dan mobile yang dibangun menggunakan kerangka kerja **Laravel 12** dan **Filament 3.3** untuk antarmuka admin yang responsif dan modern. Sistem ini menyediakan solusi komprehensif untuk:

-   **Penelusuran Kendaraan**: Pencarian instan kendaraan berdasarkan nomor polisi, nomor kontrak, atau nama konsumen
-   **Manajemen Data Konsumen**: Penyimpanan dan pengelolaan informasi lengkap konsumen dengan kredit bermasalah
-   **Detail Kendaraan**: Informasi terperinci tentang kendaraan termasuk nomor rangka, nomor mesin, dan spesifikasi
-   **Kategori Past Due**: Pengelompokan kendaraan berdasarkan tingkat keterlambatan pembayaran
-   **Pengorganisasian Wilayah**: Struktur data berdasarkan resort, sektor, dan sub-sektor untuk penugasan yang efisien
-   **Sistem Berlangganan**: Manajemen akses pengguna berdasarkan status langganan dan pembayaran
-   **Pengelolaan Pembayaran**: Pelacakan dan verifikasi pembayaran langganan layanan
-   **Akses Mobile**: Antarmuka responsif untuk penggunaan di perangkat mobile di lapangan

## Keunggulan

-   **Pencarian Ultra Cepat**: Algoritma pencarian dioptimalkan untuk menemukan kendaraan dalam hitungan detik
-   **Antarmuka Mobile-First**: Dirancang untuk penggunaan di lapangan dengan UI yang intuitif di perangkat mobile
-   **Mode Offline**: Kemampuan menyimpan data kendaraan untuk diakses tanpa koneksi internet
-   **Notifikasi Real-time**: Pemberitahuan instan untuk kendaraan prioritas di sekitar lokasi pengguna
-   **Sistem Berlangganan**: Model bisnis berbasis langganan dengan berbagai durasi (harian, mingguan, bulanan)
-   **Keamanan Data**: Proteksi data sensitif dengan enkripsi end-to-end
-   **Pembaruan Database Otomatis**: Sinkronisasi data terbaru untuk memastikan informasi yang akurat
-   **Integrasi GPS**: Pemetaan lokasi kendaraan berdasarkan data terakhir (fitur opsional)
-   **Dashboard Analitik**: Statistik performa dan laporan aktivitas untuk supervisor
-   **Integrasi API**: Kemampuan untuk terhubung dengan sistem manajemen kredit dan database eksternal

## Kebutuhan Sistem

### Server

-   PHP 8.2 atau lebih tinggi
-   Composer
-   Node.js & NPM
-   Database MySQL/PostgreSQL
-   Server web (Apache/Nginx)
-   Ekstensi PHP: `BCMath`, `Ctype`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `Tokenizer`, `XML`

### Perangkat Pengguna (Minimal)

-   Smartphone dengan Android 7.0+ atau iOS 12.0+
-   Koneksi internet 3G (minimal untuk sinkronisasi)
-   100MB ruang penyimpanan untuk cache data
-   Browser: Chrome 70+, Safari 12+, Firefox 63+

## Instalasi

1. **Clone Repositori**

    ```bash
    git clone https://github.com/mryunkaka/kolektor-db.git
    cd kolektor-db
    ```

2. **Instal Dependensi**

    ```bash
    composer install
    npm install
    ```

3. **Konfigurasi Lingkungan**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    Sesuaikan konfigurasi database dan pengaturan lainnya di file `.env`.

4. **Migrasi Database**

    ```bash
    php artisan migrate --seed
    ```

5. **Kompilasi Aset**

    ```bash
    npm run build
    ```

6. **Jalankan Aplikasi**

    ```bash
    php artisan serve
    ```

    Aplikasi sekarang dapat diakses di [http://localhost:8000](http://localhost:8000)

7. **Akses Panel Admin**
   Panel admin Filament tersedia di [http://localhost:8000/admin](http://localhost:8000/admin)

    Gunakan kredensial default:

    - Email: `admin@example.com`
    - Password: `password`

    > **Penting:** Pastikan untuk mengubah kredensial default segera setelah login pertama.

## Struktur Database

### `users`

```
├── id_users (PK)         # ID unik pengguna
├── name                  # Nama pengguna
├── phone                 # Nomor telepon (untuk login)
├── password              # Password terenkripsi
├── role                  # Peran (admin, user)
├── remember_token        # Token untuk fitur "ingat saya"
├── is_subscribed         # Status berlangganan (true/false)
├── active_until          # Tanggal berakhir langganan
└── timestamps            # created_at, updated_at
```

### `vehicles`

```
├── id_vehicles (PK)      # ID unik kendaraan
├── no_kontrak            # Nomor kontrak kredit
├── nama_konsumen         # Nama pemilik kendaraan
├── no_polisi             # Nomor polisi kendaraan
├── no_rangka             # Nomor rangka kendaraan
├── no_mesin              # Nomor mesin kendaraan
├── merk_tipe             # Merk dan tipe kendaraan
├── past_due              # Jumlah hari keterlambatan pembayaran
├── nama_resort           # Wilayah resort (pengelompokan tingkat 1)
├── nama_sector           # Wilayah sektor (pengelompokan tingkat 2)
├── nama_sub_sector       # Wilayah sub-sektor (pengelompokan tingkat 3)
├── product               # Jenis produk pembiayaan
└── timestamps            # created_at, updated_at
```

### `payments`

```
├── id_payments (PK)      # ID unik pembayaran
├── id_users (FK)         # Referensi ke tabel users
├── nominal               # Jumlah pembayaran
├── duration              # Durasi langganan (1 hari, 7 hari, 30 hari)
├── status                # Status pembayaran (pending, completed, failed)
├── unique_code           # Kode unik untuk identifikasi pembayaran
├── payment_method        # Metode pembayaran
├── payment_proof         # Bukti pembayaran (file path)
├── bank_destination      # Bank tujuan pembayaran
├── expires_at            # Waktu kadaluarsa pembayaran
└── timestamps            # created_at, updated_at
```

### `sessions`

```
├── id (PK)               # ID unik sesi
├── user_id (FK)          # Referensi ke tabel users
├── ip_address            # Alamat IP pengguna
├── user_agent            # User agent browser
├── payload               # Data sesi terenkripsi
└── last_activity         # Timestamp aktivitas terakhir
```

## Penggunaan

Dokumentasi penggunaan lengkap dapat ditemukan di folder `/docs` repositori ini. Secara umum, alur kerja aplikasi meliputi:

1. Login ke sistem menggunakan kredensial yang diberikan
2. Navigasi melalui sidebar untuk mengakses berbagai modul
3. Gunakan fitur CRUD untuk mengelola data di setiap modul
4. Generate laporan sesuai kebutuhan melalui panel pelaporan

## Kontribusi

Kami menyambut kontribusi untuk perbaikan dan pengembangan Kolektor Database. Silakan ikuti langkah-langkah berikut untuk berkontribusi:

1. Fork repositori
2. Buat branch fitur
    ```bash
    git checkout -b fitur-baru
    ```
3. Commit perubahan
    ```bash
    git commit -m 'Menambahkan fitur baru'
    ```
4. Push ke branch
    ```bash
    git push origin fitur-baru
    ```
5. Submit Pull Request

## Lisensi

Hak Cipta © 2025 PT Kolektor Indonesia. Hak cipta dilindungi undang-undang.

Dikembangkan dengan ❤️ menggunakan Laravel 12 dan Filament 3.3
