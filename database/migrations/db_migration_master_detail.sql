-- Migration to Master-Detail Structure

-- 1. Backup old table (Safety First)
CREATE TABLE IF NOT EXISTS peminjaman_backup AS SELECT * FROM peminjaman;

-- 2. Create new tables structure
-- We will keep the 'peminjaman' table name for the header but modify it.
-- Since ALTER is messy with FKs, we'll recreate or heavily modify.
-- Let's drop the FKs first if they exist.

ALTER TABLE peminjaman DROP FOREIGN KEY peminjaman_ibfk_2; -- inventaris_id fk
ALTER TABLE peminjaman DROP KEY idx_inventaris_id;
ALTER TABLE peminjaman DROP COLUMN inventaris_id;
ALTER TABLE peminjaman DROP COLUMN jumlah;
ALTER TABLE peminjaman DROP COLUMN alasan_reject; 

-- Add new columns to Header if needed
ALTER TABLE peminjaman ADD COLUMN kode_peminjaman VARCHAR(20) AFTER id;
ALTER TABLE peminjaman ADD COLUMN alasan_reject TEXT AFTER keterangan; -- Re-adding if we want it here

-- Update existing rows to have a code (if we kept rows, but we dropped inventory info so those rows are now invalid loans... actually they are empty headers now. We should truncate to be safe or this is a fresh start).
TRUNCATE TABLE peminjaman; 

-- 3. Create the Detail table
CREATE TABLE peminjaman_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    peminjaman_id INT NOT NULL,
    inventaris_id INT NOT NULL,
    jumlah INT NOT NULL,
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (peminjaman_id) REFERENCES peminjaman(id) ON DELETE CASCADE,
    FOREIGN KEY (inventaris_id) REFERENCES inventaris(id) ON DELETE CASCADE,
    INDEX idx_peminjaman (peminjaman_id),
    INDEX idx_inventaris (inventaris_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
