<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldOfflinepayment extends RSFormProField
{
	public function getPreviewInput()
	{
		return '<span class="rsficon rsficon-file-text" style="font-size:24px;margin-right:5px"></span> ' . $this->getProperty('LABEL');
	}
}