<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/choosepayment.php';

class RSFormProFieldBootstrap2Choosepayment extends RSFormProFieldChoosepayment
{
	protected function getRadioInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/bootstrap2/radiogroup.php';

		$field = new RSFormProFieldBootstrap2RadioGroup($config);

		return $field->output;
	}
}