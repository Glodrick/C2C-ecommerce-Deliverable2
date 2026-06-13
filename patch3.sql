ALTER TABLE `transaction` MODIFY COLUMN `order_status` ENUM('processing', 'received', 'shipped', 'delivered', 'return_pending_seller', 'return_denied_damaged', 'return_denied_seller', 'return_escalated_admin', 'return_refunded', 'return_denied_admin') NOT NULL DEFAULT 'received';
UPDATE `transaction` SET `order_status` = 'received' WHERE `order_status` = 'processing';
ALTER TABLE `transaction` MODIFY COLUMN `order_status` ENUM('received', 'shipped', 'delivered', 'return_pending_seller', 'return_denied_damaged', 'return_denied_seller', 'return_escalated_admin', 'return_refunded', 'return_denied_admin') NOT NULL DEFAULT 'received';

ALTER TABLE `transaction` ADD COLUMN `return_reason` TEXT DEFAULT NULL;
ALTER TABLE `transaction` ADD COLUMN `return_condition` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `transaction` ADD COLUMN `updated_at` DATETIME DEFAULT NULL;
