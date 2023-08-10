<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fieldmultiple.php';
require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/payment.php';

class RSFormProFieldMultipleproducts extends RSFormProFieldMultiple
{
	public function getPreviewInput()
	{
		$out = '<span class="rsficon rsficon-dollar2" style="font-size:24px;margin-right:5px"></span> ';

		return $out . $this->getFormInput();
	}
	
	public function getFormInput()
	{
		$out = '';

		// Adjust items because our field uses price|label
		if ($items = $this->getItems())
		{
			$this->data['ITEMS'] = array();

			$special = array('[c]', '[g]', '[/g]','[d]');

			foreach ($items as $item)
			{
				$price 			= 0;
				$strippedItem 	= str_replace($special, '', $item);
				$hasGroup 		= strpos($item, '[g]') !== false || strpos($item, '[/g]') !== false;

				if (!$hasGroup)
				{
					if (strpos($strippedItem, '|') !== false)
					{
						list($val, $txt) = explode('|', $strippedItem, 2);

						$val = trim($val);
					}
					else
					{
						$val = $txt = $strippedItem;
					}

					if (is_numeric($val) && (float) $val !== (float) 0)
					{
						$price = (float) $val;
						$txt_price = RSFormProPaymentHelper::getPriceMask($txt, $val);
					}
					else
					{
						// No point showing - 0.00
						$txt_price = $txt;

						if ($val !== '' && (float) $val === (float) 0)
						{
							$val = $txt;
						}
					}

					if ($val)
					{
						$tmpItem = $txt . '|' . $txt_price;
					}
					else
					{
						$tmpItem = '|' . $txt_price;
					}

					foreach ($special as $flag)
					{
						if (strpos($item, $flag) !== false)
						{
							$tmpItem .= $flag;
						}
					}

					RSFormProPaymentHelper::addProduct($this->componentId, $price, $txt);
				}
				else
				{
					$tmpItem = $item;
				}

				$this->data['ITEMS'][] = $tmpItem;
			}

			$this->data['ITEMS'] = implode("\n", $this->data['ITEMS']);
		}

		$config = array(
			'formId' 			=> $this->formId,
			'componentId' 		=> $this->componentId,
			'data' 				=> $this->data,
			'value' 			=> $this->value,
			'invalid' 			=> $this->invalid,
			'errorClass' 		=> $this->errorClass,
			'fieldErrorClass' 	=> $this->fieldErrorClass,
			'preview'			=> $this->preview
		);

		$function = 'getPrice_' . $this->formId . '();';

		switch ($this->getProperty('VIEW_TYPE'))
		{
			case 'DROPDOWN':
				$this->addOnChange($config['data']['ADDITIONALATTRIBUTES'], $function);

				$out .= $this->getSelectInput($config);
				break;

			case 'RADIOGROUP':
				$this->addOnChange($config['data']['ADDITIONALATTRIBUTES'], $function);

				$out .= $this->getRadioInput($config);
				break;

			case 'CHECKBOX':
				$this->addOnChange($config['data']['ADDITIONALATTRIBUTES'], $function);

				$out .= $this->getCheckboxInput($config);
				break;
		}

		return $out;
	}

	protected function getSelectInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/selectlist.php';

		$field = new RSFormProFieldSelectList($config);

		$field->setId('payment-' . $this->componentId);

		return $field->output;
	}

	protected function getRadioInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/radiogroup.php';

		$field = new RSFormProFieldRadioGroup($config);

		$field->setId('payment-' . $this->componentId);

		return $field->output;
	}

	protected function getCheckboxInput($config)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/checkboxgroup.php';

		$field = new RSFormProFieldCheckboxGroup($config);

		$field->setId('payment-' . $this->componentId);

		return $field->output;
	}

	protected function addOnChange(&$attributes, $onChange)
	{
		if (preg_match('#onchange="(.*?)"#is', $attributes, $matches))
			$attributes = str_replace($matches[0], str_replace($matches[1], $matches[1].'; '.$onChange, $matches[0]), $attributes);
		else
			$attributes .= ' onchange="'.$onChange.'"';

		return $attributes;
	}

	protected function addOnClick(&$attributes, $onClick)
	{
		return RSFormProHelper::addOnClick($attributes, $onClick);
	}
}