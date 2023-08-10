<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldMultiPaypal extends RSFormProField
{
	public function getPreviewInput()
	{
		return '<span style="font-size:24px;margin-right:5px" class="rsficon rsficon-paypal"></span> ' . $this->getProperty('LABEL');
	}
}