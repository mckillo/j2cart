ALTER TABLE `#__j2store_orderitemattributes`
MODIFY COLUMN `orderitemattribute_price` decimal(12,4) NOT NULL /** CAN FAIL **/;

ALTER TABLE `#__j2store_orderitems`
MODIFY COLUMN `orderitem_price` decimal(12,4) NOT NULL /** CAN FAIL **/;

ALTER TABLE `#__j2store_shippingrates`
MODIFY COLUMN `shipping_rate_price` decimal(12,4) NOT NULL,
MODIFY COLUMN `shipping_rate_handling` decimal(12,4) NOT NULL /** CAN FAIL **/;
