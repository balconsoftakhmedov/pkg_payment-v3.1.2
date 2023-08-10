<?php
/**
 * @package RSForm!Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

define('RSFORM_FIELD_PAYMENT_OFFLINE', 499);

class plgSystemRsfpofflinepayment extends JPlugin
{
	public $componentId 	= RSFORM_FIELD_PAYMENT_OFFLINE;
	public $componentValue = 'offlinepayment';

	protected $autoloadLanguage = true;

	public function onRsformBackendAfterCreateFieldGroups(&$fieldGroups, $self)
	{
		$fieldGroups['payment']->fields[] = (object) array(
			'id' 	=> RSFORM_FIELD_PAYMENT_OFFLINE,
			'name' 	=> JText::_('RSFP_OFFLINE_PAYMENT'),
			'icon'  => 'rsficon rsficon-file-text'
		);
	}
	
	public function onRsformAfterCreatePlaceholders($args)
	{
		$formId = $args['form']->FormId;
		
		if ($components = RSFormProHelper::componentExists($formId, $this->componentId))
		{
			if ($choose	= RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_CHOOSE))
            {
                $chooseData = RSFormProHelper::getComponentProperties($choose[0]);

                $foundWireDetails = false;

                // find the value
                $pos = array_search('{'.$chooseData['NAME'].':value}', $args['placeholders']);
                if ($pos !== false)
                {
                    $payValue = $args['values'][$pos];

                    foreach ($components as $component)
                    {
                        $data = RSFormProHelper::getComponentProperties($component);

                        if ($payValue == $data['LABEL'] || $payValue == $this->componentValue)
                        {
                            $wireDetails = $data['WIRE'];

							$args['placeholders'][] = '{offline}';
							$args['values'][] 		= $wireDetails;

                            if (isset($data['TAX']))
							{
								$grandTotal = $this->calcTax($args['submission']->values['rsfp_Total'], $data['TAX'], $data['TAXTYPE']);

								$args['placeholders'][] = '{grandtotal}';
								$args['values'][] = $this->number_format($grandTotal);

								$args['placeholders'][] = '{tax}';
								$args['values'][] = $this->number_format($grandTotal - (float) $args['submission']->values['rsfp_Total']);
							}

							$foundWireDetails = true;

                            break;
                        }
                    }
                }

                if (!$foundWireDetails)
				{
					$args['placeholders'][] = '{offline}';
					$args['values'][] 		= '';
				}
            }
		}
	}
	
	public function onRsformGetPayment(&$items, $formId)
	{
		if ($components = RSFormProHelper::componentExists($formId, $this->componentId))
		{
			foreach ($components as $component)
			{
				$data = RSFormProHelper::getComponentProperties($component);
				
				$item 			= new stdClass();
				$item->value 	= $data['LABEL'];
				$item->text 	= $data['LABEL'];
				
				if (!empty($data['TAX'])) {
					$item->tax = $data['TAX'];
					$item->tax_type = $data['TAXTYPE'] == '0' ? 'percent' : 'fixed';
				}
				
				// add to array
				$items[] = $item;
			}
		}
	}

	public function onRsformAfterCreateQuickAddPlaceholders(& $placeholders, $componentId)
	{
		if ($componentId == $this->componentId)
		{
			$placeholders['display'][] = '{offline}';
			$placeholders['display'][] = '{grandtotal}';
			$placeholders['display'][] = '{tax}';
		}

		return $placeholders;
	}

	protected function calcTax($price, $amount, $type)
	{
		$price = (float) $price;
		$amount = (float) $amount;

		switch ($type)
		{
			case false:
				$price = $price + (($price * $amount) / 100);
				break;

			case true:
				$price = $price + $amount;
				break;
		}

		return $price;
	}

	private function number_format($val)
	{
		return number_format((float) $val, RSFormProHelper::getConfig('payment.nodecimals'), RSFormProHelper::getConfig('payment.decimal'), RSFormProHelper::getConfig('payment.thousands'));
	}

	public function onRsformDefineHiddenComponents(&$hiddenComponents)
	{
		$hiddenComponents[] = $this->componentId;
	}
}