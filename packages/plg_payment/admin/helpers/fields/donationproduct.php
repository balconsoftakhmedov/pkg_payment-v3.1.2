<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/textbox.php';

class RSFormProFieldDonationProduct extends RSFormProFieldTextbox
{
	public function getPreviewInput()
	{
		$value 		= (string) $this->getProperty('DEFAULTVALUE', '');
		$size 	  	= $this->getProperty('SIZE', 0);
		$codeIcon 	= '';

		if ($this->hasCode($value))
		{
			$value 		= JText::_('RSFP_PHP_CODE_PLACEHOLDER');
			$codeIcon	= RSFormProHelper::getIcon('php');
		}

		return $codeIcon . '<span class="rsficon rsficon-moneybag" style="font-size:24px;margin-right:5px"></span> <input type="text" value="' . $this->escape($value).'" size="' . $this->escape($size) . '" />';
	}

	public function processValidation($validationType = 'form', $submissionId = 0)
	{
		$value = $this->getValue();

		if ($this->getProperty('REQUIRED'))
		{
			if (empty($value) || (float) $value == 0)
			{
				return false;
			}
		}

		if ($validationRule = $this->getProperty('VALIDATIONRULE'))
		{
			$validations 	 = array_flip(RSFormProHelper::getValidationRules(true));
			$validationClass = RSFormProHelper::getValidationClass();

			if (isset($validations[$validationRule]) && !call_user_func(array($validationClass, $validationRule), $value, $this->getProperty('VALIDATIONEXTRA'), $this->data))
			{
				return false;
			}
		}

		return true;
	}

	public function getAttributes()
	{
		$attr = parent::getAttributes();

		$attr['oninput'] = 'getPrice_' . $this->formId . '();';

		return $attr;
	}
}