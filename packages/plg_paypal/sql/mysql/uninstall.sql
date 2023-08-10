DELETE FROM #__rsform_config WHERE SettingName = 'paypal.email';
DELETE FROM #__rsform_config WHERE SettingName = 'paypal.return';
DELETE FROM #__rsform_config WHERE SettingName = 'paypal.test';
DELETE FROM #__rsform_config WHERE SettingName = 'paypal.cancel';
DELETE FROM #__rsform_config WHERE SettingName = 'paypal.language';
DELETE FROM #__rsform_config WHERE SettingName = 'paypal.tax.type';
DELETE FROM #__rsform_config WHERE SettingName = 'paypal.tax.value';

DELETE FROM #__rsform_component_types WHERE ComponentTypeId = 500;
DELETE FROM #__rsform_component_type_fields WHERE ComponentTypeId = 500;
