<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/quantity.php';

class RSFormProFieldBootstrap4Quantity extends RSFormProFieldQuantity
{
	protected function getSelectInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/bootstrap4/selectlist.php';

		$field = new RSFormProFieldBootstrap4SelectList($config);

		return $field->output;
	}

	protected function getTextboxInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/bootstrap4/textbox.php';

		$field = new RSFormProFieldBootstrap4Textbox($config);

		return $field->output;
	}
}