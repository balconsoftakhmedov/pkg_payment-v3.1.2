INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES
('payment.currency', 'USD'),
('payment.thousands', ','),
('payment.decimal', '.'),
('payment.nodecimals', '2'),
('payment.mask', '{product} - {price} {currency}'),
('payment.totalmask', '{price} {currency}');

UPDATE `#__rsform_component_types` SET `ComponentTypeName`='singleProduct' WHERE `ComponentTypeName` = 'paypalSingleProduct';
UPDATE `#__rsform_component_types` SET `ComponentTypeName`='multipleProducts' WHERE `ComponentTypeName` = 'paypalMultipleProducts';
UPDATE `#__rsform_component_types` SET `ComponentTypeName`='total' WHERE `ComponentTypeName` = 'paypalTotal';

DELETE FROM `#__rsform_component_types` WHERE `ComponentTypeId` IN (21, 22, 23, 26, 27, 28, 29);

INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`, `CanBeDuplicated`) VALUES
(21, 'singleProduct', 0),
(22, 'multipleProducts', 1),
(28, 'donationProduct', 1),
(29, 'quantity', 1),
(23, 'total', 0),
(26, 'discount', 0),
(27, 'choosePayment', 0);

DELETE FROM `#__rsform_component_type_fields` WHERE ComponentTypeId IN (21, 22, 23, 26, 27, 28, 29);

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES
(21, 'PRICE', 'textbox', '', 4),
(21, 'CAPTION', 'textbox', '', 1),
(21, 'NAME', 'hiddenparam', 'rsfp_Product', 0),
(21, 'COMPONENTTYPE', 'hidden', '21', 0),
(21, 'DESCRIPTION', 'textarea', '', 2),
(21, 'SHOW', 'select', 'YES\r\nNO', 3);

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES
(23, 'COMPONENTTYPE', 'hidden', '23', 2),
(23, 'CAPTION', 'textbox', '', 1),
(23, 'NAME', 'textbox', '', 0),
(23, 'REQUIRED', 'select', 'NO\r\nYES', 3),
(23, 'VALIDATETOTAL', 'select', 'NO\r\nYES', 4),
(23, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', 4);

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Properties`, `Ordering`) VALUES
(28, 'NAME', 'textbox', '', '', 1),
(28, 'CAPTION', 'textbox', '', '', 2),
(28, 'REQUIRED', 'select', 'NO\r\nYES', '', 3),
(28, 'SIZE', 'textbox', '20', '', 4),
(28, 'MAXSIZE', 'textbox', '', '', 5),
(28, 'INPUTTYPE', 'select', 'text\r\nnumber\r\nrange', '{"case":{"number":{"show":["ATTRMIN","ATTRMAX","ATTRSTEP"],"hide":["MAXSIZE"]},"range":{"show":["ATTRMIN","ATTRMAX","ATTRSTEP"],"hide":["MAXSIZE"]},"text":{"show":["MAXSIZE"],"hide":["ATTRMIN","ATTRMAX","ATTRSTEP"]}}}', 0),
(28, 'ATTRMIN', 'textbox', '', 'float', 1),
(28, 'ATTRMAX', 'textbox', '', 'float',  2),
(28, 'ATTRSTEP', 'textbox', '1', 'float', 2),
(28, 'VALIDATIONRULE', 'select', '//<code>\r\nreturn RSFormProHelper::getValidationRules();\r\n//</code>', '', 6),
(28, 'VALIDATIONMULTIPLE', 'selectmultiple', '//<code>\r\nreturn RSFormProHelper::getValidationRules(false, true);\r\n//</code>', '', 6),
(28, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', '', 7),
(28, 'ADDITIONALATTRIBUTES', 'textarea', '', '', 8),
(28, 'DEFAULTVALUE', 'textarea', '', '', 9),
(28, 'DESCRIPTION', 'textarea', '', '', 10),
(28, 'COMPONENTTYPE', 'hidden', '28', '', 11),
(28, 'VALIDATIONEXTRA', 'textbox', '', '', 12);

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Properties`, `Ordering`) VALUES
(22, 'REQUIRED', 'select', 'NO\r\nYES', '', 6),
(22, 'ITEMS', 'textarea', '', '', 5),
(22, 'MULTIPLE', 'select', 'NO\r\nYES', '', 3),
(22, 'SIZE', 'textbox', '', '', 2),
(22, 'COMPONENTTYPE', 'hidden', '22', '', 9),
(22, 'CAPTION', 'textbox', '', '', 1),
(22, 'NAME', 'textbox', '', '', 0),
(22, 'ADDITIONALATTRIBUTES', 'textarea', '', '', 7),
(22, 'DESCRIPTION', 'textarea', '', '', 8),
(22, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', '', 9),
(22, 'VIEW_TYPE', 'select', 'DROPDOWN\r\nCHECKBOX\r\nRADIOGROUP', '{"case":{"DROPDOWN":{"show":["MULTIPLE"],"hide":["FLOW"]},"CHECKBOX":{"show":["FLOW"],"hide":["MULTIPLE"]},"RADIOGROUP":{"show":["FLOW"],"hide":["MULTIPLE"]}}}', 4),
(22, 'FLOW', 'select', 'HORIZONTAL\r\nVERTICAL\r\nVERTICAL2COLUMNS\r\nVERTICAL3COlUMNS\r\nVERTICAL4COLUMNS\r\nVERTICAL6COLUMNS', '', 3);

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Properties`, `Ordering`) VALUES
(27, 'NAME', 'textbox', '', '', 0),
(27, 'CAPTION', 'textbox', '', '', 1),
(27, 'VIEW_TYPE', 'select', 'DROPDOWN\r\nRADIOGROUP', '{"case":{"RADIOGROUP":{"show":["FLOW","SELECT_FIRST_ITEM","SHOW_PAYMENT_ICONS"],"hide":[]},"DROPDOWN":{"show":[],"hide":["FLOW","SELECT_FIRST_ITEM","SHOW_PAYMENT_ICONS"]}}}', 2),
(27, 'SHOW_PAYMENT_ICONS', 'select', 'NO\r\nYES', '', 2),
(27, 'FLOW', 'select', 'HORIZONTAL\r\nVERTICAL\r\nVERTICAL2COLUMNS\r\nVERTICAL3COlUMNS\r\nVERTICAL4COLUMNS\r\nVERTICAL6COLUMNS', '', 3),
(27, 'SELECT_FIRST_ITEM', 'select', 'YES\r\nNO', '', 4),
(27, 'ADDITIONALATTRIBUTES', 'textarea', '', '', 4),
(27, 'DESCRIPTION', 'textarea', '', '', 5),
(27, 'SHOW', 'select', 'YES\r\nNO', '', 6),
(27, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', '', 9),
(27, 'COMPONENTTYPE', 'hidden', '27', '', 6);

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Properties`, `Ordering`) VALUES
(26, 'NAME', 'textbox', '', '', 0),
(26, 'CAPTION', 'textbox', '', '', 1),
(26, 'COUPONS', 'textarea', '', '', 2),
(26, 'ADDITIONALATTRIBUTES', 'textarea', '', '', 4),
(26, 'DESCRIPTION', 'textarea', '', '', 5),
(26, 'COMPONENTTYPE', 'hidden', '26', '', 6),
(26, 'REQUIRED', 'select', 'NO\r\nYES', '', 3),
(26, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', '', 4),
(26, 'SIZE', 'textbox', '20', 'numeric', 4),
(26, 'MAXSIZE', 'textbox', '', 'numeric', 5),
(26, 'PLACEHOLDER', 'textbox', '', '', 6);

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Properties`, `Ordering`) VALUES
(29, 'NAME', 'textbox', '', '', 0),
(29, 'CAPTION', 'textbox', '', '', 1),
(29, 'DEFAULTVALUE', 'textarea', '', '', 2),
(29, 'DESCRIPTION', 'textarea', '', '', 3),
(29, 'COMPONENTTYPE', 'hidden', '29', '', 0),
(29, 'PAYMENTFIELD', 'select', '//<code>\r\nreturn class_exists(\'plgSystemRsfppayment\') ? plgSystemRsfppayment::getOtherPaymentFields() : \'\';\r\n//</code>', '', 4),
(29, 'VIEW_TYPE', 'select', 'DROPDOWN\r\nTEXTBOX', '', 3),
(29, 'ATTRMIN', 'textbox', '1', 'float', 5),
(29, 'ATTRMAX', 'textbox', '10', 'float', 6),
(29, 'ATTRSTEP', 'textbox', '1', 'float', 7),
(29, 'ADDITIONALATTRIBUTES', 'textarea', '', '', 4),
(29, 'REQUIRED', 'select', 'NO\r\nYES', '', 3),
(29, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', '', 4);

UPDATE `#__rsform_component_type_fields` SET `FieldValues`='//<code>\r\nreturn RSFormProHelper::getValidationRules();\r\n//</code>' WHERE `ComponentTypeId` = 28 AND `FieldName` = 'VALIDATIONRULE' AND `FieldValues` LIKE '%RSgetValidationRules%';

CREATE TABLE IF NOT EXISTS `#__rsform_payment` (
  `form_id` int(11) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`form_id`)
) DEFAULT CHARSET=utf8;