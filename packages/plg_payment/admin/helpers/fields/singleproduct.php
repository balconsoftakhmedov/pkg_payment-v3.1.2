<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';
require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/payment.php';

class RSFormProFieldSingleproduct extends RSFormProField
{
	public function getPreviewInput()
	{
		$caption = $this->getProperty('CAPTION');
		$price	 = $this->getProperty('PRICE');

		return '<span class="rsficon rsficon-dollar2" style="font-size:24px;margin-right:5px"></span> ' . RSFormProPaymentHelper::getPriceMask($caption, $price);
	}
	
	public function getFormInput()
	{
		$caption = $this->getProperty('CAPTION');
		$price	 = $this->getProperty('PRICE');

		$out  = '<input type="hidden" value="' . $this->escape($price) . '" />';
		$out .= '<input type="hidden" name="' . $this->getName() . '" id="' . $this->getId() . '" value="'.$this->escape($caption).'" />';

		if ($this->getProperty('SHOW'))
		{
			$out .= RSFormProPaymentHelper::getPriceMask($caption, $price);
		}

		return $out;
	}
}