DELETE FROM `#__rsform_component_types` WHERE `ComponentTypeId` IN (499);

INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`, `CanBeDuplicated`) VALUES
(499, 'offlinePayment', 1);

DELETE FROM `#__rsform_component_type_fields` WHERE ComponentTypeId = 499;
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Properties`, `Ordering`) VALUES
(499, 'NAME', 'textbox', '', '', 0),
(499, 'LABEL', 'textbox', '', '', 1),
(499, 'WIRE', 'textarea', 'Thank you for your purchase. Payment due is {grandtotal}. Tax is {tax}.', '', 2),
(499, 'TAXTYPE', 'select', '0|PERCENT\r\n1|FIXED', '', 3),
(499, 'TAX', 'textbox', '0', 'numeric', 4),
(499, 'COMPONENTTYPE', 'hidden', '499', '', 6),
(499, 'LAYOUTHIDDEN', 'hiddenparam', 'YES', '', 7);