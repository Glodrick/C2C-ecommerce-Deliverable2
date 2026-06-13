UPDATE `product` SET `item_quantity` = 1;
ALTER TABLE `pending_listing` ADD COLUMN `item_quantity` INT(11) NOT NULL DEFAULT 1 AFTER `item_category`;
