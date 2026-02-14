-- Populating the new 'image' column in the 'inventaris' table
-- with the filenames from the public/assets/product/ directory.

UPDATE `inventaris` SET `image` = 'm4-macbook-pro.webp' WHERE `nama_barang` LIKE 'Laptop Dell%';
UPDATE `inventaris` SET `image` = 'mouse-apple.jpg' WHERE `nama_barang` LIKE 'Mouse Wireless%';
UPDATE `inventaris` SET `image` = 'keyboard-apple.webp' WHERE `nama_barang` LIKE 'Keyboard Mechanical%';
UPDATE `inventaris` SET `image` = 'monitor-apple.webp' WHERE `nama_barang` LIKE 'Monitor LG%';
UPDATE `inventaris` SET `image` = 'projector.jpg' WHERE `nama_barang` LIKE 'Proyektor Epson%';
UPDATE `inventaris` SET `image` = 'webcam.jpg' WHERE `nama_barang` LIKE 'Webcam Logitech%';
UPDATE `inventaris` SET `image` = 'headset.png' WHERE `nama_barang` LIKE 'Headset Sony%';
UPDATE `inventaris` SET `image` = 'printer.jpg' WHERE `nama_barang` LIKE 'Printer HP%';
UPDATE `inventaris` SET `image` = 'scanner.webp' WHERE `nama_barang` LIKE 'Scanner Canon%';
UPDATE `inventaris` SET `image` = 'kabel.webp' WHERE `nama_barang` LIKE 'Kabel HDMI%';
