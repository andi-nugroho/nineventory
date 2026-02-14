-- Sample employee data with linked user_ids

USE nineventory;

-- Insert some sample employee data and link them to user accounts
-- User IDs: 2 for Budi, 3 for Ani, 4 for Rudi (as defined in db_schema.sql)
INSERT INTO `employees` (`user_id`, `nama_karyawan`, `jabatan`, `departemen`, `email`, `telepon`) VALUES
(2, 'Budi Santoso', 'Software Engineer', 'IT', 'budi.santoso@nineventory.com', '081234567890'),
(3, 'Ani Lestari', 'Project Manager', 'IT', 'ani.lestari@nineventory.com', '081234567891'),
(4, 'Rudi Hartono', 'UI/UX Designer', 'Design', 'rudi.hartono@nineventory.com', '081234567892');