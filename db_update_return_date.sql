-- Update database schema to add tanggal_kembali_rencana column
-- Run this SQL in phpMyAdmin or MySQL command line

ALTER TABLE peminjaman 
ADD COLUMN tanggal_kembali_rencana DATE NULL 
COMMENT 'Expected return date set by admin' 
AFTER tanggal_pinjam;
