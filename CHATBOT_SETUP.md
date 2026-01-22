# NINEVENTORY - AI Chatbot Setup Guide

## ✅ Chatbot Berhasil Diimplementasikan!

Chatbot NINEVENTORY menggunakan **Cohere AI** dengan model `command-a-03-2025`.

---

## 🎯 Fitur Chatbot

### **AI-Powered Responses**
- ✅ Cek stok barang real-time
- ✅ Daftar barang tersedia
- ✅ Statistik peminjaman
- ✅ Lokasi barang
- ✅ Riwayat peminjaman user

### **Fallback System**
Jika Cohere AI error, chatbot otomatis pakai **rule-based** (pattern matching).

---

## 🔧 Setup Cohere AI

### **1. Daftar Cohere**
1. Buka: https://dashboard.cohere.com/welcome/register
2. Daftar dengan Google/Email
3. Verifikasi email

### **2. Buat API Key**
1. Login ke dashboard
2. Klik **"API Keys"** di sidebar
3. Klik **"Create Trial Key"** atau **"+ New Trial key"**
4. Copy API key (format: `7jj2ckqv...`)

### **3. Tambahkan ke `.env`**
```env
COHERE_API_KEY=your_api_key_here
```

---

## 📊 Cohere Free Tier

- ✅ **1000 requests/month** gratis
- ✅ **No credit card** required
- ✅ **Model**: command-a-03-2025
- ✅ **Response time**: ~2 detik

---

## 🧪 Testing

### **Test API**
Buka: `http://localhost/nineventory/test_cohere.php`

Expected result:
```
✅ API Key found: 7jj2ckqv...
Testing Cohere API...
HTTP Code: 200
Response: [text] => ...
```

### **Test Chatbot**
1. Buka dashboard
2. Klik chatbot di kanan bawah
3. Coba quick reply atau ketik pertanyaan

---

## 💡 Cara Kerja

```
User Input
    ↓
Ambil Data Inventory dari Database
    ↓
Build Prompt dengan Context
    ↓
Kirim ke Cohere API
    ↓
AI Response (atau Fallback ke Rule-Based)
    ↓
Display ke User
```

---

## 🎨 Contoh Pertanyaan

**Quick Replies:**
- 📦 Berapa stok laptop yang tersedia?
- 🔍 Barang apa saja yang ada?
- 📊 Berapa total barang yang sedang dipinjam?
- 📍 Dimana lokasi proyektor?

**Natural Language:**
- "Ada laptop gak?"
- "Stok mouse berapa?"
- "Barang apa aja yang bisa dipinjam?"

---

## 🔍 Troubleshooting

### **Error: API key not found**
- Pastikan `COHERE_API_KEY` ada di `.env`
- Restart web server (Apache)

### **Error: Model not found**
- Pastikan model `command-a-03-2025` (bukan `command` atau `command-r`)

### **Chatbot tidak response**
- Cek `test_cohere.php` untuk error detail
- Chatbot akan fallback ke rule-based jika AI error

---

## 📁 File Terkait

- `src/ChatBot.php` - Main chatbot class
- `public/api/chatbot.php` - API endpoint
- `public/assets/js/chatbot.js` - Frontend
- `test_cohere.php` - Test script
- `.env` - API key configuration

---

**Status**: ✅ Production Ready
**Last Updated**: 2026-01-21
