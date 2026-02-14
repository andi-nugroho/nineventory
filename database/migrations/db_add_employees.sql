-- Add employees table and link to loans

USE nineventory;

-- Table: employees
-- Stores employee information
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_karyawan VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100),
    departemen VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    telepon VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add employee_id to peminjaman table
ALTER TABLE peminjaman
ADD COLUMN employee_id INT NULL AFTER user_id,
ADD CONSTRAINT fk_employee_id FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL;
