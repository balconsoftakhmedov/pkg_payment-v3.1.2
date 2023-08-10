INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES
('paypal.email', ''),
('paypal.return', ''),
('paypal.test', '0'),
('paypal.cancel', ''),
('paypal.language', 'US'),
('paypal.tax.type', '1'),
('paypal.tax.value', '');

DELETE FROM `#__rsform_component_types` WHERE `ComponentTypeId` IN (520);

INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`, `CanBeDuplicated`) VALUES
(520, 'paypal', 0);

DELETE FROM `#__rsform_component_type_fields` WHERE ComponentTypeId = 520;
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES
(520, 'NAME', 'textbox', '', 0),
(520, 'LABEL', 'textbox', '', 1),
(520, 'COMPONENTTYPE', 'hidden', '520', 2),
(520, 'LAYOUTHIDDEN', 'hiddenparam', 'YES', 7);