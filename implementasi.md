# PROJECT PLAN: SISTEM PEMINJAMAN INVENTARIS KANTOR - NINEVENTORY Powered By AI

## 1. Spesifikasi Teknis & Standar Keamanan
- **Backend:** PHP Native 8.x
- **Dependency Manager:** Composer (untuk library AI & Dotenv)
- **Security:** Password Hashing menggunakan `PASSWORD_BCRYPT`
- **Database:** MariaDB/MySQL (Prepared Statements untuk cegah SQL Injection)
- **AI Integration:** Google Gemini atau OpenAI API melalui Guzzle HTTP (Composer)

## 2. Struktur Folder & Composer Setup
/inventaris-kantor
│
├── bin/                # Script eksekusi (optional)
├── config/             # database.php, app.php
├── src/                # Logic utama (Class atau Functions)
├── public/             # Entry point (index.php, assets css/js)
├── vendor/             # Autoload folder (Dihasilkan oleh Composer)
├── .env                # Menyimpan API Key & DB Credentials (Safe)
├── composer.json       # Daftar library (Guzzle, Dotenv, dll)
└── db_schema.sql       # Rancangan database

## 3. Tahapan Pengerjaan (Step-by-Step)

### Phase 1: Environment & Dependency Setup
1. Inisialisasi Composer: `composer init`.
2. Install library utama:
   - `composer require guzzlehttp/guzzle` (Untuk koneksi AI API).
   - `composer require vlucas/phpdotenv` (Untuk mengamankan API Key di file .env).
3. Buat file `public/index.php` yang memanggil `require __DIR__ . '/../vendor/autoload.php';`.

### Phase 2: Database & Security (Bcrypt)
1. Eksekusi `db_schema.sql` di phpMyAdmin.
2. Implementasi Register: Gunakan `password_hash($password, PASSWORD_BCRYPT)` untuk menyimpan password.
3. Implementasi Login: Gunakan `password_verify($password, $hashed_password)` untuk autentikasi.

### Phase 3: Core CRUD (Inventaris & Peminjaman)
1. Dashboard Admin: Manajemen stok barang (Tambah, Edit, Hapus).
2. Dashboard User: List barang tersedia & form pengajuan pinjam.
3. Transaction Logic: Script untuk update status tabel `inventaris` secara otomatis saat admin melakukan approval.

### Phase 4: AI Chatbot Integration
1. Buat class `ChatBot.php` di dalam folder `src/`.
2. Gunakan Guzzle HTTP untuk mengirim prompt ke AI.
3. **Prompt Engineering:** Berikan konteks data stok barang ke AI agar bot bisa menjawab: "Stok laptop saat ini ada 5 yang tersedia".

### Phase 5: UI & Final Testing
1. Integrasi Bootstrap 5 via CDN atau npm.
2. Testing flow: Registrasi -> Login -> Ajukan Pinjam -> Admin Approve -> Cek Stok AI.

## 4. Keunggulan Sistem (Nilai Plus)
- **Modern Workflow:** Menggunakan Composer `autoload` (tidak perlu banyak `include` manual).
- **High Security:** Standar Bcrypt memastikan data user aman meski database bocor.
- **AI-Driven:** Chatbot memberikan pengalaman interaktif yang jarang ada di tugas PHP Native biasa.

## 5. UI/UX: Floating Chatbot (Messenger Style)
- **Position:** Fixed bottom-right corner.
- **Components:**
    - `chat-launcher`: Tombol bulat untuk membuka/tutup chat.
    - `chat-box`: Container utama yang berisi `chat-header`, `chat-logs`, dan `chat-input`.
- **Interaction:** Menggunakan AJAX (JavaScript Fetch) agar saat user bertanya, halaman tidak perlu di-refresh (Single Page Experience).

## 6. Logic: AI Context Injection
- **System Role:** Mengatur kepribadian AI (Persona: Admin Inventaris).
- **Language Support:** Memaksa AI merespon dalam Bahasa Indonesia melalui System Message.
- **Live Data:** PHP akan menarik data dari database dan menyisipkannya ke dalam prompt sebelum dikirim ke API, sehingga jawaban AI selalu akurat dengan stok asli.

## 7. Tambahkan Landing Page diawal untuk menu menu nya Home, About, Features, Pricing, Contact

untuk logo nya juga saya sudah siapkan