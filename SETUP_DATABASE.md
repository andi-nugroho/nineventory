# Instruksi Setup Database Ninventory

Mohon maaf atas error yang terjadi sebelumnya. Error tersebut disebabkan oleh urutan eksekusi file SQL yang kurang tepat.

Untuk memastikan semua data terbaru diterapkan dengan benar, cara teraman adalah dengan **menginisialisasi ulang database** Anda menggunakan instruksi yang sudah diperbaiki di bawah ini.

---

### Langkah-langkah Setup Database di phpMyAdmin

#### Langkah 1: Hapus Database Lama (Opsional, tapi Sangat Direkomendasikan)
1. Di phpMyAdmin, pilih database `nineventory`.
2. Buka tab **"Operations"** (Operasi).
3. Di bagian "Remove Database" (Hapus Database), klik **"Drop the database (DROP)"**.
4. Konfirmasi penghapusan.

---

#### Langkah 2: Jalankan File SQL secara Berurutan
Di phpMyAdmin, buka tab **"SQL"** dan jalankan isi dari setiap file `.sql` sesuai urutan yang benar di bawah ini.

**PENTING:** Jalankan satu per satu secara berurutan. Salin seluruh isi file, tempelkan di kotak SQL, lalu klik "Go". Lanjutkan ke file berikutnya setelah selesai.

**Urutan Eksekusi:**
1. `db_schema.sql`
2. `db_migration_master_detail.sql`
3. `db_add_employees.sql`
4. `db_link_employees_to_users.sql`
5. `db_sample_employee_data.sql`
6. `db_update_return_date.sql`

Jika Anda ingin melakukannya dalam satu kali eksekusi, Anda bisa menyalin-tempel isi dari semua file tersebut ke dalam satu tab SQL di phpMyAdmin sesuai urutan di atas.

---

### Data Login Karyawan (Password Baru: `user123`)

Setelah database berhasil di-setup, berikut adalah data login untuk setiap karyawan:

| Nama Karyawan  | Email untuk Login                 | Password |
|----------------|-----------------------------------|----------|
| Budi Santoso   | `budi.santoso@nineventory.com`    | `user123`  |
| Ani Lestari    | `ani.lestari@nineventory.com`     | `user123`  |
| Rudi Hartono   | `rudi.hartono@nineventory.com`    | `user123`  |
