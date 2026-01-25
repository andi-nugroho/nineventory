-- Update database schema to add tanggal_kembali_rencana column

ALTER TABLE peminjaman
ADD COLUMN tanggal_kembali_rencana DATE NULL
COMMENT 'Expected return date set by admin'
AFTER tanggal_pinjam;
