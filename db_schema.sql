-- NINEVENTORY Database Schema
-- Office Inventory Loan System with AI Chatbot
-- Created: 2026-01-20

CREATE DATABASE IF NOT EXISTS nineventory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nineventory;

-- Table: users
-- Stores user accounts with bcrypt hashed passwords
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Bcrypt hashed password',
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: inventaris
-- Stores inventory items with stock tracking
CREATE TABLE inventaris (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    stok_total INT NOT NULL DEFAULT 0,
    stok_tersedia INT NOT NULL DEFAULT 0,
    kondisi ENUM('baik', 'rusak ringan', 'rusak berat') NOT NULL DEFAULT 'baik',
    lokasi VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_kategori (kategori),
    INDEX idx_stok_tersedia (stok_tersedia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: peminjaman
-- Stores loan transactions with status tracking
CREATE TABLE peminjaman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    inventaris_id INT NOT NULL,
    jumlah INT NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali_rencana DATE NULL COMMENT 'Expected return date set by admin',
    tanggal_kembali DATE NULL COMMENT 'Actual return date',
    status ENUM('pending', 'approved', 'rejected', 'returned') NOT NULL DEFAULT 'pending',
    keterangan TEXT NULL,
    alasan_reject TEXT NULL COMMENT 'Reason for rejection if status is rejected',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (inventaris_id) REFERENCES inventaris(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_inventaris_id (inventaris_id),
    INDEX idx_status (status),
    INDEX idx_tanggal_pinjam (tanggal_pinjam)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user
-- Username: admin, Password: admin123 (hashed with bcrypt)
-- Username: user, Password: user123 (hashed with bcrypt)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@nineventory.com', '$2y$10$f6epKe4z4aX1Pp73fXeWKuVRUx4RHBOu.dNTEYe/iyCLHHxeZRLEG', 'admin'),
('user', 'user@nineventory.com', '$2y$10$Sd01A2RUlxbPrmPJN.zaSe/JVdoUt3dk.qVnBLwutruOG55viGnva', 'user');

-- Insert sample inventory data
INSERT INTO inventaris (nama_barang, kategori, stok_total, stok_tersedia, kondisi, lokasi, deskripsi) VALUES
('Laptop Dell Latitude 5420', 'Elektronik', 10, 10, 'baik', 'Gudang A - Rak 1', 'Laptop untuk keperluan kantor dengan spesifikasi Intel Core i5, RAM 8GB, SSD 256GB'),
('Mouse Wireless Logitech M185', 'Aksesoris', 25, 25, 'baik', 'Gudang A - Rak 2', 'Mouse wireless dengan koneksi USB receiver'),
('Keyboard Mechanical Keychron K2', 'Aksesoris', 15, 15, 'baik', 'Gudang A - Rak 2', 'Keyboard mechanical dengan switch Gateron Brown'),
('Monitor LG 24 Inch', 'Elektronik', 8, 8, 'baik', 'Gudang B - Rak 1', 'Monitor LED 24 inch Full HD 1080p'),
('Proyektor Epson EB-X06', 'Elektronik', 3, 3, 'baik', 'Ruang Meeting', 'Proyektor untuk presentasi dengan brightness 3600 lumens'),
('Webcam Logitech C920', 'Elektronik', 12, 12, 'baik', 'Gudang A - Rak 3', 'Webcam HD 1080p untuk video conference'),
('Headset Sony WH-1000XM4', 'Aksesoris', 20, 20, 'baik', 'Gudang B - Rak 2', 'Headset wireless dengan noise cancelling'),
('Printer HP LaserJet Pro', 'Elektronik', 5, 5, 'baik', 'Ruang Administrasi', 'Printer laser monochrome untuk dokumen'),
('Scanner Canon LiDE 300', 'Elektronik', 4, 4, 'baik', 'Ruang Administrasi', 'Scanner flatbed untuk dokumen A4'),
('Kabel HDMI 2 Meter', 'Aksesoris', 30, 30, 'baik', 'Gudang A - Rak 4', 'Kabel HDMI untuk koneksi display');

-- Insert sample loan transactions
INSERT INTO peminjaman (user_id, inventaris_id, jumlah, tanggal_pinjam, status, keterangan) VALUES
(2, 1, 1, '2026-01-15', 'approved', 'Untuk keperluan presentasi klien'),
(2, 3, 1, '2026-01-18', 'pending', 'Butuh keyboard untuk workstation baru'),
(2, 5, 1, '2026-01-20', 'pending', 'Presentasi meeting bulanan');
