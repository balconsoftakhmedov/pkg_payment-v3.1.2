<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/field.php';
require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/payment.php';

class RSFormProFieldChoosepayment extends RSFormProField
{
	public function getPreviewInput()
	{
		$out = '<span class="rsficon rsficon-list-alt" style="font-size:24px;margin-right:5px"></span> ';

		$items = RSFormProPaymentHelper::getPayments($this->formId);

		switch ($this->getProperty('VIEW_TYPE'))
		{
			case 'DROPDOWN':
				$out .= '<select>';

				foreach ($items as $item)
				{
					$out .= '<option>' . $this->escape($item->text) . '</option>';
				}

				$out .= '</select>';
				break;

			case 'RADIOGROUP':
				$i = 0;
				foreach ($items as $item)
				{
					$selectDefault = false;

					if ($this->getProperty('SELECT_FIRST_ITEM'))
					{
						$selectDefault = $i == 0;
					}

					$checked = $selectDefault ? 'checked="checked"' : '';
					$out .= '<label for="' . $this->getId() . $i . '" class="radio' . ($this->getProperty('FLOW') != 'VERTICAL' ? ' inline' : '') . '"><input ' . $checked . ' type="radio" value="' . $this->escape($item->value) . '" id="' . $this->getId() . $i . '" />' . $this->escape($item->text) . '</label>';
					$i++;
				}
				break;
		}

		return $out;
	}
	
	public function getFormInput()
	{
		$viewType			= $this->getProperty('VIEW_TYPE');
		$showIcons 			= $viewType == 'RADIOGROUP' && $this->getProperty('SHOW_PAYMENT_ICONS');
		$selectFirstItem 	= $viewType == 'RADIOGROUP' && $this->getProperty('SELECT_FIRST_ITEM');

		if (!$this->getProperty('SHOW'))
		{
			RSFormProAssets::addStyleDeclaration('.rsform-block-' . JFilterOutput::stringURLSafe($this->getId()) . ' { display: none !important; }');
		}

		$out = '';

		// Emulate the 'ITEMS' property
		$this->data['ITEMS'] = array();

		if ($items = RSFormProPaymentHelper::getPayments($this->formId))
		{
			foreach ($items as $item)
			{
				$text = $item->text;

				if ($showIcons)
				{
					$text = '<i class="payment-ico-' . $this->filterValue($item->value) . '"></i>' . $text;
				}

				if ($selectFirstItem)
				{
					$selectFirstItem = false;
					$text .= '[c]';
				}

				$this->data['ITEMS'][] = $item->value . '|' . $text;
			}
		}

		$this->data['ITEMS'] = implode("\n", $this->data['ITEMS']);

		$config = array(
			'formId' 			=> $this->formId,
			'componentId' 		=> $this->componentId,
			'data' 				=> $this->data,
			'value' 			=> $this->value,
			'invalid' 			=> $this->invalid,
			'errorClass' 		=> $this->errorClass,
			'fieldErrorClass' 	=> $this->fieldErrorClass
		);

		switch ($viewType)
		{
			case 'DROPDOWN':
				$out = $this->getSelectInput($config);
				break;

			case 'RADIOGROUP':
				if ($showIcons)
				{
					RSFormProAssets::addStyleSheet(JHtml::_('stylesheet', 'plg_system_rsfppayment/style.css', array('relative' => true, 'pathOnly' => true)));
				}

				$out = $this->getRadioInput($config);
				break;
		}

		return $out;
	}

	protected function getSelectInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/selectlist.php';

		$field = new RSFormProFieldSelectList($config);

		return $field->output;
	}

	protected function getRadioInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/radiogroup.php';

		$field = new RSFormProFieldRadioGroup($config);

		return $field->output;
	}

	protected function filterValue($value)
	{
		return JFilterOutput::stringURLSafe($value);
	}

	public function processValidation($validationType = 'form', $SubmissionId = 0)
	{
		// Skip directory editing since it makes no sense
		if ($validationType == 'directory')
		{
			return true;
		}

		$value = $this->getValue();

		return !empty($value);
	}
}