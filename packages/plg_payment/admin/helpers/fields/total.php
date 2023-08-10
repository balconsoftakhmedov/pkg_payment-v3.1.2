<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';
require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/payment.php';

class RSFormProFieldTotal extends RSFormProField
{
	public function getPreviewInput()
	{
		$total = RSFormProPaymentHelper::getTotalMask(0);

		return '<span class="rsficon rsficon-dollar" style="font-size:24px;margin-right:5px"></span> ' . $total;
	}
	
	public function getFormInput()
	{
		$total = RSFormProPaymentHelper::getTotalMask(0);

		return '<span id="payment_total_' . $this->formId . '" class="rsform_payment_total">' . $total . '</span> <input type="hidden" id="' . $this->getId() . '" value="0" name="' . $this->getName() . '" />';
	}

	public function processValidation($validationType = 'form', $SubmissionId = 0)
	{
		// Skip directory editing since it makes no sense
		if ($validationType == 'directory')
		{
			return true;
		}

		if (!$this->isRequired())
		{
			return true;
		}

		$value = $this->getValue();
		return floatval($value) > 0;
	}
}