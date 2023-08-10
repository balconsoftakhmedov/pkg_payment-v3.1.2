<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';
require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/payment.php';

class RSFormProFieldQuantity extends RSFormProField
{
	public function getPreviewInput()
	{
		static $cache;
		if (!is_array($cache))
		{
			$cache 		= array();
			$fields 	= RSFormProHelper::componentExists($this->formId, array(RSFORM_FIELD_PAYMENT_SINGLE_PRODUCT, RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS, RSFORM_FIELD_PAYMENT_DONATION));
			$all_data 	= RSFormProHelper::getComponentProperties($fields);

			foreach ($all_data as $componentId => $properties)
			{
				$cache[$componentId] = $properties['NAME'];
			}
		}

		$paymentFieldId = $this->getProperty('PAYMENTFIELD');

		if (isset($cache[$paymentFieldId]))
		{
			return '<span class="rsficon rsficon-bars"></span> x ' . $cache[$paymentFieldId];
		}
		else
		{
			return JText::_('RSFP_COMP_PREVIEW_NOT_AVAILABLE');
		}
	}
	
	public function getFormInput()
	{
		$viewType = $this->getProperty('VIEW_TYPE');

		$config = array(
			'formId' 			=> $this->formId,
			'componentId' 		=> $this->componentId,
			'data' 				=> $this->data,
			'value' 			=> $this->value,
			'invalid' 			=> $this->invalid,
			'errorClass' 		=> $this->errorClass,
			'fieldErrorClass' 	=> $this->fieldErrorClass
		);

		$function = 'getPrice_' . $this->formId . '();';

		$min 	= $this->getProperty('ATTRMIN', '');
		$max 	= $this->getProperty('ATTRMAX', '');
		$step 	= $this->getProperty('ATTRSTEP', 1);

		switch ($viewType)
		{
			case 'DROPDOWN':
				$this->addOnChange($config['data']['ADDITIONALATTRIBUTES'], $function);

				$value = $this->getValue();

				$config['data']['ITEMS'] = array();
				if ((float) $min == 0 || (float) $min < 0)
				{
					$min = '0';
				}
				// Can't have an infinite dropdown
				if ((float) $max == 0)
				{
					$max = '10';
				}
				for ($i = $min; $i <= $max; $i = $i + $step)
				{
					if ($value && $i == $value)
					{
						$config['data']['ITEMS'][] = $i . '[c]';
					}
					else
					{
						$config['data']['ITEMS'][] = $i;
					}
				}
				$config['data']['ITEMS'] = implode("\n", $config['data']['ITEMS']);

				return $this->getSelectInput($config);
				break;

			case 'TEXTBOX':
				$this->addOnInput($config['data']['ADDITIONALATTRIBUTES'], $function);

				if ((float) $max == 0)
				{
					$config['data']['ATTRMAX'] = '';
				}
				if ((float) $min == 0 || (float) $min < 0)
				{
					$config['data']['ATTRMIN'] = '0';
				}

				$config['data']['INPUTTYPE'] = 'number';
				return $this->getTextboxInput($config);
				break;
		}
	}

	protected function getSelectInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/selectlist.php';

		$field = new RSFormProFieldSelectList($config);

		return $field->output;
	}

	protected function getTextboxInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/textbox.php';

		$field = new RSFormProFieldTextbox($config);

		return $field->output;
	}

	protected function addOnChange(&$attributes, $onChange)
	{
		if (preg_match('#onchange="(.*?)"#is', $attributes, $matches))
		{
			$attributes = str_replace($matches[0], str_replace($matches[1], $matches[1].'; '.$onChange, $matches[0]), $attributes);
		}
		else
		{
			$attributes .= ' onchange="'.$onChange.'"';
		}

		return $attributes;
	}

	protected function addOnInput(&$attributes, $onInput)
	{
		if (preg_match('#oninput="(.*?)"#is', $attributes, $matches))
		{
			$attributes = str_replace($matches[0], str_replace($matches[1], $matches[1].'; '.$onInput, $matches[0]), $attributes);
		}
		else
		{
			$attributes .= ' oninput="'.$onInput.'"';
		}

		return $attributes;
	}
}