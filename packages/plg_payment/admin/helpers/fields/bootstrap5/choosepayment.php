<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/choosepayment.php';

class RSFormProFieldBootstrap5Choosepayment extends RSFormProFieldChoosepayment
{
	protected function getRadioInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/bootstrap5/radiogroup.php';

		$field = new RSFormProFieldBootstrap5RadioGroup($config);

		return $field->output;
	}

	protected function getSelectInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/bootstrap5/selectlist.php';

		$field = new RSFormProFieldBootstrap5SelectList($config);

		return $field->output;
	}
}