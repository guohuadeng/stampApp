#中文姓名变更，修改lastname字段属性
UPDATE `eav_attribute` SET `is_required` = '0' WHERE `eav_attribute`.`attribute_id` =22;
UPDATE `eav_attribute` SET `is_required` = '0' WHERE `eav_attribute`.`attribute_id` =7;
UPDATE `customer_eav_attribute` SET `validate_rules` = 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:0;}' WHERE `customer_eav_attribute`.`attribute_id` =7;
UPDATE `customer_eav_attribute` SET `validate_rules` = 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:0;}' WHERE `customer_eav_attribute`.`attribute_id` =22;
