# NINEVENTORY - Sistem Peminjaman Inventaris Kantor

![NINEVENTORY](public/assets/images/logo.svg)

**NINEVENTORY** adalah sistem peminjaman inventaris kantor modern yang dilengkapi dengan AI Chatbot powered by Google Gemini. Dibangun dengan PHP Native 8.x, sistem ini menawarkan keamanan tingkat enterprise dengan password hashing Bcrypt dan prepared statements untuk mencegah SQL injection.

## 🌟 Fitur Unggulan

- 🔒 **Keamanan Tinggi**: Password hashing dengan Bcrypt dan prepared statements
- 📊 **Manajemen Real-time**: Pantau stok inventaris secara real-time
- 🤖 **AI Chatbot Assistant**: Tanya jawab seputar stok dengan AI yang terintegrasi dengan database
- 📱 **Responsive Design**: Akses dari perangkat apapun
- ⚡ **Fast & Modern**: Dibangun dengan Composer autoload untuk performa optimal
- 📈 **Dashboard Analytics**: Statistik lengkap tentang inventaris dan peminjaman

## 🛠️ Teknologi

- **Backend**: PHP 8.x Native
- **Database**: MariaDB/MySQL
- **Dependency Manager**: Composer
- **AI Integration**: Google Gemini API via Guzzle HTTP
- **Frontend**: Bootstrap 5, Vanilla JavaScript
- **Security**: Bcrypt Password Hashing, PDO Prepared Statements

## 📋 Persyaratan Sistem

- PHP >= 8.0
- MySQL/MariaDB
- Composer
- XAMPP/LAMP/WAMP (untuk development)
- Google Gemini API Key

## 🚀 Instalasi

### 1. Clone atau Download Project

```bash
cd c:\xampp\htdocs\nineventory
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Konfigurasi Environment

Edit file `.env` dan isi dengan konfigurasi Anda:

```env
# Database Configuration
DB_HOST=localhost
DB_NAME=nineventory
DB_USER=root
DB_PASS=

# Google Gemini API
GEMINI_API_KEY=your_gemini_api_key_here

# Application Settings
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost/nineventory/public
```

### 4. Import Database

1. Buka phpMyAdmin
2. Buat database baru bernama `nineventory`
3. Import file `db_schema.sql`

### 5. Jalankan Aplikasi

Akses aplikasi melalui browser:
```
http://localhost/nineventory/public
```

## 👤 Akun Demo

Sistem sudah dilengkapi dengan akun demo:

**Admin:**
- Email: admin@nineventory.com
- Password: admin123

**User:**
- Email: user@nineventory.com
- Password: user123

## 📖 Cara Penggunaan

### Untuk Admin:

1. Login dengan akun admin
2. Kelola inventaris: tambah, edit, hapus barang
3. Setujui/tolak pengajuan peminjaman dari user
4. Tandai barang yang sudah dikembalikan
5. Gunakan AI Chatbot untuk cek stok dengan cepat

### Untuk User:

1. Login atau register akun baru
2. Lihat daftar barang yang tersedia
3. Ajukan peminjaman dengan mengisi form
4. Pantau status pengajuan di riwayat peminjaman
5. Gunakan AI Chatbot untuk bertanya tentang stok

## 🤖 AI Chatbot

Chatbot AI terintegrasi dengan database real-time dan dapat menjawab pertanyaan seperti:

- "Berapa stok laptop yang tersedia?"
- "Barang apa saja yang ada di kategori elektronik?"
- "Dimana lokasi proyektor?"
- "Berapa total barang yang sedang dipinjam?"

## 📁 Struktur Folder

```
/nineventory
├── config/             # Konfigurasi database dan aplikasi
├── src/                # Class utama (Auth, Inventory, Loan, ChatBot)
├── public/             # Entry point dan assets
│   ├── admin/          # Halaman admin
│   ├── user/           # Halaman user
│   ├── api/            # API endpoints
│   ├── assets/         # CSS, JS, Images
│   └── includes/       # Komponen reusable
├── vendor/             # Composer dependencies
├── .env                # Environment variables
├── composer.json       # Composer configuration
└── db_schema.sql       # Database schema
```

## 🔐 Keamanan

- Password di-hash menggunakan `PASSWORD_BCRYPT`
- Semua query menggunakan PDO Prepared Statements
- Session management dengan regenerasi ID
- Input validation di client dan server side
- XSS protection dengan `htmlspecialchars()`

## 🎨 Desain

Sistem menggunakan desain modern SaaS dengan:
- Gradient backgrounds
- Smooth animations
- Card-based layouts
- Responsive grid system
- Professional color palette

## 📝 Lisensi

Project ini dibuat untuk keperluan edukasi dan demonstrasi.

## 👨‍💻 Developer

Developed by **NINEVENTORY Team**

## 🙏 Acknowledgments

- Google Gemini API untuk AI integration
- Bootstrap 5 untuk UI framework
- Guzzle HTTP untuk API client
- PHP Dotenv untuk environment management

---

**NINEVENTORY** - Kelola Inventaris Kantor dengan Mudah 🚀
