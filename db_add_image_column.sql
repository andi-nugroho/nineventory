ALTER TABLE `inventaris`
ADD COLUMN `image` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Image filename for the product'
AFTER `deskripsi`;
