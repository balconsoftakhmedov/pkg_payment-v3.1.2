<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/choosepayment.php';

class RSFormProFieldUikit3Choosepayment extends RSFormProFieldChoosepayment
{
	protected function getRadioInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/uikit3/radiogroup.php';

		$field = new RSFormProFieldUikit3RadioGroup($config);

		return $field->output;
	}

	protected function getSelectInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/uikit3/selectlist.php';

		$field = new RSFormProFieldUikit3SelectList($config);

		return $field->output;
	}
}