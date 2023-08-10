<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/multipleproducts.php';

class RSFormProFieldUikit3Multipleproducts extends RSFormProFieldMultipleproducts
{
	protected function getSelectInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/uikit3/selectlist.php';

		$field = new RSFormProFieldUikit3SelectList($config);

		$field->setId('payment-' . $this->componentId);

		return $field->output;
	}

	protected function getRadioInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/uikit3/radiogroup.php';

		$field = new RSFormProFieldUikit3RadioGroup($config);

		$field->setId('payment-' . $this->componentId);

		return $field->output;
	}

	protected function getCheckboxInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/uikit3/checkboxgroup.php';

		$field = new RSFormProFieldUikit3CheckboxGroup($config);

		$field->setId('payment-' . $this->componentId);

		return $field->output;
	}
}