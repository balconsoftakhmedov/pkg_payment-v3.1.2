<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/quantity.php';

class RSFormProFieldBootstrap3Quantity extends RSFormProFieldQuantity
{
	protected function getSelectInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/bootstrap3/selectlist.php';

		$field = new RSFormProFieldBootstrap3SelectList($config);

		return $field->output;
	}

	protected function getTextboxInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/bootstrap3/textbox.php';

		$field = new RSFormProFieldBootstrap3Textbox($config);

		return $field->output;
	}
}