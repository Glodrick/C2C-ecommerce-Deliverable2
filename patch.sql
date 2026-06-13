ALTER TABLE `product` ADD COLUMN `item_quantity` INT(11) NOT NULL DEFAULT 5 AFTER `item_in_stock`;
UPDATE `product` SET `item_image` = './assets/peripherals/wirelessJBL.jpg' WHERE `item_image` LIKE '%jbl720bt.jpg%';
