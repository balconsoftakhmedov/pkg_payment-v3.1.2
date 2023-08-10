<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/multipleproducts.php';

class RSFormProFieldUikitMultipleproducts extends RSFormProFieldMultipleproducts
{
	protected function getRadioInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/uikit/radiogroup.php';

		$field = new RSFormProFieldUikitRadioGroup($config);

		return $field->output;
	}

	protected function getCheckboxInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/uikit/checkboxgroup.php';

		$field = new RSFormProFieldUikitCheckboxGroup($config);

		return $field->output;
	}
}