<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

define('RSFORM_FIELD_PAYMENT_SINGLE_PRODUCT', 21);
define('RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS', 22);
define('RSFORM_FIELD_PAYMENT_DONATION', 28);
define('RSFORM_FIELD_PAYMENT_TOTAL', 23);
define('RSFORM_FIELD_PAYMENT_CHOOSE', 27);
define('RSFORM_FIELD_PAYMENT_DISCOUNT', 26);
define('RSFORM_FIELD_PAYMENT_QUANTITY', 29);

class plgSystemRsfppayment extends JPlugin
{
	protected $newComponents = array(
		RSFORM_FIELD_PAYMENT_SINGLE_PRODUCT,
		RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS,
		RSFORM_FIELD_PAYMENT_TOTAL,
		RSFORM_FIELD_PAYMENT_CHOOSE,
		RSFORM_FIELD_PAYMENT_DONATION,
		RSFORM_FIELD_PAYMENT_DISCOUNT
	);

	protected $autoloadLanguage = true;

	public function onRsformBackendAfterCreateFieldGroups(&$fieldGroups, $self)
	{
		$formId = JFactory::getApplication()->input->getInt('formId');

		$fieldGroups['payment']->name = JText::_('RSFP_PAYMENT');

		$exists = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_SINGLE_PRODUCT);
		$fieldGroups['payment']->fields[] = (object) array(
			'id' 	=> RSFORM_FIELD_PAYMENT_SINGLE_PRODUCT,
			'name' 	=> JText::_('RSFP_SPRODUCT'),
			'icon'  => 'rsficon rsficon-dollar2',
			'exists' => $exists ? $exists[0] : false
		);
		$fieldGroups['payment']->fields[] = (object) array(
			'id' 	=> RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS,
			'name' 	=> JText::_('RSFP_MPRODUCT'),
			'icon'  => 'rsficon rsficon-dollar2'
		);
		$fieldGroups['payment']->fields[] = (object) array(
			'id' 	=> RSFORM_FIELD_PAYMENT_DONATION,
			'name' 	=> JText::_('RSFP_DONATION'),
			'icon'  => 'rsficon rsficon-moneybag'
		);
		$fieldGroups['payment']->fields[] = (object) array(
			'id' 	=> RSFORM_FIELD_PAYMENT_QUANTITY,
			'name' 	=> JText::_('RSFP_QUANTITY'),
			'icon'  => 'rsficon rsficon-bars'
		);
		$exists = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_DISCOUNT);
		$fieldGroups['payment']->fields[] = (object) array(
			'id' 	=> RSFORM_FIELD_PAYMENT_DISCOUNT,
			'name' 	=> JText::_('RSFP_DISCOUNT'),
			'icon'  => 'rsficon rsficon-money',
			'exists' => $exists ? $exists[0] : false
		);
		$exists = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_TOTAL);
		$fieldGroups['payment']->fields[] = (object) array(
			'id' 	=> RSFORM_FIELD_PAYMENT_TOTAL,
			'name' 	=> JText::_('RSFP_TOTAL'),
			'icon'  => 'rsficon rsficon-dollar',
			'exists' => $exists ? $exists[0] : false
		);
		$exists = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_CHOOSE);
		$fieldGroups['payment']->fields[] = (object) array(
			'id' 	=> RSFORM_FIELD_PAYMENT_CHOOSE,
			'name' 	=> JText::_('RSFP_CHOOSE_PAYMENT'),
			'icon'  => 'rsficon rsficon-list-alt',
			'exists' => $exists ? $exists[0] : false
		);
	}

	protected function getTable()
	{
		return JTable::getInstance('RSForm_Payment', 'Table');
	}

	public function onRsformBackendAfterShowFormEditTabs() {
		$formId = JFactory::getApplication()->input->getInt('formId');

		$row = $this->getTable();
		if (!$row)
		{
			return;
		}

		if (!$row->load($formId))
		{
			$row->setDefaultParams();
		}

		$data = $row->getProperties();

		JForm::addFormPath(__DIR__ . '/forms');

		$form = JForm::getInstance( 'plg_system_rsfppayment.tab', 'tab', array('control' => 'payment'), false, false );
		$form->bind($data['params']);

		?>
		<div id="paymentdiv">
			<fieldset class="form-horizontal">
				<legend class="rsfp-legend"><?php echo JText::_('RSFP_PAYMENT_EMAIL_SETTINGS'); ?></legend>
				<div class="alert alert-info"><?php echo JText::_('RSFP_PAYMENT_EMAIL_SETTINGS_DESC'); ?></div>
				<?php
				if ($fields = $form->getFieldset('email'))
				{
					foreach ($fields as $field)
					{
						echo $field->renderField();
					}
				}
				?>
				<legend class="rsfp-legend"><?php echo JText::_('RSFP_PAYMENT_SILENTPOST_SETTINGS'); ?></legend>
				<?php
				if ($fields = $form->getFieldset('silentpost'))
				{
					foreach ($fields as $field)
					{
						echo $field->renderField();
					}
				}
				?>
				<legend class="rsfp-legend"><?php echo JText::_('RSFP_PAYMENT_MAPPINGS_SETTINGS'); ?></legend>
				<?php
				if ($fields = $form->getFieldset('mappings'))
				{
					foreach ($fields as $field)
					{
						echo $field->renderField();
					}
				}
				?>
			</fieldset>
		</div>
		<?php
	}

	public function onRsformBackendAfterShowFormEditTabsTab() {
		?>
		<li><a href="javascript: void(0);"><span class="rsficon rsficon-dollar2"></span><span class="inner-text"><?php echo JText::_('RSFP_PAYMENT_SETTINGS'); ?></span></a></li>
		<?php
	}

	public function onRsformFormSave($form)
	{
		$app  = JFactory::getApplication();
		$row  = $this->getTable();
		$data = array(
			'form_id' => $form->FormId ,
			'params'  => $app->input->post->get('payment', array(), 'array')
		);

		if (!$row)
		{
			return false;
		}

		return $row->save($data);
	}

	public function onRsformBackendFormCopy($args)
	{
		$formId = $args['formId'];
		$newFormId = $args['newFormId'];

		if ($row = $this->getTable())
		{
			if ($row->load($formId))
			{
				$row->save(array('form_id' => $newFormId, 'params' => $row->params));
			}
		}
	}

	public function onRsformAfterCreateQuickAddPlaceholders(& $placeholders, $componentId)
	{
		if (in_array($componentId, $this->newComponents))
		{
			switch ($componentId)
			{
				case RSFORM_FIELD_PAYMENT_TOTAL:
					$placeholders['display'][] = '{' . $placeholders['name'] . ':price}';
					$placeholders['display'][] = '{' . $placeholders['name'] . ':amount}';
				break;

				case RSFORM_FIELD_PAYMENT_SINGLE_PRODUCT:
				case RSFORM_FIELD_PAYMENT_DONATION:
				case RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS:
					$placeholders['display'][] = '{' . $placeholders['name'] . ':price}';
					$placeholders['display'][] = '{' . $placeholders['name'] . ':totalprice}';
					$placeholders['display'][] = '{' . $placeholders['name'] . ':amount}';
					$placeholders['display'][] = '{' . $placeholders['name'] . ':totalamount}';
					$placeholders['display'][] = '{' . $placeholders['name'] . ':quantity}';
					break;

				case RSFORM_FIELD_PAYMENT_CHOOSE:
					$placeholders['display'][] = '{' . $placeholders['name'] . ':text}';
					$placeholders['display'][] = '{_STATUS:caption}';
					$placeholders['display'][] = '{_STATUS:value}';
					$placeholders['display'][] = '{_TRANSACTION_ID:caption}';
					$placeholders['display'][] = '{_TRANSACTION_ID:value}';
				break;

				case RSFORM_FIELD_PAYMENT_DISCOUNT:
					$placeholders['display'][] = '{discount}';
					$placeholders['display'][] = '{discountprice}';
					break;
			}
		}

		return $placeholders;
    }

	public function onRsformBackendAfterShowConfigurationTabs($tabs) {
		$tabs->addTitle(JText::_('RSFP_PAYMENT'), 'form-payment');
		$tabs->addContent($this->configurationScreen());
	}

	public function onRsformBackendCreateConditionOptionFields($args)
	{
		$args['types'][] = RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS;
		$args['types'][] = RSFORM_FIELD_PAYMENT_CHOOSE;

		if ($componentId = RSFormProHelper::componentExists($args['formId'], RSFORM_FIELD_PAYMENT_CHOOSE))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/payment.php';

			$properties =& RSFormProHelper::getComponentProperties($componentId[0]);
			if ($items = RSFormProPaymentHelper::getPayments($args['formId']))
			{
				$properties['ITEMS'] = array();
				foreach ($items as $item)
				{
					$properties['ITEMS'][] = $item->value;
				}
				$properties['ITEMS'] = implode("\n", $properties['ITEMS']);
			}
		}
	}

	public function onRsformBackendCreateConditionOptionFieldItem($args)
	{
		if ($args['field']->ComponentTypeId == RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS)
		{
			if ($args['item']->value !== '')
			{
				$args['item']->value = $args['item']->label;
			}
		}
	}

	public function onRsformAfterConfirmPayment($SubmissionId)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';
		$submission = RSFormProSubmissionsHelper::getSubmission($SubmissionId, false);
		if ($submission)
		{
			RSFormProHelper::sendSubmissionEmails($SubmissionId);

			if ($params = $this->_getPaymentSettings($submission->FormId))
			{
				if (!empty($params->Mappings))
				{
					RSFormProHelper::doMappings(RSFormProHelper::getMappings($submission->FormId), array('SubmissionId' => $SubmissionId));
				}
				if (!empty($params->SilentPost))
				{
					RSFormProHelper::doSilentPost(RSFormProHelper::getSilentPost($submission->FormId), array('SubmissionId' => $SubmissionId));
				}
			}
		}
	}

	public function onRsformFrontendBeforeFormDisplay($args) {
		$formId		= $args['formId'];
		$formLayout = &$args['formLayout'];
		$nodecimals = RSFormProHelper::getConfig('payment.nodecimals');
		$decimal    = RSFormProHelper::getConfig('payment.decimal');
		$thousands  = RSFormProHelper::getConfig('payment.thousands');
		$currency   = RSFormProHelper::getConfig('payment.currency');
		$totalMask  = RSFormProHelper::getConfig('payment.totalmask');
		$totalMask  = RSFormProHelper::explode($totalMask);
		$totalMask  = implode('<br/>', $totalMask);

		$donations = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_DONATION);
		$single   = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_SINGLE_PRODUCT);
		$multiple = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS);
		$total 	  = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_TOTAL);
		$discount = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_DISCOUNT);
		$quantities = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_QUANTITY);
        $quantityAttachments = array();
		$choose_payment = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_CHOOSE);

		$allComponents = array();
		if ($donations) {
			$allComponents = array_merge($allComponents, $donations);
		}
		if ($single) {
			$allComponents = array_merge($allComponents, $single);
		}
		if ($multiple) {
			$allComponents = array_merge($allComponents, $multiple);
		}
		if ($total) {
			$allComponents = array_merge($allComponents, $total);
		}
		if ($discount) {
			$allComponents = array_merge($allComponents, $discount);
		}
		if ($quantities) {
			$allComponents = array_merge($allComponents, $quantities);
		}
		
		if ($choose_payment) {
			$allComponents = array_merge($allComponents, $choose_payment);
		}

		// no point going ahead if we have no fields added
		if (!$allComponents) {
			return;
		}

		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/payment.php';
		
		// available payment methods
		$payment_taxes = array();
		if ($payment_methods = RSFormProPaymentHelper::getPayments($formId))
		{
			foreach ($payment_methods as $method)
			{
				if (isset($method->tax))
				{
					$payment_taxes[$method->value] = $method->tax.($method->tax_type == 'percent' ? '%' : '');
				}
			}
		}

		$properties = RSFormProHelper::getComponentProperties($allComponents);

        if ($quantities)
		{
			foreach ($quantities as $componentId)
			{
                $quantityAttachments[$properties[$componentId]['PAYMENTFIELD']] = array('name' => $properties[$componentId]['NAME'], 'view_type' => $properties[$componentId]['VIEW_TYPE']);
			}
		}
		
		$script = '';

		if ($choose_payment && $properties[$choose_payment[0]]['SHOW'] == 'YES')
		{
			$script .= '
			RSFormProUtils.addEvent(window, \'load\', function() {
				var rsf_choose_payment_field = RSFormPro.getFieldsByName('.$formId.', "'.RSFormProHelper::htmlEscape($properties[$choose_payment[0]]['NAME']).'");
				if (rsf_choose_payment_field)
				{
					for(i = 0; i < rsf_choose_payment_field.length; i++) {
						if (rsf_choose_payment_field[i].nodeName == "SELECT" || rsf_choose_payment_field[i].nodeName == "INPUT")
						{
							RSFormProUtils.addEvent(rsf_choose_payment_field[i], "change", getPrice_'.$formId.');
						}
					}
				}
			});'."\n";
		}


		$script .= 'function getPrice_'.$formId.'() {'."\n";
		$script .= 'var total = 0;'."\n";
		$script .= 'var tax_value = 0;'."\n";
		
		if ($choose_payment && $properties[$choose_payment[0]]['SHOW'] == 'YES')
		{
			$script .= 'var choose_payment_field = RSFormPro.getFieldsByName('.$formId.', "'.RSFormProHelper::htmlEscape($properties[$choose_payment[0]]['NAME']).'");'."\n";
		}

		if (!empty($payment_taxes))
		{
			$script .= 'var taxes = '.json_encode($payment_taxes).';'."\n";
		}

		// Single product
		if ($single) {
			$data = $properties[$single[0]];

			$script .= "var singlePrice = parseFloat('".addslashes($data['PRICE'])."');\n";
            $script .= "var singlePriceQuantity = 1;\n";
			$script .= "if (!isNaN(singlePrice)) {\n";
            if (!empty($quantityAttachments[$single[0]]))
			{
                $quantity = $quantityAttachments[$single[0]];
                $script .= "var singlePriceQuantityField = RSFormPro.getFieldsByName('$formId', " . json_encode($quantity['name']) . ");\n";
                $script .= "singlePriceQuantity = parseFloat(singlePriceQuantityField[0].value);\n";
			}
			$script .= "total += (singlePriceQuantity * singlePrice);\n";
			$script .= "}\n";
		}

		// Donation fields
		if ($donations)
		{
		    foreach ($donations as $donation)
            {
	            $data = $properties[$donation];

	            $script .= "var donationField{$donation} = RSFormPro.getFieldsByName($formId, '".addslashes($data['NAME'])."');\n";
	            $script .= "if (donationField{$donation}) {\n";
	            $script .= "var donationPrice{$donation} = donationField{$donation}[0].value;\n";
                $script .= "if (donationField{$donation}[0].type == 'number') {\n";
	            $script .= "donationPrice{$donation} = parseFloat(donationPrice{$donation});";
                $script .= "} else {\n";
	            $script .= "while (donationPrice{$donation}.indexOf(".json_encode($thousands).") > -1) {\n";
	            $script .= "donationPrice{$donation} = donationPrice{$donation}.replace(".json_encode($thousands).", '');\n";
	            $script .= "}\n";
	            $script .= "donationPrice{$donation} = parseFloat(donationPrice{$donation}.replace(".json_encode($decimal).", '.'));";
	            $script .= "}\n";
	            $script .= "if (!isNaN(donationPrice{$donation})) {\n";
				$script .= "var donationQuantity = 1;\n";
				if (!empty($quantityAttachments[$donation]))
				{
					$quantity = $quantityAttachments[$donation];
					$script .= "var donationQuantityField = RSFormPro.getFieldsByName('$formId', " . json_encode($quantity['name']) . ");\n";
					$script .= "donationQuantity = parseFloat(donationQuantityField[0].value);\n";
				}
	            $script .= "total += (donationQuantity * donationPrice{$donation});\n";
	            $script .= "}\n";
	            $script .= "}\n";
            }
		}

		// Multiple products
		if ($multiple) {
			$script .= 'var products = {};'."\n";
			foreach ($multiple as $componentId) {
				$data = $properties[$componentId];

				$script .= 'products['.$componentId.'] = [];'."\n";
				if ($products = RSFormProPaymentHelper::getProducts($componentId))
				{
					foreach ($products as $item)
					{
						$val = $item['val'];

						$script .= "products[".$componentId."].push(parseFloat('".addslashes($val)."'));\n";
					}

					$script .= "var fields = RSFormPro.getFieldsByName($formId, '".addslashes($data['NAME'])."');\n";
                    $script .= "var multipleQuantity = 1;\n";
					if (!empty($quantityAttachments[$componentId]))
					{
						$quantity = $quantityAttachments[$componentId];
						$script .= "var multipleQuantityField = RSFormPro.getFieldsByName('$formId', " . json_encode($quantity['name']) . ");\n";
						$script .= "multipleQuantity = parseFloat(multipleQuantityField[0].value);\n";
					}
					if ($data['VIEW_TYPE'] == 'DROPDOWN') {
						$script .= "for (var i=0; i<fields[0].options.length; i++) {\n";
						$script .= "if (fields[0].options[i].selected && typeof products[".$componentId."][i] != 'undefined') {\n";
						$script .= "var price = products[".$componentId."][i];\n";
						$script .= "if (!isNaN(price)) {\n";
						$script .= "total += (multipleQuantity * price);\n";
						$script .= "}\n";
						$script .= "}\n";
						$script .= "}\n";
					} elseif ($data['VIEW_TYPE'] == 'CHECKBOX' || $data['VIEW_TYPE'] == 'RADIOGROUP') {
						$script .= "for (var i=0; i<fields.length; i=i+2) {\n";
						$script .= "if (fields[i].checked && typeof products[".$componentId."][i/2] != 'undefined') {\n";
						$script .= "var price = products[".$componentId."][i/2];\n";
						$script .= "if (!isNaN(price)) {\n";
						$script .= "total += (multipleQuantity * price);\n";
						$script .= "}\n";
						$script .= "}\n";
						$script .= "}\n";
					}
				}
			}
		}

		// Discount field
		if ($discount) {
			$data = $properties[$discount[0]];

			if ($codes = RSFormProHelper::isCode($data['COUPONS']))
			{
				$codes = RSFormProHelper::explode($codes);
				$discounts = array();
				foreach ($codes as $string)
				{
					if (strpos($string, '|') !== false)
					{
						list($value, $code) = explode('|', $string, 2);
						$discounts[md5($code)] = $value;
					}
				}

				$script .= "var discountField = RSFormPro.getFieldsByName($formId, '".addslashes($data['NAME'])."');\n";
				$script .= "if (discountField) {\n";
				$script .= "var codes = " . json_encode($discounts) . ";\n";
				$script .= "total = RSFormProPayment.applyCode(discountField, codes, total);\n";
				$script .= "}\n";
			}
		}

		// Format the price
		$script .= "var formattedTotal = RSFormPro.formatNumber(total, '".addslashes($nodecimals)."', '".addslashes($decimal)."', '".addslashes($thousands)."');\n";
		$script .= "var hiddenFormattedTotal = RSFormPro.formatNumber(total, 2, '.', '');\n";

		// Total field - populate it
		if ($total) {
			$data = $properties[$total[0]];
			
			$script .= "
			if (typeof choose_payment_field !== 'undefined' && typeof taxes !== 'undefined')
			{
				var payment_chosen = 0;
				for(i = 0; i < choose_payment_field.length; i++) {
					if (choose_payment_field[i].nodeName == 'SELECT' || (choose_payment_field[i].nodeName == 'INPUT' && choose_payment_field[i].checked))
					{
						var payment_chosen  = choose_payment_field[i].value;
					}
				}

				if (payment_chosen && typeof taxes[payment_chosen] !== 'undefined')
				{
					tax_value = taxes[payment_chosen];
				}
			}
			";
			if (!empty($payment_methods))
			{
				$script .= "
				else if(typeof taxes !== 'undefined')
				{
					tax_value = taxes['".RSFormProHelper::htmlEscape($payment_methods[0]->value)."'];
				}";
			}
			else
			{
				$script .= "else
				{
					tax_value = 0;
				}";
			}

			$script .= "
			if (tax_value != 0)
			{
				if (tax_value.indexOf('%') > -1)
				{
					var tax_value_raw = parseFloat(tax_value);
					tax_value = (total * tax_value_raw) / 100;
				}
				else
				{
					tax_value = parseFloat(tax_value);
				}
			}

			var grandTotal = total + tax_value;
			var formattedGrandTotal = number_format(grandTotal, '".addslashes($nodecimals)."', '".addslashes($decimal)."', '".addslashes($thousands)."');
			var formattedTax = number_format(tax_value, '".addslashes($nodecimals)."', '".addslashes($decimal)."', '".addslashes($thousands)."');
			";

			$script .= "var totalMask = '".addslashes($totalMask)."';\n";
			$script .= "totalMask = totalMask.replace('{price}', formattedTotal);\n";
			$script .= "totalMask = totalMask.replace(/\\{currency\\}/g, '".addslashes($currency)."');\n";
			$script .= "totalMask = totalMask.replace('{grandtotal}', formattedGrandTotal);\n";
			$script .= "totalMask = totalMask.replace('{tax}', formattedTax);\n";
			$script .= "document.getElementById('payment_total_".$formId."').innerHTML = totalMask;\n";
			$script .= "var hiddenFormattedField = RSFormPro.getFieldsByName($formId, '" . addslashes($data['NAME']) . "');\n";
			$script .= "hiddenFormattedField[0].value = hiddenFormattedTotal;\n";
		}

		$formLayout = str_replace('</form>', '<input type="hidden" name="form[rsfp_Total]" value="0" />'."\n".'</form>', $formLayout);

		$script .= "var field = RSFormPro.getFieldsByName($formId, 'rsfp_Total');\n";
        $script .= "field[0].value = hiddenFormattedTotal;\n";
        $script .= '}'."\n";

        $script .=
            "RSFormProUtils.addEvent(window, 'load', function() {\n" .
                "\tRSFormProUtils.addEvent(RSFormPro.getForm({$formId}), 'reset', function() {\n" .
                    "\t\twindow.setTimeout(getPrice_{$formId}, 10);\n" .
                "\t});\n" .
            "});\n";

        $script .= sprintf('window.addEventListener(\'DOMContentLoaded\', getPrice_%d);', $formId);

        RSFormProAssets::addScriptDeclaration($script);
	}

	public function onRsformFrontendBeforeStoreSubmissions($args)
	{
		if (!$this->_hasPaymentFields($args['formId']))
		{
			return false;
		}

		$args['post']['_STATUS'] = '0';
		$args['post']['_TRANSACTION_ID'] = '';

		if (isset($args['post']['rsfp_Total']) && $args['post']['rsfp_Total'] == '0.00')
		{
			$args['post']['_STATUS'] = '1';
		}
	}

	public function onRsformBackendBeforeGrid($self)
	{
		$formId = $self->formId;
		if ($this->_hasPaymentFields($formId))
		{
			$total 	  		= RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_TOTAL);
			$choose_payment = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_CHOOSE);

			if (!$total || !$choose_payment)
			{
				?>
				<div class="alert alert-error center text-center">
					<?php
					if (!$total)
					{
						echo '<p>' . JText::sprintf('PLG_SYSTEM_RSFPPAYMENT_FIELD_IS_MISSING', JText::_('RSFP_TOTAL')) . '</p>';
					}
					if (!$choose_payment)
					{
						echo '<p>' . JText::sprintf('PLG_SYSTEM_RSFPPAYMENT_FIELD_IS_MISSING', JText::_('RSFP_CHOOSE_PAYMENT')) . '</p>';
					}
					?>
					<p><?php echo JText::_('PLG_SYSTEM_RSFPPAYMENT_PLEASE_ADD_MISSING_FIELDS'); ?></p>
				</div>
				<?php
			}
		}
	}

	public function onRsformFrontendAfterFormProcess($args)
	{
		if (!$this->_hasPaymentFields($args['formId']))
		{
			return false;
		}

		$db = JFactory::getDbo();

		$products   = array();
		$donations  = RSFormProHelper::componentExists($args['formId'], RSFORM_FIELD_PAYMENT_DONATION);
		$single 	= RSFormProHelper::componentExists($args['formId'], RSFORM_FIELD_PAYMENT_SINGLE_PRODUCT);
		$multiple 	= RSFormProHelper::componentExists($args['formId'], RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS);
		$total 	  	= RSFormProHelper::componentExists($args['formId'], RSFORM_FIELD_PAYMENT_TOTAL);

		$choosePayment = RSFormProHelper::componentExists($args['formId'], RSFORM_FIELD_PAYMENT_CHOOSE);

		$allComponents = array();
		if ($donations)
		{
			$allComponents = array_merge($allComponents, $donations);
		}
		if ($single)
		{
			$allComponents = array_merge($allComponents, $single);
		}
		if ($multiple)
		{
			$allComponents = array_merge($allComponents, $multiple);
		}
		if ($total)
		{
			$allComponents = array_merge($allComponents, $total);
		}
		$properties = RSFormProHelper::getComponentProperties($allComponents);

		// PayPal, for legacy reasons
		if (defined('RSFORM_FIELD_PAYMENT_PAYPAL'))
		{
			$hasPayPal = RSFormProHelper::componentExists($args['formId'], RSFORM_FIELD_PAYMENT_PAYPAL);
		}
		else
		{
			$hasPayPal = false;
		}

		// Total price
		$price = $this->_getSubmissionValue($args['SubmissionId'], 'rsfp_Total');

		// Build products information
		// Single product
		if ($single)
		{
			$data = $properties[$single[0]];
			$products[] = strip_tags($data['CAPTION']);
		}

		// Multiple product
		if ($multiple)
		{
			foreach ($multiple as $componentId)
			{
				$data = $properties[$componentId];
				if ($bought = $this->_getSubmissionValue($args['SubmissionId'], $data['NAME']))
				{
					$products[] = strip_tags($data['CAPTION'].' - '.$bought);
				}
			}
		}

		// Donation products
		if ($donations)
		{
		    foreach ($donations as $donation)
            {
	            $data = $properties[$donation];
	            if ($donated = $this->_getSubmissionValue($args['SubmissionId'], $data['NAME']))
	            {
		            $products[] = strip_tags($data['CAPTION']);
	            }
            }
		}

		if (($choosePayment && ($payValue = $this->_getSubmissionValue($args['SubmissionId'], $choosePayment[0]))) || ($hasPayPal && !$choosePayment && $payValue = 'paypal'))
		{
			$query = $db->getQuery(true)
				->select($db->qn('DateSubmitted'))
				->from($db->qn('#__rsform_submissions'))
				->where($db->qn('SubmissionId') . ' = ' . $db->q($args['SubmissionId']));

			// Build verification code
			$code = md5($args['SubmissionId'].$db->setQuery($query)->loadResult());

			JFactory::getApplication()->triggerEvent('onRsformDoPayment', array($payValue, $args['formId'], $args['SubmissionId'], (float) $price, $products, $code));
		}
	}

	protected function _getComponentName($componentId) {
        if ($data = RSFormProHelper::getComponentProperties($componentId))
        {
            if (isset($data['NAME']))
            {
                return $data['NAME'];
            }
        }

        return false;
	}

	protected function _getSubmissionValue($submissionId, $componentId)
    {
        static $cache = array();

        if (is_numeric($componentId)) {
            $name = $this->_getComponentName($componentId);
        } else {
            $name = $componentId;
        }

        if (!isset($cache[$submissionId])) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->qn(array('FieldName', 'FieldValue')))
                ->from($db->qn('#__rsform_submission_values'))
                ->where($db->qn('SubmissionId') . ' = ' . $db->q($submissionId));
            $cache[$submissionId] = (array) $db->setQuery($query)->loadAssocList('FieldName');
        }

        // The _STATUS field can change, so don't cache it, grab a fresh version every time we need it.
        if ($name == '_STATUS')
        {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->qn('FieldValue'))
                ->from($db->qn('#__rsform_submission_values'))
                ->where($db->qn('SubmissionId') . ' = ' . $db->q($submissionId))
                ->where($db->qn('FieldName') . ' = ' . $db->q($name));
            $cache[$submissionId][$name]['FieldValue'] = $db->setQuery($query)->loadResult();
        }

        if (!empty($cache[$submissionId]) && isset($cache[$submissionId][$name])) {
            return $cache[$submissionId][$name]['FieldValue'];
        }

        return null;
	}

	protected function _hasPaymentFields($formId)
    {
        static $cache = array();

        if (!isset($cache[$formId]))
        {
            $cache[$formId] = RSFormProHelper::componentExists($formId, $this->newComponents);
        }

        return $cache[$formId];
    }

	protected function _getPaymentSettings($formId)
    {
        static $cache = array();

        if (!isset($cache[$formId]))
        {
			$row = $this->getTable();
			if (!$row->load($formId))
			{
				$row->setDefaultParams();
			}

			$cache[$formId] = $row->params;
        }

        return $cache[$formId];
    }

	private function loadFormData()
	{
		$data 	= array();
		$db 	= JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__rsform_config'))
			->where($db->qn('SettingName') . ' LIKE ' . $db->q('payment.%', false));
		if ($results = $db->setQuery($query)->loadObjectList())
		{
			foreach ($results as $result)
			{
				$data[$result->SettingName] = $result->SettingValue;
			}
		}

		return $data;
	}

	protected function configurationScreen()
	{
		ob_start();

		JForm::addFormPath(__DIR__ . '/forms');

		$form = JForm::getInstance( 'plg_system_rsfppayment.configuration', 'configuration', array('control' => 'rsformConfig'), false, false );
		$form->bind($this->loadFormData());

		?>
		<div id="page-payment" class="form-horizontal">
			<?php
			foreach ($form->getFieldsets() as $fieldset)
			{
				if ($fields = $form->getFieldset($fieldset->name))
				{
					foreach ($fields as $field)
					{
						echo $field->renderField();
					}
				}
			}
			?>
		</div>
		<?php

		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	protected function isOfflinePayment($args)
	{
		static $result;

		if ($result === null)
		{
			$result = false;

			if ($choosePayment = RSFormProHelper::componentExists($args['form']->FormId, RSFORM_FIELD_PAYMENT_CHOOSE))
			{
				$chooseData = RSFormProHelper::getComponentProperties($choosePayment[0]);
				$pos = array_search('{'.$chooseData['NAME'].':value}', $args['placeholders']);
				if ($pos !== false)
				{
					$payValue = $args['values'][$pos];

					if ($components = RSFormProHelper::componentExists($args['form']->FormId, RSFORM_FIELD_PAYMENT_OFFLINE))
					{
						$properties = RSFormProHelper::getComponentProperties($components);
						foreach ($properties as $componentId => $data)
						{
							if ($payValue == $data['LABEL'])
							{
								$result = true;
								break;
							}
						}
					}
				}
			}
		}

		return $result;
	}

	public function onRsformBeforeUserEmail($args) {
	    if (!$this->_hasPaymentFields($args['form']->FormId))
        {
            return;
        }

        if ($params = $this->_getPaymentSettings($args['form']->FormId))
        {
			if (!empty($params->DisableDeferOfflinePayment) && $this->isOfflinePayment($args))
			{
				return;
			}

			if (isset($params->UserEmail))
			{
                $status = $this->_getSubmissionValue($args['submissionId'], '_STATUS');
                $total = $this->_getSubmissionValue($args['submissionId'], 'rsfp_Total');

                // Don't defer anything if we don't have any payment
                if ($total == '0.00')
				{
					return;
				}

				// defer sending if
                // - user email is deferred && the payment is not confirmed (send email only when payment is confirmed)
                if ($params->UserEmail == 1 && $status == 0)
                {
                    $args['userEmail']['to'] = '';
                }

                // - user email is not deferred && the payment is confirmed (don't send the email once again, it has already been sent)
                if ($params->UserEmail == 0 && $status == 1)
                {
                    $args['userEmail']['to'] = '';
                }
			}
		}
	}

	public function onRsformBeforeAdminEmail($args) {
        if (!$this->_hasPaymentFields($args['form']->FormId))
        {
            return;
        }

        if ($params = $this->_getPaymentSettings($args['form']->FormId))
        {
			if (!empty($params->DisableDeferOfflinePayment) && $this->isOfflinePayment($args))
			{
				return;
			}

			if (isset($params->AdminEmail))
			{
                $status = $this->_getSubmissionValue($args['submissionId'], '_STATUS');
				$total = $this->_getSubmissionValue($args['submissionId'], 'rsfp_Total');

				// Don't defer anything if we don't have any payment
				if ($total == '0.00')
				{
					return;
				}

                // defer sending if
                // - admin email is deferred && the payment is not confirmed (send email only when payment is confirmed)
                if ($params->AdminEmail == 1 && $status == 0)
                {
                    $args['adminEmail']['to'] = '';
                }

                // - admin email is not deferred && the payment is confirmed (don't send the email once again, it has already been sent)
                if ($params->AdminEmail == 0 && $status == 1)
                {
                    $args['adminEmail']['to'] = '';
                }
			}
		}
	}

	public function onRsformBeforeAdditionalEmail($args) {
        if (!$this->_hasPaymentFields($args['form']->FormId))
        {
            return;
        }

        if ($params = $this->_getPaymentSettings($args['form']->FormId))
        {
			if (!empty($params->DisableDeferOfflinePayment) && $this->isOfflinePayment($args))
			{
				return;
			}

			if (isset($params->AdditionalEmails))
			{
                $status = $this->_getSubmissionValue($args['submissionId'], '_STATUS');
				$total = $this->_getSubmissionValue($args['submissionId'], 'rsfp_Total');

				// Don't defer anything if we don't have any payment
				if ($total == '0.00')
				{
					return;
				}

                // defer sending if
                // - admin email is deferred && the payment is not confirmed (send email only when payment is confirmed)
                if ($params->AdditionalEmails == 1 && $status == 0)
                {
                    $args['additionalEmail']['to'] = '';
                }

                // - admin email is not deferred && the payment is confirmed (don't send the email once again, it has already been sent)
                if ($params->AdditionalEmails == 0 && $status == 1)
                {
                    $args['additionalEmail']['to'] = '';
                }
			}
		}
	}

	private function translate(&$properties, $translations)
    {
        foreach ($properties as $componentId => $componentProperties)
        {
            foreach ($componentProperties as $property => $value)
            {
                $reference_id = $componentId.'.'.$property;
                if (isset($translations[$reference_id]))
                    $componentProperties[$property] = $translations[$reference_id];
            }
            $properties[$componentId] = $componentProperties;
        }
    }

	public function onRsformAfterCreatePlaceholders($args) {
		$formId 			= $args['form']->FormId;
		$submissionId 		= $args['submission']->SubmissionId;
		$multipleSeparator 	= $args['form']->MultipleSeparator;

		if ($this->_hasPaymentFields($formId))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/payment.php';

            // language
            $translations = RSFormProHelper::getTranslations('properties', $formId, $args['submission']->Lang);

			$singleProduct 		= RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_SINGLE_PRODUCT);
			$multipleProducts 	= RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS);
			$total				= RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_TOTAL);
			$donationProducts 	= RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_DONATION);
			$choosePayment		= RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_CHOOSE);
			$discountField		= RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_DISCOUNT);
            $quantities         = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_QUANTITY);
			$quantityAttachments = array();

			if ($quantities)
			{
                $properties = RSFormProHelper::getComponentProperties($quantities);
				foreach ($quantities as $componentId)
				{
					$quantityAttachments[$properties[$componentId]['PAYMENTFIELD']] = array('name' => $properties[$componentId]['NAME'], 'view_type' => $properties[$componentId]['VIEW_TYPE']);
				}
			}

			// choose payment
			if (!empty($choosePayment)) {
				$data 	    = RSFormProHelper::getComponentProperties($choosePayment[0]);
				$items 		= RSFormProPaymentHelper::getPayments($formId);
				$value 		= $this->_getSubmissionValue($submissionId, $choosePayment[0]);
				$text		= '';

				if ($items) {
					foreach ($items as $item) {
						if ($item->value == $value) {
							$text = $item->text;
							break;
						}
					}
				}

				$args['placeholders'][] = '{'.$data['NAME'].':text}';
				$args['values'][] 		= $text;
			}

			// multiple products
			if (!empty($multipleProducts))
			{
			    $properties = RSFormProHelper::getComponentProperties($multipleProducts, false);
                $this->translate($properties, $translations);

				foreach ($multipleProducts as $product)
				{
					$data  = $properties[$product];
					$value = $this->_getSubmissionValue($submissionId, $product);
					if ($value === null)
                    {
						$value = '';
                    }

					$value = explode("\n", $value);

					require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/fielditem.php';
					require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fieldmultiple.php';
					$f = new RSFormProFieldMultiple(array(
						'formId' 			=> $formId,
						'componentId' 		=> $product,
						'data' 				=> $data,
						'value' 			=> array('formId' => $formId, $data['NAME'] => $value),
						'invalid' 			=> false
					));

					$replace 	= '{'.$data['NAME'].':price}';
					$with 		= array();
					$withAmount = array();

					if ($items = $f->getItems())
					{
						foreach ($items as $item)
						{
							$item = new RSFormProFieldItem($item);

							if (in_array($item->label, $value))
							{
								$with[] = $this->_getPriceMask($item->label, $item->value);
								$withAmount[] = $item->value;
							}
						}
					}

					if (($position = array_search($replace, $args['placeholders'])) !== false)
					{
						$args['placeholders'][$position] = $replace;
						$args['values'][$position] 		 = implode($multipleSeparator, $with);
					}
					else
					{
						$args['placeholders'][] = $replace;
						$args['values'][] 		= implode($multipleSeparator, $with);
					}

					$args['placeholders'][] = '{'.$data['NAME'].':amount}';
					$args['values'][] = $withAmount ? array_sum($withAmount) : 0;

					$quantity = 1;
					if (!empty($quantityAttachments[$product]))
					{
						$quantity = $this->_getSubmissionValue($submissionId, $quantityAttachments[$product]['name']);
						$quantity = (float) $quantity;
					}
					$args['placeholders'][] = '{'.$data['NAME'].':totalprice}';
					$args['values'][] 		= $this->_getPriceMask($data['CAPTION'], ($quantity * (float) ($withAmount ? array_sum($withAmount) : 0)));
					$args['placeholders'][] = '{'.$data['NAME'].':totalamount}';
					$args['values'][] 		= $quantity * (float) ($withAmount ? array_sum($withAmount) : 0);
					$args['placeholders'][] = '{'.$data['NAME'].':quantity}';
					$args['values'][] 		= $quantity;
				}
			}

			// donation
			if (!empty($donationProducts))
			{
				$properties = RSFormProHelper::getComponentProperties($donationProducts, false);
				$this->translate($properties, $translations);

			    foreach ($donationProducts as $donationProduct)
                {
	                $price = $this->_getSubmissionValue($submissionId, $donationProduct);
	                $data = $properties[$donationProduct];

	                $args['placeholders'][] = '{'.$data['NAME'].':price}';
	                $args['values'][] 		= $this->_getPriceMask($data['CAPTION'], $price);
	                $args['placeholders'][] = '{'.$data['NAME'].':amount}';
	                $args['values'][] 		= $price ? $price : 0;

					$quantity = 1;
					if (!empty($quantityAttachments[$donationProduct]))
					{
						$quantity = $this->_getSubmissionValue($submissionId, $quantityAttachments[$donationProduct]['name']);
						$quantity = (float) $quantity;
					}
					$args['placeholders'][] = '{'.$data['NAME'].':totalprice}';
					$args['values'][] 		= $this->_getPriceMask($data['CAPTION'], ($quantity * (float) $price));
					$args['placeholders'][] = '{'.$data['NAME'].':totalamount}';
					$args['values'][] 		= $quantity * (float) $price;
					$args['placeholders'][] = '{'.$data['NAME'].':quantity}';
					$args['values'][] 		= $quantity;
                }
			}

			// single product
			if (!empty($singleProduct))
			{
			    $properties = RSFormProHelper::getComponentProperties($singleProduct, false);

			    $this->translate($properties, $translations);

				$data 	= $properties[$singleProduct[0]];
				$price 	= $data['PRICE'];

				$args['placeholders'][] = '{rsfp_Product:price}';
				$args['values'][] 		= $this->_getPriceMask($data['CAPTION'], $price);
				$args['placeholders'][] = '{rsfp_Product:amount}';
				$args['values'][] 		= $price;

				$quantity = 1;
				if (!empty($quantityAttachments[$singleProduct[0]]))
				{
					$quantity = $this->_getSubmissionValue($submissionId, $quantityAttachments[$singleProduct[0]]['name']);
					$quantity = (float) $quantity;
				}
				$args['placeholders'][] = '{rsfp_Product:totalprice}';
				$args['values'][] 		= $this->_getPriceMask($data['CAPTION'], ($quantity * (float) $price));
				$args['placeholders'][] = '{rsfp_Product:totalamount}';
				$args['values'][] 		= $quantity * (float) $price;
				$args['placeholders'][] = '{rsfp_Product:quantity}';
				$args['values'][] 		= $quantity;
			}

			if (!empty($total))
			{
				$price 		= $this->_getSubmissionValue($submissionId, $total[0]);
                $properties = RSFormProHelper::getComponentProperties($total, false);

                $this->translate($properties, $translations);

				$data 	= $properties[$total[0]];

				$args['placeholders'][] = '{'.$data['NAME'].':price}';
				$args['values'][] 		= $this->_getTotalMask($price, $formId, $submissionId);
				$args['placeholders'][] = '{'.$data['NAME'].':amount}';
				$args['values'][] 		= $price;
			}

			if (!empty($discountField))
			{
				$args['placeholders'][] = '{discount}';
				$args['placeholders'][] = '{discountprice}';

				$discount = '';
				$discountPrice = '';

				$data = RSFormProHelper::getComponentProperties($discountField[0], false);
				if ($codes = RSFormProHelper::isCode($data['COUPONS']))
				{
					$usedCode = $this->_getSubmissionValue($submissionId, $discountField[0]);
					$codes = RSFormProHelper::explode($codes);
					foreach ($codes as $string)
					{
						if (strpos($string, '|') === false)
						{
							continue;
						}

						list($value, $code) = explode('|', $string, 2);

						if ($code == $usedCode)
						{
							$discount = $calculatedDiscount = $value;
							if (strpos($value, '%') !== false)
							{
								$price = $this->_getSubmissionValue($submissionId, 'rsfp_Total');
								$value = (float) trim($value, '%');

								if (is_numeric($value))
								{
									if ($value != 100)
									{
										$calculatedDiscount = ($price / (100 - $value)) * $value;
									}
								}
							}

							$discountPrice = $this->number_format($calculatedDiscount);

							break;
						}
					}
				}

				$args['values'][] = $discount;
				$args['values'][] = $discountPrice;
			}

            $args['placeholders'][] = '{_STATUS:value}';
            $args['values'][]       = isset($args['submission']->values['_STATUS']) ? JText::_('RSFP_PAYMENT_STATUS_' . $args['submission']->values['_STATUS']) : '';

            $args['placeholders'][] = '{_STATUS:caption}';
            $args['values'][]       = JText::_('RSFP_PAYMENT_STATUS');

			$args['placeholders'][] = '{_TRANSACTION_ID:value}';
			$args['values'][]       = isset($args['submission']->values['_TRANSACTION_ID']) ? $args['submission']->values['_TRANSACTION_ID'] : '';

			$args['placeholders'][] = '{_TRANSACTION_ID:caption}';
			$args['values'][]       = JText::_('RSFP_PAYMENT_TRANSACTION_ID');
		}
	}

	public function onRsformBackendGetSubmissionHeaders(&$headers, $formId)
    {
        if ($this->_hasPaymentFields($formId))
        {
            $headers[] = '_STATUS';
            $headers[] = '_TRANSACTION_ID';
        }
    }

    public function onRsformBackendGetHeaderLabel(&$header, $formId)
    {
        if ($this->_hasPaymentFields($formId))
        {
            if ($header === '_STATUS')
            {
                $header = JText::_('RSFP_PAYMENT_STATUS');
            }
            elseif ($header === 'rsfp_Product')
            {
                $header = JText::_('RSFP_SPRODUCT');
            }
            elseif ($header === '_TRANSACTION_ID')
			{
				$header = JText::_('RSFP_PAYMENT_TRANSACTION_ID');
			}
        }
    }

    public function onRsformFrontendGetEditFields(&$return, $submission)
    {
        // This is for the frontend editing (Directory), we use mostly the same code
        if ($this->_hasPaymentFields($submission->FormId))
        {
        	// Choose Payment
			if (JFactory::getApplication()->input->get('format') != 'pdf' && ($componentId = RSFormProHelper::componentExists($submission->FormId, RSFORM_FIELD_PAYMENT_CHOOSE)) && isset($return[$componentId[0]]))
			{
				$return[$componentId[0]][1] = $this->generateChoosePayment($submission, RSFormProHelper::getComponentProperties($componentId[0]));
			}

			// Multiple Products
			if (JFactory::getApplication()->input->get('format') != 'pdf')
			{
				if ($componentIds = RSFormProHelper::componentExists($submission->FormId, RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS))
				{
					foreach ($componentIds as $componentId)
					{
						if (isset($return[$componentId]))
						{
							$return[$componentId][1] = $this->generateMultipleProducts($submission, RSFormProHelper::getComponentProperties($componentId, false));
						}
					}
				}
			}

            // Payment Status
            if (isset($submission->values['_STATUS']) && isset($return[-RSFORM_FIELD_PAYMENT_CHOOSE]))
            {
                $return[-RSFORM_FIELD_PAYMENT_CHOOSE][0] = JText::_('RSFP_PAYMENT_STATUS');

                $name   = '_STATUS';
                $value  = $submission->values['_STATUS'];

                $options = array(
                    JHtml::_('select.option', -1, JText::_('RSFP_PAYMENT_STATUS_-1')),
                    JHtml::_('select.option', 0, JText::_('RSFP_PAYMENT_STATUS_0')),
                    JHtml::_('select.option', 1, JText::_('RSFP_PAYMENT_STATUS_1'))
                );
                $return[-RSFORM_FIELD_PAYMENT_CHOOSE][1] = JHtml::_('select.genericlist', $options, 'form['.$name.'][]', null, 'value', 'text', $value);
            }

			// Transaction ID
			if (isset($submission->values['_TRANSACTION_ID']) && isset($return[-RSFORM_FIELD_PAYMENT_TOTAL]))
			{
				$return[-RSFORM_FIELD_PAYMENT_TOTAL][0] = JText::_('RSFP_PAYMENT_TRANSACTION_ID');

				$name   = '_TRANSACTION_ID';
				$value  = $submission->values['_TRANSACTION_ID'];

				$return[-RSFORM_FIELD_PAYMENT_TOTAL][1] = '<input class="rs_inp rs_80" type="text" name="form['.$name.']" value="' . RSFormProHelper::htmlEscape($value) . '" />';
			}
        }
    }

	public function onRsformBackendSwitchTasks()
	{
		$app = JFactory::getApplication();

		if ($app->input->getString('plugin_task') == 'payment.confirm')
		{
			try
			{
				$id = $app->input->getInt('id');

				require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';
				$submission = RSFormProSubmissionsHelper::getSubmission($id);

				if (!$submission)
				{
					throw new Exception(JText::_('RSFP_PAYMENT_CANNOT_CONFIRM_A_MISSING_SUBMISSION'));
				}

				// Dynamic field - update value.
				$object = (object) array(
					'FormId' 		=> $submission->FormId,
					'SubmissionId' 	=> $submission->SubmissionId,
					'FieldName'		=> '_STATUS',
					'FieldValue'	=> 1
				);

				// Update only if we've changed something
				JFactory::getDbo()->updateObject('#__rsform_submission_values', $object, array('SubmissionId', 'FormId', 'FieldName'));

				$app->triggerEvent('onRsformAfterConfirmPayment', array($submission->SubmissionId));

				$app->enqueueMessage(JText::_('RSFP_PAYMENT_CONFIRMED'));
				$app->redirect('index.php?option=com_rsform&view=submissions&layout=edit&cid=' . $submission->SubmissionId);
			}
			catch (Exception $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
				$app->redirect('index.php?option=com_rsform&view=submissions');
			}
		}
	}

    public function onRsformBackendGetEditFields(&$return, $submission)
    {
        if ($this->_hasPaymentFields($submission->FormId))
        {
	        $isPDF  = JFactory::getApplication()->input->get('format') == 'pdf';

	        if (!$isPDF)
            {
	            // Choose Payment
	            if ($componentId = RSFormProHelper::componentExists($submission->FormId, RSFORM_FIELD_PAYMENT_CHOOSE))
	            {
		            $data = RSFormProHelper::getComponentProperties($componentId[0]);
		            foreach ($return as $k => $field)
		            {
			            if (isset($field[3]) && $field[3] == $data['NAME'])
			            {
				            $return[$k][1] = $this->generateChoosePayment($submission, $data);
				            break;
			            }
		            }
	            }

	            // Multiple Products
	            if ($componentIds = RSFormProHelper::componentExists($submission->FormId, RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS))
	            {
		            $all_data = RSFormProHelper::getComponentProperties($componentIds, false);

		            foreach ($return as $k => $field)
		            {
			            foreach ($all_data as $componentId => $data)
			            {
				            if (isset($field[3]) && $field[3] == $data['NAME'])
				            {
					            $return[$k][1] = $this->generateMultipleProducts($submission, $data);
				            }
			            }
		            }
	            }
            }

            // Payment Status
            if (isset($submission->values['_STATUS']))
            {
                $name   = '_STATUS';
                $value  = $submission->values['_STATUS'];

                $new_field[0] = JText::_('RSFP_PAYMENT_STATUS');

                if ($isPDF)
                {
                    $new_field[1] = JText::_('RSFP_PAYMENT_STATUS_' . $value);
                }
                else
                {
                    $options = array(
                        JHtml::_('select.option', -1, JText::_('RSFP_PAYMENT_STATUS_-1')),
                        JHtml::_('select.option', 0, JText::_('RSFP_PAYMENT_STATUS_0')),
                        JHtml::_('select.option', 1, JText::_('RSFP_PAYMENT_STATUS_1'))
                    );
                    $new_field[1] = JHtml::_('select.genericlist', $options, 'form['.$name.'][]', null, 'value', 'text', $value);

	                JText::script('RSFP_PAYMENT_SURE_CONFIRM');

                    $new_field[1] .= '<br><a onclick="return confirm(Joomla.JText._(\'RSFP_PAYMENT_SURE_CONFIRM\'))" href="' . JRoute::_('index.php?option=com_rsform&task=plugin&plugin_task=payment.confirm&id=' . $submission->SubmissionId) . '" class="btn btn-primary">' . JText::_('RSFP_PAYMENT_CONFIRM') . '</a><br><small>' . JText::_('RSFP_PAYMENT_CONFIRM_DESC') . '</small>';
                }

                $return[] = $new_field;
            }

            // Transaction ID
			if (isset($submission->values['_TRANSACTION_ID']))
			{
				$name   = '_TRANSACTION_ID';
				$value  = $submission->values['_TRANSACTION_ID'];

				$new_field[0] = JText::_('RSFP_PAYMENT_TRANSACTION_ID');

				if ($isPDF)
				{
					$new_field[1] = $value;
				}
				else
				{
					$new_field[1] = '<input class="rs_inp rs_80" type="text" name="form['.$name.']" value="' . RSFormProHelper::htmlEscape($value) . '" />';
				}

				$return[] = $new_field;
			}
        }
    }

	private function generateMultipleProducts($submission, $data)
	{
		// Translate in submission language so options match
		if ($translations = RSFormProHelper::getTranslations('properties', $submission->FormId, $submission->Lang))
		{
			foreach ($data as $property => $value)
			{
				$reference_id = $data['componentId'] . '.' . $property;
				if (isset($translations[$reference_id]))
				{
					$data[$property] = $translations[$reference_id];
				}
			}
		}

		$name  = $data['NAME'];
		$value = isset($submission->values[$name]) ? $submission->values[$name] : '';
		$value = RSFormProHelper::explode($value);

		if ($data['VIEW_TYPE'] == 'CHECKBOX')
		{
			$data['MULTIPLE'] = 'YES';
			$data['SIZE'] = 5;
		}
		elseif ($data['VIEW_TYPE'] == 'RADIOGROUP')
		{
			$data['MULTIPLE'] = 'NO';
		}

		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/payment.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/fielditem.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fieldmultiple.php';
		$field = new RSFormProFieldMultiple(array(
			'formId' 			=> $submission->FormId,
			'componentId' 		=> $data['componentId'],
			'data' 				=> $data,
			'value' 			=> array('formId' => $submission->FormId, $data['NAME'] => $value),
			'invalid' 			=> array()
		));

		$options = array();
		if ($items = $field->getItems())
		{
			$special = array('[c]', '[g]', '[/g]','[d]');

			foreach ($items as $item)
			{
				$strippedItem = str_replace($special, '', $item);
				$hasGroup 	  = strpos($item, '[g]') !== false || strpos($item, '[/g]') !== false;

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
				}
				else
				{
					$tmpItem = $item;
				}

				$item = new RSFormProFieldItem($tmpItem);

				if ($item->flags['optgroup']) {
					$options[] = JHtml::_('select.option', '<OPTGROUP>', $item->label, 'value', 'text');
				} elseif ($item->flags['/optgroup']) {
					$options[] = JHtml::_('select.option', '</OPTGROUP>', $item->label, 'value', 'text');
				} else {
					$options[] = JHtml::_('select.option', $item->value, $item->label, 'value', 'text', $item->flags['disabled']);
				}
			}
		}

		$attribs = array();
		if (!empty($data['SIZE']) && (int) $data['SIZE'] > 0)
		{
			$attribs[] = 'size="'.(int) $data['SIZE'].'"';
		}

		if ($data['MULTIPLE'] == 'YES')
		{
			$attribs[] = 'multiple="multiple"';
		}

		$attribs = implode(' ', $attribs);

		return JHtml::_('select.genericlist', $options, 'form['.$name.'][]', $attribs, 'value', 'text', $value);
	}

	private function generateChoosePayment($submission, $data)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/payment.php';

		$name	= $data['NAME'];
		$value 	= isset($submission->values[$name]) ? $submission->values[$name] : '';

		$data['ITEMS'] = array();
		if ($items = RSFormProPaymentHelper::getPayments($submission->FormId))
		{
			foreach ($items as $item)
			{
				$data['ITEMS'][] = $item->value . '|' . $item->text;
			}
		}
		$data['ITEMS'] = implode("\n", $data['ITEMS']);

		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/fielditem.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fieldmultiple.php';
		$field = new RSFormProFieldMultiple(array(
			'formId' 			=> $submission->FormId,
			'componentId' 		=> $data['componentId'],
			'data' 				=> $data,
			'value' 			=> array('formId' => $submission->FormId, $data['NAME'] => $value),
			'invalid' 			=> array()
		));

		$options = array();
		if ($items = $field->getItems())
		{
			foreach ($items as $item)
			{
				$item = new RSFormProFieldItem($item);

				$options[] = JHtml::_('select.option', $item->value, $item->label, 'value', 'text', $item->flags['disabled']);
			}
		}

		return JHtml::_('select.genericlist', $options, 'form['.$name.'][]', null, 'value', 'text', $value);
	}

    public function onRsformBackendManageSubmissions($args)
    {
        if ($this->_hasPaymentFields($args['formId']))
        {
            foreach ($args['submissions'] as $SubmissionId => $submission)
            {
                if (isset($submission['SubmissionValues']['_STATUS']['Value']))
                {
                    $args['submissions'][$SubmissionId]['SubmissionValues']['_STATUS']['Value'] = JText::_('RSFP_PAYMENT_STATUS_' . $submission['SubmissionValues']['_STATUS']['Value']);
                }
            }
        }
    }

    public function onRsformBackendGetAllDirectoryFields(&$cache, $formId)
    {
        if ($this->_hasPaymentFields($formId))
        {
            $cache[-RSFORM_FIELD_PAYMENT_CHOOSE] = (object) array(
                'FieldName' 	=> '_STATUS',
                'FieldId'		=> -RSFORM_FIELD_PAYMENT_CHOOSE,
                'FieldType' 	=> 0,
                'FieldCaption' 	=> JText::_('RSFP_PAYMENT_STATUS')
            );

			$cache[-RSFORM_FIELD_PAYMENT_TOTAL] = (object) array(
				'FieldName' 	=> '_TRANSACTION_ID',
				'FieldId'		=> -RSFORM_FIELD_PAYMENT_TOTAL,
				'FieldType' 	=> 0,
				'FieldCaption' 	=> JText::_('RSFP_PAYMENT_TRANSACTION_ID')
			);
        }
    }

    public function onRsformAfterManageDirectoriesQuery(&$items, $formId)
    {
        if ($this->_hasPaymentFields($formId))
        {
            foreach ($items as $item)
            {
                if (isset($item->{'_STATUS'}))
                {
                    $item->{'_STATUS'} =  JText::_('RSFP_PAYMENT_STATUS_' . $item->{'_STATUS'});
                }
            }
        }
    }

    public function onRsformFrontendDownloadCSV(&$submissions, $formId)
    {
        if ($this->_hasPaymentFields($formId))
        {
            foreach ($submissions as $item)
            {
                if (isset($item->values['_STATUS']))
                {
                    $item->values['_STATUS'] =  JText::_('RSFP_PAYMENT_STATUS_' . $item->values['_STATUS']);
                }
            }
        }
    }

	private function _getPriceMask($txt, $val) {
		static $init, $nodecimals, $decimal, $thousands, $currency, $mask;
		if (!$init) {
			$init = true;

			$nodecimals = RSFormProHelper::getConfig('payment.nodecimals');
			$decimal    = RSFormProHelper::getConfig('payment.decimal');
			$thousands  = RSFormProHelper::getConfig('payment.thousands');
			$currency   = RSFormProHelper::getConfig('payment.currency');

			$mask = RSFormProHelper::getConfig('payment.mask');
		}

		$formattedPrice = number_format((float) $val, $nodecimals, $decimal, $thousands);
		$replacements   = array(
			'{product}' 	=> $txt,
			'{price}' 		=> $formattedPrice,
			'{currency}' 	=> $currency,
		);

		return str_replace(array_keys($replacements), array_values($replacements), $mask);
	}

	private function _getTotalMask($val, $formId, $submissionId)
	{
		$nodecimals = RSFormProHelper::getConfig('payment.nodecimals');
		$decimal    = RSFormProHelper::getConfig('payment.decimal');
		$thousands  = RSFormProHelper::getConfig('payment.thousands');
		$currency   = RSFormProHelper::getConfig('payment.currency');
		$mask  		= RSFormProHelper::getConfig('payment.totalmask');
		$mask  		= RSFormProHelper::explode($mask);
		$mask  		= implode('<br/>', $mask);

		$formattedPrice = number_format((float) $val, $nodecimals, $decimal, $thousands);

		// Some defaults
		$tax = number_format(0, $nodecimals, $decimal, $thousands);
		$grandTotal = $formattedPrice;

		if ($choose	= RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_CHOOSE))
		{
			$chooseData = RSFormProHelper::getComponentProperties($choose[0]);
			$chosenPayment = $this->_getSubmissionValue($submissionId, $chooseData['NAME']);

			if ($payments = RSFormProPaymentHelper::getPayments($formId))
			{
				foreach ($payments as $method)
				{
					if (isset($method->tax) && $method->value == $chosenPayment)
					{
						if ($method->tax_type === 'percent')
						{
							$rawTax = ((float) $val * $method->tax) / 100;
							$rawGrandTotal = (float) $val + $rawTax;
						}
						else
						{
							$rawTax = $method->tax;
							$rawGrandTotal = (float) $val + (float) $rawTax;
						}

						$tax = number_format($rawTax, $nodecimals, $decimal, $thousands);
						$grandTotal = number_format($rawGrandTotal, $nodecimals, $decimal, $thousands);

						break;
					}
				}
			}
		}

		$replacements   = array(
			'{price}' 		=> $formattedPrice,
			'{currency}' 	=> $currency,
			'{grandtotal}'	=> $grandTotal,
			'{tax}'			=> $tax
		);

		return str_replace(array_keys($replacements), array_values($replacements), $mask);
	}

	private function number_format($val)
	{
		return number_format((float) $val, RSFormProHelper::getConfig('payment.nodecimals'), RSFormProHelper::getConfig('payment.decimal'), RSFormProHelper::getConfig('payment.thousands'));
	}

	public function onRsformFormDelete($formId)
	{
		if ($row = $this->getTable())
		{
			$row->delete($formId);
		}
	}

	public function onRsformFormBackup($form, $xml, $fields)
	{
		if ($row = $this->getTable())
		{
			if ($row->load($form->FormId))
			{
				// This converts $params to serialized string
				$row->check();

				$data = $row->getProperties();
				unset($data['form_id']);

				$xml->add('payment');
				foreach ($data as $property => $value)
				{
					$xml->add($property, $value);
				}
				$xml->add('/payment');
			}

            if ($quantities = RSFormProHelper::componentExists($form->FormId, RSFORM_FIELD_PAYMENT_QUANTITY))
            {
                $properties = RSFormProHelper::getComponentProperties($quantities);
                foreach ($properties as $quantityId => $data)
				{
                    $paymentFieldData = RSFormProHelper::getComponentProperties($data['PAYMENTFIELD']);
                    $xml->replaceLine('<PAYMENTFIELD>' . $data['PAYMENTFIELD'] . '</PAYMENTFIELD>', '<PAYMENTFIELD>' . $paymentFieldData['NAME'] . '</PAYMENTFIELD>');
				}
			}
		}
	}

	public function onRsformFormRestore($form, $xml, $fields)
	{
		if (isset($xml->payment))
		{
			$data = array(
				'form_id' => $form->FormId
			);
			foreach ($xml->payment->children() as $property => $value)
			{
				$data[$property] = (string) $value;
			}

			$row = $this->getTable();
			$row->save($data);
		}

		if ($quantities = RSFormProHelper::componentExists($form->FormId, RSFORM_FIELD_PAYMENT_QUANTITY))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__rsform_properties'))
				->where($db->qn('ComponentId') . ' IN (' . implode(',', $db->q($quantities)) . ')')
				->where($db->qn('PropertyName') . ' = ' . $db->q('PAYMENTFIELD'));
            if ($results = $db->setQuery($query)->loadObjectList())
			{
                foreach ($results as $result)
				{
                    $componentId = RSFormProHelper::getComponentId($result->PropertyValue, $form->FormId);

                    $query->clear()
                        ->update('#__rsform_properties')
                        ->set($db->qn('PropertyValue') . ' = ' . $db->q($componentId))
                        ->where($db->qn('PropertyId') . ' = ' . $db->q($result->PropertyId));
                    $db->setQuery($query)->execute();
				}
			}
		}
	}

	public function onRsformBackendFormRestoreTruncate() {
		JFactory::getDbo()->truncateTable('#__rsform_payment');
	}

	public function onContentPrepareForm($form, $data)
	{
		if (!($form instanceof JForm))
		{
			return false;
		}

		$name  = $form->getName();
		$forms = array('com_menus.item' => 'menu');

		if (!isset($forms[$name]))
		{
			return true;
		}

		if ($forms[$name] == 'menu' && isset($data->request, $data->request['option'], $data->request['view']) && $data->request['option'] == 'com_rsform' && $data->request['view'] == 'directory')
		{
			JForm::addFormPath(__DIR__);
			$form->loadFile($forms[$name], false);
		}

		return true;
	}


	/**
	 * @param $query JDatabaseQuery
	 * @param $formId int
	 * @throws Exception
	 */
	public function onRsformAfterManageDirectoriesQueryCreated(&$query, $formId)
	{
		if ($query instanceof JDatabaseQuery // $query must be an object
			&& JFactory::getApplication()->getParams('com_rsform')->get('show_only_accepted_payments') // this is enabled
			&& $this->_hasPaymentFields($formId) // has any payment fields for this to make sense
		)
		{
			$db	= JFactory::getDbo();
			$query->select('GROUP_CONCAT(IF(' . $db->qn('sv.FieldName') . '=' . $db->q('_STATUS') . ', ' . $db->qn('sv.FieldValue') . ', NULL)) AS ' . $db->qn('_PLUGIN_PAYMENT_STATUS'));
			$query->having($db->qn('_PLUGIN_PAYMENT_STATUS') . ' = ' . $db->q(1), 'OR');
		}
	}

    public function onRsformDefineTotalFields(&$types)
    {
        $types[] = RSFORM_FIELD_PAYMENT_DONATION;
        $types[] = RSFORM_FIELD_PAYMENT_QUANTITY;
    }

	public function onRsformFrontendInitFormDisplay($args)
	{
		if ($componentIds = RSFormProHelper::componentExists($args['formId'], RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS))
		{
			$all_data = RSFormProHelper::getComponentProperties($componentIds);

			if ($all_data)
			{
				foreach ($all_data as $componentId => $data)
				{
					if ($data['VIEW_TYPE'] === 'DROPDOWN')
					{
						$args['formLayout'] = preg_replace('/<label (.*?) for="' . preg_quote($data['NAME'], '/') .'"/', '<label $1 for="payment-' . $componentId . '"', $args['formLayout']);
					}
				}
			}
		}
	}

	public function onRsformDefineCheckboxes(&$checkboxes, $formId)
	{
		if ($componentIds = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS))
		{
			$all_data = RSFormProHelper::getComponentProperties($componentIds);

			if ($all_data)
			{
				foreach ($all_data as $componentId => $data)
				{
					if ($data && $data['VIEW_TYPE'] === 'CHECKBOX')
					{
						$checkboxes[] = $componentId;
					}
				}
			}
		}
	}

	public function onRsformDefineRadiogroups(&$radiogroups, $formId)
	{
		if ($componentIds = RSFormProHelper::componentExists($formId, array(RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS, RSFORM_FIELD_PAYMENT_CHOOSE)))
		{
			$all_data = RSFormProHelper::getComponentProperties($componentIds);

			if ($all_data)
			{
				foreach ($all_data as $componentId => $data)
				{
					if ($data && $data['VIEW_TYPE'] === 'RADIOGROUP')
					{
						$radiogroups[] = $componentId;
					}
				}
			}
		}
	}

	public function onRsformBeforeSilentPost($SubmissionId, $formId, &$silentPost)
	{
		if (!$this->_hasPaymentFields($formId))
		{
			return false;
		}

		if ($params = $this->_getPaymentSettings($formId))
		{
			if (!empty($params->SilentPost))
			{
				$silentPost = false;
			}
		}
	}

	public function onRsformBeforeMappings($SubmissionId, $formId, &$mappings)
	{
		if (!$this->_hasPaymentFields($formId))
		{
			return false;
		}

		if ($params = $this->_getPaymentSettings($formId))
		{
			if (!empty($params->Mappings))
			{
				$mappings = false;
			}
		}
	}

	public function onRsformFrontendBeforeFormValidation($args)
	{
		$formId = $args['formId'];
		$post   = &$args['post'];
		if (!empty($args['invalid']) || !$this->_hasPaymentFields($formId))
		{
			return false;
		}

		if ($totalField = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_TOTAL))
		{
			$totalData = RSFormProHelper::getComponentProperties($totalField[0]);
			if (!empty($totalData['VALIDATETOTAL']) && $totalData['VALIDATETOTAL'] === 'YES')
			{
				$donations = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_DONATION);
				$single   = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_SINGLE_PRODUCT);
				$multiple = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS);
				$discount = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_DISCOUNT);
                $quantities = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAYMENT_QUANTITY);
				$quantityAttachments = array();

				$total = 0;

				$allComponents = array();
				if ($donations)
				{
					$allComponents = array_merge($allComponents, $donations);
				}
				if ($single)
				{
					$allComponents = array_merge($allComponents, $single);
				}
				if ($multiple)
				{
					$allComponents = array_merge($allComponents, $multiple);
				}
				if ($discount)
				{
					$allComponents = array_merge($allComponents, $discount);
				}
                if ($quantities)
				{
                    $allComponents = array_merge($allComponents, $quantities);
				}

				$properties = RSFormProHelper::getComponentProperties($allComponents);

				if ($quantities)
				{
					foreach ($quantities as $componentId)
					{
						$quantityAttachments[$properties[$componentId]['PAYMENTFIELD']] = array('name' => $properties[$componentId]['NAME'], 'view_type' => $properties[$componentId]['VIEW_TYPE']);
					}
				}

				if ($single)
				{
					$data = $properties[$single[0]];

                    $quantity = 1;
					if (!empty($quantityAttachments[$single[0]]))
					{
                        $quantity = isset($post[$quantityAttachments[$single[0]]['name']]) ? $post[$quantityAttachments[$single[0]]['name']] : 1;
                        if (is_array($quantity))
						{
                            $quantity = implode('', $quantity);
						}
                        $quantity = (float) $quantity;
					}

					$total += ($quantity * (float) $data['PRICE']);
				}

				if (!empty($multiple))
				{
					foreach ($multiple as $product)
					{
						$data = $properties[$product];
						if (isset($post[$data['NAME']]))
						{
							$value = (array) $post[$data['NAME']];

							require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/fielditem.php';
							require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fieldmultiple.php';
							$f = new RSFormProFieldMultiple(array(
								'formId' 			=> $formId,
								'componentId' 		=> $product,
								'data' 				=> $data,
								'value' 			=> array('formId' => $formId, $data['NAME'] => $value),
								'invalid' 			=> false
							));

							if ($items = $f->getItems())
							{
								$quantity = 1;
								if (!empty($quantityAttachments[$product]))
								{
									$quantity = isset($post[$quantityAttachments[$product]['name']]) ? $post[$quantityAttachments[$product]['name']] : 1;
									if (is_array($quantity))
									{
										$quantity = implode('', $quantity);
									}
									$quantity = (float) $quantity;
								}

								foreach ($items as $item)
								{
									$item = new RSFormProFieldItem($item);

									if (in_array($item->label, $value))
									{
										$total += ($quantity * (float) $item->value);
									}
								}
							}
						}
					}
				}

				if ($donations)
				{
					$thousands = RSFormProHelper::getConfig('payment.thousands');
					$decimal   = RSFormProHelper::getConfig('payment.decimal');
					foreach ($donations as $donation)
					{
						$data = $properties[$donation];
						if (isset($post[$data['NAME']]))
						{
							$quantity = 1;
							if (!empty($quantityAttachments[$donation]))
							{
								$quantity = isset($post[$quantityAttachments[$donation]['name']]) ? $post[$quantityAttachments[$donation]['name']] : 1;
								if (is_array($quantity))
								{
									$quantity = implode('', $quantity);
								}
								$quantity = (float) $quantity;
							}

							$donationTotal = str_replace(array($thousands, $decimal), array('', '.'), $post[$data['NAME']]);

							$total += ($quantity * (float) $donationTotal);
						}
					}
				}

				// Discount field
				if ($discount)
				{
					$data = $properties[$discount[0]];

					if (isset($post[$data['NAME']]))
					{
						if ($codes = RSFormProHelper::isCode($data['COUPONS']))
						{
							$foundDiscount = false;
							$codes = RSFormProHelper::explode($codes);
							foreach ($codes as $string)
							{
								if (strpos($string, '|') !== false)
								{
									list($value, $code) = explode('|', $string, 2);

									if ($code === $post[$data['NAME']])
									{
										$foundDiscount = $value;
									}
								}
							}

							if ($foundDiscount !== false)
							{
								if (strpos($foundDiscount, '%') !== false)
								{
									$foundDiscount = str_replace('%', '', $foundDiscount);
									$total = ((100 - $foundDiscount) / 100) * $total;
								}
								else
								{
									$total -= (float) $foundDiscount;
								}
							}
						}
					}
				}

				if ($total < 0)
				{
					$total = 0;
				}

				$post[$totalData['NAME']] = number_format($total, 2, '.', '');
				$post['rsfp_Total'] = $total;
			}
		}
	}

    public static function getOtherPaymentFields()
	{
		$list 	= array();
		$formId = JFactory::getApplication()->input->getInt('formId');
		$fields = RSFormProHelper::componentExists($formId, array(RSFORM_FIELD_PAYMENT_SINGLE_PRODUCT, RSFORM_FIELD_PAYMENT_MULTIPLE_PRODUCTS, RSFORM_FIELD_PAYMENT_DONATION));

		$list[] = array(
			'value' => '',
			'text' => 'PLEASE_SELECT_PAYMENT_FIELD'
		);

        if ($fields)
		{
            $all_data = RSFormProHelper::getComponentProperties($fields);
            foreach ($all_data as $componentId => $data)
			{
                $list[] = array(
                    'value' => $componentId,
                    'text'  => $data['NAME']
                );
			}
		}

		return RSFormProHelper::createList($list);
	}

    public function onRsformBackendValidateName($name, $componentType, $formId, $currentComponentId)
	{
        if ($componentType == RSFORM_FIELD_PAYMENT_QUANTITY)
		{
            $param = JFactory::getApplication()->input->get('param');
            if (empty($param['PAYMENTFIELD']))
			{
                throw new Exception(JText::_('PLG_SYSTEM_RSFPPAYMENT_PLEASE_SELECT_A_PAYMENT_FIELD'), 2);
			}

			$fields = RSFormProHelper::componentExists($formId, array(RSFORM_FIELD_PAYMENT_QUANTITY));
            if ($currentComponentId > 0)
			{
				unset($fields[array_search($currentComponentId, $fields)]);
			}

			if ($fields)
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select('PropertyValue')
					->from($db->qn('#__rsform_properties'))
					->where($db->qn('ComponentId') . ' IN (' . implode(',', $db->q($fields)) . ')')
					->where($db->qn('PropertyName') . ' = ' . $db->q('PAYMENTFIELD'));

				$results = $db->setQuery($query)->loadColumn();

                if (in_array($param['PAYMENTFIELD'], $results))
                {
					throw new Exception(JText::_('PLG_SYSTEM_RSFPPAYMENT_PLEASE_SELECT_A_DIFFERENT_PAYMENT_FIELD_ALREADY_ASSIGNED'), 2);
				}
			}
		}
	}
}